<!-- START SECTION BANNER -->
<div class="mt-4 staggered-animation-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 offset-lg-3">
                <div class="banner_section shop_el_slider">
                    <div id="carouselExampleControls" class="carousel slide carousel-fade light_arrow" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($products as $product)
                                <div class="carousel-item @if ($loop->first) active @endif background_bg" data-img-src="{{ RvMedia::getImageUrl(MetaBox::getMetaData($product, 'featured_image', true), 'featured', false, RvMedia::getDefaultImage()) }}">
                                    <div class="banner_slide_content banner_content_inner">
                                        <div class="col-lg-7 col-10">
                                            <div class="banner_content3 overflow-hidden">
                                                <h2 class="staggered-animation" data-animation="slideInLeft" data-animation-delay="1s">{{ $product->name }}</h2>
                                                <h4 class="staggered-animation mb-4 product-price" data-animation="slideInLeft" data-animation-delay="1.2s">
                                                    <span class="price">{{ format_price($product->front_sale_price) }}</span>
                                                    @if ($product->front_sale_price !== $product->price)
                                                        <del>{{ format_price($product->price) }}</del>
                                                    @endif
                                                </h4>
                                                <a class="btn btn-fill-out btn-radius staggered-animation text-uppercase" href="{{ $product->url }}" data-animation="slideInLeft" data-animation-delay="1.5s">{{ __('Shop Now') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <ol class="carousel-indicators indicators_style3">
                            @foreach($products as $product)
                                <li data-target="#carouselExampleControls" data-slide-to="{{ $loop->index }}" @if ($loop->first) class="active" @endif></li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION BANNER -->
