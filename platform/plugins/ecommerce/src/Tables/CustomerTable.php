<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\CustomerInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Html;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class CustomerTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;

    public function __construct(DataTables $table, UrlGenerator $urlGenerator, CustomerInterface $customerRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $customerRepository;

        if (! Auth::user()->hasAnyPermission(['customers.edit', 'customers.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('avatar', function ($item) {
                if ($this->request()->input('action') == 'excel' ||
                    $this->request()->input('action') == 'csv') {
                    return $item->avatar_url;
                }

                return Html::tag('img', '', ['src' => $item->avatar_url, 'alt' => BaseHelper::clean($item->name), 'width' => 50]);
            })
            ->editColumn('name', function ($item) {
                if (! Auth::user()->hasPermission('customers.edit')) {
                    return BaseHelper::clean($item->name);
                }

                return Html::link(route('customers.edit', $item->id), BaseHelper::clean($item->name));
            })
            ->editColumn('email', function ($item) {
                return BaseHelper::clean($item->email);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at,'d/m/Y');
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            });

        if (EcommerceHelper::isEnableEmailVerification()) {
            $data = $data
                ->addColumn('confirmed_at', function ($item) {
                    return $item->confirmed_at ? Html::tag(
                        'span',
                        trans('core/base::base.yes'),
                        ['class' => 'text-success']
                    ) : trans('core/base::base.no');
                });
        }

        $data = $data
            ->addColumn('operations', function ($item) {
                return $this->getOperations('customers.edit', 'customers.destroy', $item,'<button class="btn btn-primary btn-outline resetUserPass"><i class="fas fa-sync"></i></button>&nbsp <button class="btn btn-primary btn-outline welcomeMail"><i class="fas fa-sign-in"></i></button> ');
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()->select([
            'id',
            'codice',
            'name',
            'email',
            'avatar',
            'created_at',
            'status',
            'confirmed_at',
            'first_access',
            'last_access'
        ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            'id' => [
                'title' => 'id',
                'width' => '20px',
                'class' => 'text-start d-none',
            ],
            'codice' => [
                'title' => 'codice',
                'width' => '50px',
                'class' => 'text-start',
            ],
            'avatar' => [
                'title' => trans('plugins/ecommerce::customer.avatar'),
                'class' => 'text-center',
            ],
            'name' => [
                'title' => 'NOME',
                'class' => 'text-start',
            ],
            'email' => [
                'title' => trans('plugins/ecommerce::customer.email'),
                'class' => 'text-start',
            ],
            'first_access' => [
                'title' => 'Primo Accesso',
                'class' => 'text-start',
            ],
            'last_access' => [
                'title' => 'Ultimo Accesso',
                'class' => 'text-start',
            ],
            'status' => [
                'title' => 'STATO',

                'width' => '100px',
            ],
        ];

        if (EcommerceHelper::isEnableEmailVerification()) {
            $columns += [
                'confirmed_at' => [
                    'title' => trans('plugins/ecommerce::customer.email_verified'),
                    'width' => '100px',
                ],
            ];
        }

        return $columns;
    }

    public function buttons(): array
    {

        if (request()->user()->isSuperUser()) {
            $buttons['sync'] = [
                'extend' => 'collection',
                'text' => 'Synchronise',
                'buttons' => [
                    [
                        'className' => 'action-item',
                        'text' =>  Html::tag('span', 'Users', [
                            'data-action' => 'users',
                            'data-href' => route('ecommerce.customImport.users'),
                            'class' => 'ms-1',
                        ])->toHtml(),
                    ],
                    [
                        'className' => 'action-item',
                        'text' =>  Html::tag('span', 'Regione', [
                            'data-action' => 'regione',
                            'data-href' => route('ecommerce.customImport.user-regione'),
                            'class' => 'ms-1',
                        ])->toHtml(),
                    ],

                ],
            ];
        }
        $buttons['create'] = [
            'link' => route('customers.create'),
            'text' => 'Creare clienti',
        ];
        $buttons['exportsql'] = [
            'link' => route('ecommerce.customExport.customer'),
            'text' => 'Esportare sql',
        ];

        $buttons['customer-to-db'] = [
            'link' => route('ecommerce.customExport.customer-to-db'),
            'text' => 'Esportare DB',
        ];
        return $buttons;
        // return $this->addCreateButton(route('customers.create'), 'customers.create');
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('customers.deletes'), 'customers.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'name' => [
                'title' => 'NOME',
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => 'STATO',

                'type' => 'select',
                'choices' => CustomerStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', CustomerStatusEnum::values()),
            ],
            'created_at' => [
                'title' => 'CREATO_IL',

                'type' => 'datePicker',
            ],
        ];
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if ($this->query()->count() === 0 &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && ! $this->request()->ajax()
        ) {
            return view('plugins/ecommerce::customers.intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
