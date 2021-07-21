@php
    $reviews = app(\Botble\Ecommerce\Repositories\Interfaces\ReviewInterface::class)->advancedGet([
        'condition' => [
            'status'     => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
            'product_id' => $product->id,
        ],
        'order_by' => ['created_at' => 'desc'],
        'paginate'  => [
            'per_page'      => 10,
            'current_paged' => (int) request()->input('page', 1),
        ],
    ]);
@endphp

<div class="comments">
    <h5 class="product_tab_title">{{ __(':count Review For :product', ['count' => $countRating, 'product' => $product->name]) }}</h5>
    <ul class="list_none comment_list mt-4">
        @foreach ($reviews as $review)
            @if (!empty($review->user->id) && !empty($review->product->id))
                <li>
                    <div class="comment_img">
                        <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user_name }}" />
                    </div>
                    <div class="comment_block">
                        <div class="rating_wrap">
                            <div class="rating">
                                <div class="product_rate" style="width: {{ $review->star * 20 }}%"></div>
                            </div>
                        </div>
                        <p class="customer_meta">
                            <span class="review_author">{{ $review->user_name }}</span>
                            <span class="comment-date">{{ $review->created_at->format('d M, Y') }}</span>
                        </p>
                        <div class="description">
                            <p>{{ $review->comment }}</p>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
            @endif
        @endforeach
    </ul>
</div>

@if (count($reviews) > 0)
    <br>
    <div class="mt-3 justify-content-center pagination_style1">
        {!! $reviews->appends(request()->query())->links() !!}
    </div>
    <br>
@endif
