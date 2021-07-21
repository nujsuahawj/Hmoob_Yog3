<template>
    <div class="col-12">
        <div v-if="isLoading">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <div  v-if="!isLoading" v-carousel class="product_slider carousel_slider owl-carousel owl-theme dot_style1" data-loop="true" data-margin="20" data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "991":{"items": "4"}}'>
            <div class="item" v-for="item in data" :key="item.id" v-if="data.length" v-html="item"></div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function() {
            return {
                isLoading: true,
                data: []
            };
        },
        mounted() {
          this.getTrendingProducts();
        },
        methods: {
            getTrendingProducts() {
                this.data = [];
                this.isLoading = true;
                axios.get('/ajax/trending-products')
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
