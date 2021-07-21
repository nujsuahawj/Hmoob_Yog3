<template>
        <div class="container">
            <div class="heading_tab_header">
                <div class="heading_s2">
                    <h4>{{ title }}</h4>
                </div>
                <div class="tab-style2">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#tabmenubar" aria-expanded="false">
                        <span class="ion-android-menu"></span>
                    </button>
                    <ul class="nav nav-tabs justify-content-center justify-content-md-end" id="tabmenubar" role="tablist">
                        <li class="nav-item" v-for="item in productCollections" :key="item.id">
                            <a :class="productCollection.id === item.id ? 'nav-link  active': 'nav-link'" :id="item.slug + '-tab'" data-toggle="tab" :href="'#' + item.slug" role="tab" :aria-controls="item.slug" aria-selected="true" @click="getProducts(item)">{{ item.name }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab_slider">
                <div class="half-circle-spinner" v-if="isLoading">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="tab-pane fade show active" v-if="!isLoading" :id="productCollection.slug" role="tabpanel" :aria-labelledby="productCollection.slug + '-tab'" :key="productCollection.id">
                    <div class="product_slider carousel_slider owl-carousel owl-theme dot_style1" v-carousel data-loop="true" data-margin="20" data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
                        <div class="item" v-for="item in data" :key="item.id" v-if="data.length" v-html="item"></div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script>
    export default {
        data: function() {
            return {
                isLoading: true,
                data: [],
                productCollections: [],
                productCollection: {}
            };
        },

        mounted() {
            if (this.product_collections.length) {
                this.productCollections = this.product_collections;
                this.productCollection = this.productCollections[0];
                this.getProducts(this.productCollection);
            }
        },

        props: {
            product_collections: {
                type: Array,
                default: () => [],
            },
            title: {
                type: String,
                default: () => null,
            },
        },

        methods: {
            getProducts(productCollection) {
                this.productCollection = productCollection;
                this.data = [];
                this.isLoading = true;
                axios.get('/ajax/product-collections/' + productCollection.id + '/products')
                    .then(res => {
                        this.data = res.data.data;
                        this.isLoading = false;
                    })
                    .catch(res => {
                        console.log(res);
                    });
            },
        },
        directives: {
            carousel: {
                inserted: function (el) {
                    $(el).owlCarousel({
                        dots : $(el).data('dots'),
                        loop : $(el).data('loop'),
                        items: $(el).data('items'),
                        margin: $(el).data('margin'),
                        mouseDrag: $(el).data('mouse-drag'),
                        touchDrag: $(el).data('touch-drag'),
                        autoHeight: $(el).data('autoheight'),
                        center: $(el).data('center'),
                        nav: $(el).data('nav'),
                        rewind: $(el).data('rewind'),
                        navText: ['<i class="ion-ios-arrow-left"></i>', '<i class="ion-ios-arrow-right"></i>'],
                        autoplay : $(el).data('autoplay'),
                        animateIn : $(el).data('animate-in'),
                        animateOut: $(el).data('animate-out'),
                        autoplayTimeout : $(el).data('autoplay-timeout'),
                        smartSpeed: $(el).data('smart-speed'),
                        responsive: $(el).data('responsive')
                    })
                },
            }
        }
    }
</script>
