<template>
    <div class="col-lg-9">
        <div v-if="isLoading">
            <div class="half-circle-spinner">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
            </div>
        </div>
        <div v-if="!isLoading" v-carousel class="testimonial_wrap testimonial_style1 carousel_slider owl-carousel owl-theme nav_style2" data-nav="true" data-dots="false" data-center="true" data-loop="true" data-autoplay="true" data-items='1'>
            <div class="testimonial_box" v-for="item in data">
                <div class="testimonial_desc">
                    <p v-html="item.content"></p>
                </div>
                <div class="author_wrap">
                    <div class="author_img">
                        <img :src="item.image" :alt="item.name">
                    </div>
                    <div class="author_name">
                        <h6>{{ item.name }}</h6>
                        <span>{{ item.company }}</span>
                    </div>
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
                axios.get('/ajax/testimonials')
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
