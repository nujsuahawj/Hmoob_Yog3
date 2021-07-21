<?php

namespace Botble\Ecommerce\Tables\Reports;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\OrderProductInterface;
use Botble\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;

class TopSellingProductsTable extends TableAbstract
{

    /**
     * @var string
     */
    protected $type = self::TABLE_TYPE_SIMPLE;

    /**
     * @var string
     */
    protected $view = 'core/table::simple-table';

    /**
     * TopSellingProductsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param OrderProductInterface $orderProductRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        OrderProductInterface $orderProductRepository
    ) {
        parent::__construct($table, $urlGenerator);
        $this->repository = $orderProductRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        return $this->table
            ->eloquent($this->query())
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * @return mixed
     */
    public function query()
    {
        $query = $this->repository
            ->getModel()
            ->join('ec_orders', 'ec_orders.id', '=', 'ec_order_product.order_id')
            ->where('ec_orders.status', OrderStatusEnum::COMPLETED)
            ->whereDate('ec_order_product.created_at', '>=',
                now(config('app.timezone'))->startOfMonth()->toDateString())
            ->whereDate('ec_order_product.created_at', '<=', now(config('app.timezone'))->endOfMonth()->toDateString())
            ->select(['ec_order_product.product_name', 'ec_order_product.product_id', 'ec_order_product.qty'])
            ->orderBy('ec_order_product.qty', 'DESC');

        return $this->applyScopes($query);
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'product_id'   => [
                'name'      => 'ec_products.id',
                'title'     => trans('plugins/ecommerce::order.order_id'),
                'width'     => '120px',
                'orderable' => false,
                'class'     => 'no-sort text-center',
            ],
            'product_name' => [
                'name'      => 'ec_products.name',
                'title'     => trans('plugins/ecommerce::reports.product_name'),
                'orderable' => false,
                'class'     => 'text-left',
            ],
            'qty'          => [
                'name'      => 'ec_products.quantity',
                'title'     => trans('plugins/ecommerce::reports.quantity'),
                'orderable' => false,
                'class'     => 'text-center',
                'width'     => '60px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function renderTable($data = [], $mergeData = [])
    {
        if ($this->query()->count() == 0) {
            return view('core/dashboard::partials.no-data')->render();
        }
        return parent::renderTable($data, $mergeData);
    }
}
