<?php

namespace Botble\Ecommerce;

use Botble\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Schema;
use Setting;

class Plugin extends PluginOperationAbstract
{
    public static function activated()
    {
        Setting::set([
            'payment_cod_status'           => 1,
            'payment_bank_transfer_status' => 1,
        ])->save();
    }

    public static function remove()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ec_product_tag_product');
        Schema::dropIfExists('ec_product_collection_products');
        Schema::dropIfExists('ec_product_category_product');
        Schema::dropIfExists('ec_prices');
        Schema::dropIfExists('ec_products');
        Schema::dropIfExists('ec_currencies');
        Schema::dropIfExists('ec_product_collections');
        Schema::dropIfExists('ec_product_categories');
        Schema::dropIfExists('ec_product_tag_product');
        Schema::dropIfExists('ec_product_tags');
        Schema::dropIfExists('ec_brands');
        Schema::dropIfExists('ec_product_variation_items');
        Schema::dropIfExists('ec_product_variations');
        Schema::dropIfExists('ec_product_with_attribute');
        Schema::dropIfExists('ec_product_with_attribute_set');
        Schema::dropIfExists('ec_product_attributes');
        Schema::dropIfExists('ec_product_attribute_sets');
        Schema::dropIfExists('ec_taxes');
        Schema::dropIfExists('ec_reviews');
        Schema::dropIfExists('ec_shipping');
        Schema::dropIfExists('ec_orders');
        Schema::dropIfExists('ec_order_product');
        Schema::dropIfExists('ec_order_addresses');
        Schema::dropIfExists('ec_discounts');
        Schema::dropIfExists('ec_wish_lists');
        Schema::dropIfExists('ec_cart');
        Schema::dropIfExists('ec_grouped_products');
        Schema::dropIfExists('ec_customers');
        Schema::dropIfExists('ec_customer_password_resets');
        Schema::dropIfExists('ec_customer_addresses');
        Schema::dropIfExists('ec_product_up_sale_relations');
        Schema::dropIfExists('ec_product_cross_sale_relations');
        Schema::dropIfExists('ec_product_related_relations');
        Schema::dropIfExists('ec_shipping_rules');
        Schema::dropIfExists('ec_shipping_rule_items');
        Schema::dropIfExists('ec_order_histories');
        Schema::dropIfExists('ec_shipments');
        Schema::dropIfExists('ec_shipment_histories');
        Schema::dropIfExists('ec_store_locators');
        Schema::dropIfExists('ec_discount_products');
        Schema::dropIfExists('ec_discount_customers');
        Schema::dropIfExists('ec_discount_product_collections');

        app(DashboardWidgetInterface::class)->deleteBy(['name' => 'widget_ecommerce_report_general']);
    }
}
