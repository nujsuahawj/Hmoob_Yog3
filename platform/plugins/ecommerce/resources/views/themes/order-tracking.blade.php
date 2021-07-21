<section class="section--blog">
    <div class="section__content">
        <section class="section--auth">
            <form class="form--auth" method="GET" action="{{ route('public.orders.tracking') }}">
                <div class="form__header">
                    <h3>{{ __('Order tracking') }}</h3>
                    <p>{{ __('Tracking your order status') }}</p>
                </div>
                <div class="form__content">
                    <div class="form-group">
                        <label for="txt-order-id">{{ __('Order ID') }}<sup>*</sup></label>
                        <input class="form-control" name="order_id" id="txt-order-id" type="text" value="{{ old('order_id', request()->input('order_id')) }}" placeholder="{{ __('Order ID') }}">
                        @if ($errors->has('order_id'))
                            <span class="text-danger">{{ $errors->first('order_id') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="txt-email">{{ __('Email Address') }}<sup>*</sup></label>
                        <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email', request()->input('email')) }}" placeholder="{{ __('Please enter your email address') }}">
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form__actions">
                        <button type="submit" class="btn--custom btn--rounded btn--outline">{{ __('Find') }}</button>
                    </div>
                </div>
            </form>
            @if ($order)
                <div class="customer-order-detail">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('Order information') }}</h5>
                            <p>
                                <span>{{ __('Order number') }}:</span>
                                <strong>{{ get_order_code($order->id) }}</strong>
                            </p>
                            <p>
                                <span>{{ __('Time') }}:</span> <strong>{{ $order->created_at->format('h:m d/m/Y') }}</strong>
                            </p>
                            <p>
                                <span>{{ __('Order status') }}:</span> <strong>{{ $order->status->label() }}</strong>
                            </p>

                            <p>
                                <span>{{ __('Payment method') }}:</span> <strong> {{ $order->payment->payment_channel->label() }} </strong>
                            </p>

                            <p>
                                <span>{{ __('Payment status') }}:</span> <strong>{{ $order->payment->status->label() }}</strong>
                            </p>

                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Customer information') }}</h5>

                            <p>
                                <span>{{ __('Full Name') }}:</span> <strong>{{ $order->address->name }} </strong>
                            </p>

                            <p>
                                <span>{{ __('Phone') }}:</span> <strong>{{ $order->address->phone }} </strong>
                            </p>

                            <p>
                                <span>{{ __('Address') }}:</span> <strong> {{ $order->address->address }} </strong>
                            </p>

                            <p>
                                <span>{{ __('City') }}:</span> <strong>{{ $order->address->city }} </strong>
                            </p>
                            <p>
                                <span>{{ __('State') }}:</span> <strong> {{ $order->address->state }} </strong>
                            </p>
                        </div>
                    </div>
                    <br>
                    <h5>{{ __('Order detail') }}</h5>
                    <div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{ __('Image') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th class="text-center">{{ __('Amount') }}</th>
                                    <th class="text-right" style="width: 100px">{{ __('Quantity') }}</th>
                                    <th class="price text-right">{{ __('Total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->products as $key => $orderProduct)
                                    @php
                                        $product = get_products([
                                            'condition' => [
                                                'ec_products.status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
                                                'ec_products.id' => $orderProduct->product_id,
                                            ],
                                            'take' => 1,
                                            'select' => [
                                                'ec_products.images',
                                                'ec_products.name',
                                                'ec_products.price',
                                                'ec_products.sale_price',
                                                'ec_products.sale_type',
                                                'ec_products.start_date',
                                                'ec_products.end_date',
                                                'ec_products.sku',
                                            ],
                                        ]);

                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        <td class="text-center">
                                            <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" width="50" alt="{{ $product->name }}"></td>
                                        <td>{{ $product->name }} ({{ $product->sku }})</td>
                                        <td>{{ number_format($orderProduct->price, 0, '.', ',') }}</td>
                                        <td class="text-center">{{ $orderProduct->qty }}</td>
                                        <td class="money text-right">
                                            <strong>
                                                {{ number_format($orderProduct->price * $orderProduct->qty, 0, '.', ',') . ' ' . $orderProduct->currency }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p>
                            <span>{{ __('Shipping fee') }}:</span> <strong>  {{ format_price($order->shipping_amount, $order->currency_id) }} </strong>
                        </p>

                        <p>
                            <span>{{ __('Total Amount') }}:</span> <strong> {{ format_price($order->amount, $order->currency_id) }} </strong>
                        </p>
                    </div>
            @elseif (request()->input('order_id') || request()->input('email'))
                <p class="text-center text-danger">{{ __('Order not found!') }}</p>
            @endif
        </section>
    </div>
</section>
