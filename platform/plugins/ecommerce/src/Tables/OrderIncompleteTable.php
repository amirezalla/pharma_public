<?php

namespace Botble\Ecommerce\Tables;

use BaseHelper;
use Html;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderIncompleteTable extends OrderTable
{
    protected $hasCheckbox = true;

    protected $hasActions = true;

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
            ->editColumn('amount', function ($item) {
                return format_price($item->amount);
            })
            ->editColumn('user_id', function ($item) {
                return BaseHelper::clean($item->user->name ?: $item->address->name);
            })
            ->editColumn('user_codice', function ($item) {
                return BaseHelper::clean($item->user->codice ?: $item->user->name);
            })
            ->editColumn('status', function ($item) {
                return BaseHelper::clean($item->status->toHtml());
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at, 'd/m/Y H:i');
            })
            ->addColumn('operations', function ($item) {
                $viewButton = Html::link(
                    route('orders.view-incomplete-order', $item->id),
                    Html::tag('i', '', ['class' => 'fa fa-eye'])->toHtml(),
                    [
                        'class' => 'btn btn-icon btn-sm btn-primary',
                        'data-bs-toggle' => 'tooltip',
                        'data-bs-original-title' => trans('core/base::tables.edit'),
                    ],
                    null,
                    false
                )->toHtml();

                return $this->getOperations('orders.edit', 'orders.destroy', $item, $viewButton);
            })
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');
                if ($keyword) {
                    return $query
                        ->whereHas('address', function ($subQuery) use ($keyword) {
                            return $subQuery->where('name', 'LIKE', '%' . $keyword . '%')->where('is_finished',0);
                        })
                        ->orWhereHas('user', function ($subQuery) use ($keyword) {
                            return $subQuery->where(function ($subQuery1) use ($keyword) {
                                $subQuery1->orWhere('codice', 'LIKE', '%' . $keyword . '%');
                            })->where('is_finished',1);
                        })
                        ->orWhere('code', 'LIKE', '%' . $keyword . '%')->where('is_finished',0);
                }

                return $query;
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->repository->getModel()
            ->where('status','returned')
            ->orWhere('status','pending')
            ->orWhere('status','canceled')
            ->select([
                'id',
                'user_id',
                'status',
                'created_at',
                'amount',
            ])
            ->with(['user']);


        return $this->applyScopes($query);
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if ($this->query()->count() === 0 &&
            ! $this->request()->wantsJson() &&
            $this->request()->input('filter_table_id') !== $this->getOption('id') && ! $this->request()->ajax()
        ) {
            return view('plugins/ecommerce::orders.incomplete-intro');
        }

        return parent::renderTable($data, $mergeData);
    }

    public function columns(): array
    {
        return [
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
            'status' => [
                'title' => "SATATO",
                'class' => 'text-center',
            ],
            'created_at' => [
                'title' => 'CREATO_IL',

                'width' => '100px',
                'class' => 'text-start',
            ],
        ];
    }

    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('orders.deletes'), 'orders.destroy', parent::bulkActions());
    }
}
