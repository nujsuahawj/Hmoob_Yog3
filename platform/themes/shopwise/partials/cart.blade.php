@if (Cart::instance('cart')->count() > 0)
    <ul class="cart_list">
        @php
            $products = [];
            $productIds = Cart::instance('cart')->content()->pluck('id')->toArray();

            if ($productIds) {
                $products = get_products([
                    'condition' => [
                        ['ec_products.id', 'IN', $productIds],
                    ],
                    'with' => ['slugable'],
                ]);
            }
        @endphp
        @if (count($products))
            @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                @php
                    $product = $products->where('id', $cartItem->id)->first();
                @endphp

                @if (!empty($product))
                    <li>
                        <a href="{{ route('public.cart.remove', $cartItem->rowId) }}" class="item_remove remove-cart-button"><i class="ion-close"></i></a>
                        <a href="{{ $product->url }}"><img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" /> {{ $product->name }}</a>
                        <span class="cart_quantity"> {{ $cartItem->qty }} x <span class="cart_amount"> <span class="price_symbole">{{ get_application_currency()->symbol }}</span></span>{{ format_price($cartItem->price, null, true, false) }}</span>
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
    <div class="cart_footer">
        <p class="cart_total"><strong>{{ __('Sub Total') }}:</strong> <span class="cart_price"> <span class="price_symbole">{{ get_application_currency()->symbol }}</span></span>{{ format_price(Cart::instance('cart')->rawSubTotal(), null, true, false) }}</p>
        <p class="cart_buttons">
            <a href="{{ route('public.cart') }}" class="btn btn-fill-line view-cart">{{ __('View Cart') }}</a>
            @if (session('tracked_start_checkout'))
                <a href="{{ route('public.checkout.information', session('tracked_start_checkout')) }}" class="btn btn-fill-out checkout">{{ __('Checkout') }}</a>
            @endif
        </p>
    </div>
@else
    <p class="text-center">{{ __('Your cart is empty!') }}</p>
@endif
