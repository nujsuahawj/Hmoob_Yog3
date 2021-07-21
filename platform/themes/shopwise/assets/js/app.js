/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import ProductCollectionsComponent from './components/ProductCollectionsComponent';
import FeaturedProductCategoriesComponent from './components/FeaturedProductCategoriesComponent';
import TrendingProductsComponent from './components/TrendingProductsComponent';
import FeaturedBrandsComponent from './components/FeaturedBrandsComponent';
import FeaturedProductsComponent from './components/FeaturedProductsComponent';
import TopRatedProductsComponent from './components/TopRatedProductsComponent';
import OnSaleProductsComponent from './components/OnSaleProductsComponent';
import FeaturedNewsComponent from "./components/FeaturedNewsComponent";
import TestimonialsComponent from "./components/TestimonialsComponent";

window.Vue = require('vue');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('product-collections-component', ProductCollectionsComponent);
Vue.component('featured-product-categories-component', FeaturedProductCategoriesComponent);
Vue.component('trending-products-component', TrendingProductsComponent);
Vue.component('featured-brands-component', FeaturedBrandsComponent);
Vue.component('featured-products-component', FeaturedProductsComponent);
Vue.component('top-rated-products-component', TopRatedProductsComponent);
Vue.component('on-sale-products-component', OnSaleProductsComponent);
Vue.component('featured-news-component', FeaturedNewsComponent);
Vue.component('testimonials-component', TestimonialsComponent);

/**
 * This let us access the `__` method for localization in VueJS templates
 * ({{ __('key') }})
 */
Vue.prototype.__ = (key) => {
    return window.trans[key] !== 'undefined' ? window.trans[key] : key;
};

const app = new Vue({
    el: '#app',
});
