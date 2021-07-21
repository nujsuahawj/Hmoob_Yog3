<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Facades\DB;

class ProductAttributeSetRepository extends RepositoriesAbstract implements ProductAttributeSetInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByProductId($productId)
    {
        $data = $this->model
            ->join(
                'ec_product_with_attribute_set',
                'ec_product_attribute_sets.id',
                '=',
                'ec_product_with_attribute_set.attribute_set_id'
            )
            ->where('ec_product_with_attribute_set.product_id', $productId)
            ->distinct()
            ->select('ec_product_attribute_sets.*')
            ->orderBy('ec_product_with_attribute_set.order', 'ASC')
            ->get();

        $this->resetModel();

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllWithSelected($productId)
    {
        $data = $this->model
            ->leftJoin(DB::raw('
                (
                    SELECT ec_product_with_attribute_set.*
                    FROM ec_product_with_attribute_set
                    WHERE ec_product_with_attribute_set.product_id = ' . esc_sql($productId) . '
                ) AS PAR
            '), 'ec_product_attribute_sets.id', '=', 'PAR.attribute_set_id')
            ->distinct()
            ->select([
                'ec_product_attribute_sets.*',
                'PAR.product_id AS is_selected',
            ])
            ->orderBy('ec_product_attribute_sets.order', 'ASC')
            ->get();

        $this->resetModel();

        return $data;
    }
}
