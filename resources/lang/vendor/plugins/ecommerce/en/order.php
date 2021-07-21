<?php

return [
    'statuses'                            => [
        'pending'    => 'Pending',
        'processing' => 'Processing',
        'delivering' => 'Delivering',
        'delivered'  => 'Delivered',
        'completed'  => 'Completed',
        'canceled'   => 'Canceled',
    ],
    'name'                                => 'Orders',
    'create'                              => 'Create an order',
    'customer'                            => [
        'messages' => [
            'cancel_error'   => 'The order is delivering or completed',
            'cancel_success' => 'You do cancel the order successful',
        ],
    ],
    'incomplete_order'                    => 'Incomplete orders',
    'order_id'                            => 'Order ID',
    'customer_label'                      => 'Customer',
    'amount'                              => 'Amount',
    'tax_amount'                          => 'Tax Amount',
    'shipping_amount'                     => 'Shipping amount',
    'payment_method'                      => 'Payment method',
    'payment_status_label'                => 'Payment status',
    'manage_orders'                       => 'Manage orders',
    'order_intro_description'             => 'Once your store has orders, this is where you will process and track those orders.',
    'create_new_order'                    => 'Create a new order',
    'manage_incomplete_orders'            => 'Manage incomplete orders',
    'incomplete_orders_intro_description' => 'Incomplete order is an order created when a customer adds a product to the cart, proceeds to fill out the purchase information but does not complete the checkout process.',
];
