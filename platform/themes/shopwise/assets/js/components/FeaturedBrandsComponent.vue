<template>
    <div class="col-12">
        <div v-if="isLoading">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <div v-if="!isLoading" v-carousel class="client_logo carousel_slider owl-carousel owl-theme nav_style3" data-dots="false" data-nav="true" data-margin="30" data-loop="true" data-autoplay="true" data-responsive='{"0":{"items": "2"}, "480":{"items": "3"}, "767":{"items": "4"}, "991":{"items": "5"}, "1199":{"items": "6"}}'>
            <div class="item" v-for="item in data">
                <div class="cl_logo">
                    <img :src="item.logo" alt=":item.name"/>
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
                data: []
            };
        },
        mounted() {
          this.getFeaturedBrands();
        },
        methods: {
            getFeaturedBrands() {
                this.data = [];
                this.isLoading = true;
                axios.get('/ajax/featured-brands')
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
