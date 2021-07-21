<div class="table">
    <table>
        <tr>
            <th>
                {{ __('Product') }}
            </th>
            <th>
                {{ __('Price') }}
            </th>
            <th>
                {{ __('Quantity') }}
            </th>
            <th>
                {{ __('Total') }}
            </th>
        </tr>

        @foreach ($order->products as $orderProduct)
            @php
                $product = get_products([
                        'condition' => [
                        'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                        'ec_products.id' => $orderProduct->product_id,
                    ],
                        'take' => 1,
                        'select' => [
                        'ec_products.id',
                        'ec_products.name',
                        'ec_products.price',
                        'ec_products.sale_price',
                        'ec_products.sale_type',
                        'ec_products.start_date',
                        'ec_products.end_date',
                        'ec_products.sku',
                    ],
                ])
            @endphp

            @if ($product)
                <tr>
                    <td>
                        {{ $product->name }}

                        @php
                            $attributes = get_product_attributes($product->id);
                        @endphp
                        @if (!empty($attributes))
                            @foreach ($attributes as $attr)
                                ({{ $attr->attribute_set_title }}: {{ $attr->title }})
                            @endforeach
                        @endif

                    </td>

                    <td>
                        {{ format_price($orderProduct->price) }}
                    </td>

                    <td>
                        x {{ $orderProduct->qty }}
                    </td>

                    <td>
                        {{ format_price($orderProduct->qty * $orderProduct->price) }}
                    </td>

                </tr>
            @endif
        @endforeach
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
                {{ __('Sub total') }}
            </td>
            <td>
                {{ format_price($order->sub_total) }}
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>{{ __('Shipping fee') }}
            </td>
            <td>
                {{ format_price($order->shipping_amount) }}
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>{{ __('Discount') }}
            </td>
            <td>
                {{ format_price($order->discount_amount) }}
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td>{{ __('Total') }}
            </td>
            <td>
                {{ format_price($order->amount) }}
            </td>
        </tr>
    </table><br>
</div>

