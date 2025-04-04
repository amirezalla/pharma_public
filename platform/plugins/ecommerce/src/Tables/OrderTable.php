<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderHistoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Table\Abstracts\TableAbstract;
use EcommerceHelper;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Mollie\Api\Types\OrderStatus;
use OrderHelper;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\DataTables;

class OrderTable extends TableAbstract
{
    protected $hasActions = true;

    protected $hasFilter = true;


    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OrderInterface $orderRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $orderRepository;

        if (! Auth::user()->hasPermission('orders.edit')) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->editColumn('payment_status', function ($item) {
                return $item->payment->status->label() ? BaseHelper::clean(
                    $item->payment->status->toHtml()
                ) : '&mdash;';
            })
            ->editColumn('payment_method', function ($item) {
                return BaseHelper::clean($item->payment->payment_channel->label() ?: '&mdash;');
            })
            ->editColumn('amount', function ($item) {
                return format_price($item->amount);
            })
            ->editColumn('shipping_amount', function ($item) {
                return format_price($item->shipping_amount);
            })
            ->editColumn('user_id', function ($item) {
                return BaseHelper::clean($item->user->name ?: $item->address->name);
            })
            ->editColumn('user_codice', function ($item) {
                return BaseHelper::clean($item->user->codice ?: $item->user->name);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at, 'd/m/Y H:i');
            });

        if (EcommerceHelper::isTaxEnabled()) {
            $data = $data->editColumn('tax_amount', function ($item) {
                return format_price($item->tax_amount);
            });
        }

        $data = $data
            ->addColumn('operations', function ($item) {
                return $this->getOperations('orders.edit', 'orders.destroy', $item,"<a href='https://marigopharma.it/public/admin/orders/generate-invoice/$item->id' class='btn btn-icon btn-sm btn-primary'><i class='fa fa-download'></i></a>");
            })
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');
                if ($keyword) {
                    return $query
                        ->whereHas('address', function ($subQuery) use ($keyword) {
                            return $subQuery->where('name', 'LIKE', '%' . $keyword . '%')->where('is_finished',1);
                        })
                        ->orWhereHas('user', function ($subQuery) use ($keyword) {
                            return $subQuery->where(function ($subQuery1) use ($keyword) {
                                $subQuery1->orWhere('codice', 'LIKE', '%' . $keyword . '%');
                            })->where('is_finished',1);
                        })
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%');
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->with(['user', 'payment'])
            ->where('status',OrderStatusEnum::COMPLETED)
            ->select([
                'id',
                'status',
                'user_id',
                'created_at',
                'amount',
                'tax_amount',
                'shipping_amount',
                'payment_id',
            ]);
            

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            'id' => [
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'class' => 'text-start',
            ],
            'user_id' => [
                'title' => trans('plugins/ecommerce::order.customer_label'),
                'class' => 'text-start',
            ],
            'user_codice' => [
                'title' => "Codice Cliente",
                'class' => 'text-start',
            ],
            'amount' => [
                'title' => "Imponibile",
                'class' => 'text-center',
            ],
        ];

        if (EcommerceHelper::isTaxEnabled()) {
            $columns['tax_amount'] = [
                'title' => "IVA",
                'class' => 'text-center',
            ];
        }

        $columns += [
            'shipping_amount' => [
                'title' => trans('plugins/ecommerce::order.shipping_amount'),
                'class' => 'text-center',
            ],
            // 'payment_method' => [
            //     'name' => 'payment_id',
            //     'title' => trans('plugins/ecommerce::order.payment_method'),
            //     'class' => 'text-start',
            // ],
            // 'payment_status' => [
            //     'name' => 'payment_id',
            //     'title' => trans('plugins/ecommerce::order.payment_status_label'),
            //     'class' => 'text-center',
            // ],
            'status' => [
                'title' => 'STATO',

                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => 'CREATO_IL',

                'width' => '100px',
                'class' => 'text-start',
            ],
        ];

        return $columns;
    }



    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
    }

    public function getBulkChanges(): array
    {
        return [
            'status' => [
                'title' => 'STATO',

                'type' => 'select',
                'choices' => OrderStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', OrderStatusEnum::values()),
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
            ! $this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && ! $this->request()->ajax()
        ) {
            return view('plugins/ecommerce::orders.intro');
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

    public function saveBulkChangeItem(Model $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'status' && $inputValue == OrderStatusEnum::CANCELED) {
            if (! $item->canBeCanceledByAdmin()) {
                return $item;
            }

            OrderHelper::cancelOrder($item);

            app(OrderHistoryInterface::class)->createOrUpdate([
                'action' => 'cancel_order',
                'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
                'order_id' => $item->id,
                'user_id' => Auth::id(),
            ]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
    public function buttons(): array
    {


        $buttons['create'] = [
            'link' => route('orders.create'),
            'text' => 'Creare clienti',
            'permission' => 'orders.create',
        ];
        $buttons['exportsql'] = [
            'link' => route('ecommerce.customExport.order'),
            'text' => 'Esportare sql',
        ];

        $buttons['order-to-db'] = [
            'link' => route('ecommerce.customExport.order-to-db'),
            'text' => 'Esportare DB',
        ];

        return $buttons;

    }
}
