<script id="product_select_image_template" type="text/x-custom-template">
    @include('plugins/ecommerce::components.form.image', [
        'name' => '__name__',
        'value' => null,
        'thumb' => RvMedia::getDefaultImage(false),
    ])
</script>
