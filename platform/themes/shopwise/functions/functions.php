<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;

register_page_template([
    'default'      => 'Default',
    'homepage'     => 'Homepage',
    'blog-sidebar' => 'Blog Sidebar',
]);

register_sidebar([
    'id'          => 'footer_sidebar',
    'name'        => 'Footer sidebar',
    'description' => 'Sidebar in the footer of site',
]);

theme_option()
    ->setField([
        'id'         => 'copyright',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Copyright'),
        'attributes' => [
            'name'    => 'copyright',
            'value'   => '© 2020 Botble Technologies. All right reserved.',
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => __('Change copyright'),
                'data-counter' => 250,
            ],
        ],
        'helper'     => __('Copyright on footer of site'),
    ])
    ->setField([
        'id'         => 'preloader_enabled',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable Preloader?'),
        'attributes' => [
            'name'    => 'preloader_enabled',
            'list'    => [
                'no'  => 'No',
                'yes' => 'Yes',
            ],
            'value'   => 'no',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'hotline',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => 'Hotline',
        'attributes' => [
            'name'    => 'hotline',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Hotline',
                'data-counter' => 30,
            ],
        ],
    ])
    ->setField([
        'id'         => 'address',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => 'Address',
        'attributes' => [
            'name'    => 'address',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Address',
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'email',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'email',
        'label'      => 'Email',
        'attributes' => [
            'name'    => 'email',
            'value'   => null,
            'options' => [
                'class'        => 'form-control',
                'placeholder'  => 'Email',
                'data-counter' => 120,
            ],
        ],
    ])
    ->setField([
        'id'         => 'about-us',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'textarea',
        'label'      => 'About us',
        'attributes' => [
            'name'    => 'about-us',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'primary_font',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'googleFonts',
        'label'      => __('Primary font'),
        'attributes' => [
            'name'  => 'primary_font',
            'value' => 'Poppins',
        ],
    ])
    ->setField([
        'id'         => 'primary_color',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Primary color'),
        'attributes' => [
            'name'  => 'primary_color',
            'value' => '#FF324D',
        ],
    ])
    ->setField([
        'id'         => 'secondary_color',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'customColor',
        'label'      => __('Secondary color'),
        'attributes' => [
            'name'  => 'secondary_color',
            'value' => '#1D2224',
        ],
    ])
    ->setField([
        'id'         => 'newsletter_image',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'mediaImage',
        'label'      => __('Image for newsletter popup'),
        'attributes' => [
            'name'  => 'newsletter_image',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'logo_footer',
        'section_id' => 'opt-text-subsection-logo',
        'type'       => 'mediaImage',
        'label'      => __('Logo in Footer'),
        'attributes' => [
            'name'  => 'logo_footer',
            'value' => null,
        ],
    ])
    ->setSection([
        'title'      => __('Social'),
        'desc'       => __('Social links'),
        'id'         => 'opt-text-subsection-social',
        'subsection' => true,
        'icon'       => 'fa fa-share-alt',
    ])
    ->setField([
        'id'         => 'facebook',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Facebook',
        'attributes' => [
            'name'    => 'facebook',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'twitter',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Twitter',
        'attributes' => [
            'name'    => 'twitter',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'youtube',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Youtube',
        'attributes' => [
            'name'    => 'youtube',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'instagram',
        'section_id' => 'opt-text-subsection-social',
        'type'       => 'text',
        'label'      => 'Instagram',
        'attributes' => [
            'name'    => 'instagram',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'facebook_chat_enabled',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable Facebook chat?'),
        'attributes' => [
            'name'    => 'facebook_chat_enabled',
            'list'    => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value'   => 'yes',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'facebook_page_id',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'text',
        'label'      => __('Facebook page ID'),
        'attributes' => [
            'name'    => 'facebook_page_id',
            'value'   => null,
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'facebook_comment_enabled_in_post',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable Facebook comment in post detail page?'),
        'attributes' => [
            'name'    => 'facebook_comment_enabled_in_post',
            'list'    => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value'   => 'yes',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setField([
        'id'         => 'facebook_comment_enabled_in_product',
        'section_id' => 'opt-text-subsection-general',
        'type'       => 'select',
        'label'      => __('Enable Facebook comment in product detail page?'),
        'attributes' => [
            'name'    => 'facebook_comment_enabled_in_product',
            'list'    => [
                'yes' => 'Yes',
                'no'  => 'No',
            ],
            'value'   => 'yes',
            'options' => [
                'class' => 'form-control',
            ],
        ],
    ])
    ->setSection([
        'title'      => __('Accepted payment methods'),
        'desc'       => __('Payment methods are accepted?'),
        'id'         => 'opt-text-subsection-payment-methods',
        'subsection' => true,
        'icon'       => 'fas fa-money-check',
    ])
    ->setField([
        'id'         => 'payment_method_1',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 1',
        'attributes' => [
            'name'  => 'payment_method_1',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'payment_method_2',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 2',
        'attributes' => [
            'name'  => 'payment_method_2',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'payment_method_3',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 3',
        'attributes' => [
            'name'  => 'payment_method_3',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'payment_method_4',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 4',
        'attributes' => [
            'name'  => 'payment_method_4',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'payment_method_5',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 5',
        'attributes' => [
            'name'  => 'payment_method_5',
            'value' => null,
        ],
    ])
    ->setField([
        'id'         => 'payment_method_6',
        'section_id' => 'opt-text-subsection-payment-methods',
        'type'       => 'mediaImage',
        'label'      => 'Payment method 6',
        'attributes' => [
            'name'  => 'payment_method_6',
            'value' => null,
        ],
    ])
    ->setSection([
        'title'      => 'Ecommerce',
        'desc'       => 'Theme options for Ecommerce',
        'id'         => 'opt-text-subsection-ecommerce',
        'subsection' => true,
        'icon'       => 'fa fa-shopping-cart',
        'fields'     => [
            [
                'id'         => 'number_of_products_per_page',
                'type'       => 'number',
                'label'      => __('Number of products per page'),
                'attributes' => [
                    'name'    => 'number_of_products_per_page',
                    'value'   => 12,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'id'         => 'max_filter_price',
                'type'       => 'number',
                'label'      => __('Maximum price to filter'),
                'attributes' => [
                    'name'    => 'max_filter_price',
                    'value'   => 100000,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
        ],
    ]);

add_action('init', function () {
    config(['filesystems.disks.public.root' => public_path('storage')]);
}, 124);

RvMedia::addSize('featured', 825, 550)
    ->addSize('medium', 540, 600)
    ->addSize('small', 540, 300)
    ->addSize('banner', 250, 355);

if (is_plugin_active('ecommerce')) {
    add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
        if (get_class($object) == ProductCategory::class && $context == 'advanced') {
            MetaBox::addMetaBox('additional_product_category_fields', __('Addition Information'), function () {
                $icon = null;
                $args = func_get_args();
                if (!empty($args[0])) {
                    $icon = MetaBox::getMetaData($args[0], 'icon', true);
                }

                return Theme::partial('product-category-fields', compact('icon'));
            }, get_class($object), $context, 'default');
        }
    }, 24, 2);

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == ProductCategory::class) {
            MetaBox::saveMetaBoxData($object, 'icon', $request->input('icon'));
        }
    }, 230, 3);

    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == ProductCategory::class) {
            MetaBox::saveMetaBoxData($object, 'icon', $request->input('icon'));
        }
    }, 231, 3);

    add_shortcode('featured-product-categories', 'Featured Product Categories', 'Featured Product Categories',
        function ($shortCode) {

            return Theme::partial('short-codes.featured-product-categories', [
                'title'       => $shortCode->title,
                'description' => $shortCode->description,
            ]);
        });

    shortcode()->setAdminConfig('featured-product-categories',
        Theme::partial('short-codes.featured-product-categories-admin-config'));

    add_shortcode('featured-brands', 'Featured Brands', 'Featured Brands', function ($shortCode) {
        return Theme::partial('short-codes.featured-brands', [
            'title' => $shortCode->title,
        ]);
    });

    shortcode()->setAdminConfig('featured-brands', Theme::partial('short-codes.featured-brands-admin-config'));

    add_shortcode('featured-products', 'Featured products', 'Featured products', function ($shortCode) {

        $products = get_featured_products([
            'take' => $shortCode->limit ? $shortCode->limit : 4,
            'with' => ['slugable'],
        ]);

        return Theme::partial('short-codes.featured-products', compact('products'));
    });

    shortcode()->setAdminConfig('featured-products', Theme::partial('short-codes.featured-products-admin-config'));

    add_shortcode('product-collections', 'Product Collections', 'Product Collections', function ($shortCode) {
        $productCollections = get_product_collections(['status' => BaseStatusEnum::PUBLISHED], [],
            ['id', 'name', 'slug'])->toArray();

        return Theme::partial('short-codes.product-collections', [
            'title'              => $shortCode->title,
            'productCollections' => $productCollections,
        ]);
    });

    shortcode()->setAdminConfig('product-collections',
        Theme::partial('short-codes.product-collections-admin-config'));

    add_action(BASE_ACTION_META_BOXES, function ($context, $object) {
        if (get_class($object) == Product::class && $context == 'side') {
            MetaBox::addMetaBox('additional_product_fields', __('Addition Information'), function () {
                $featuredImage = null;
                $args = func_get_args();
                if (!empty($args[0])) {
                    $featuredImage = MetaBox::getMetaData($args[0], 'featured_image', true);
                }

                return Theme::partial('product-fields', compact('featuredImage'));
            }, get_class($object), $context, 'default');
        }
    }, 24, 2);

    add_action(BASE_ACTION_AFTER_CREATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == Product::class) {
            MetaBox::saveMetaBoxData($object, 'featured_image', $request->input('featured_image'));
        }
    }, 230, 3);

    add_action(BASE_ACTION_AFTER_UPDATE_CONTENT, function ($type, $request, $object) {
        if (get_class($object) == Product::class) {
            MetaBox::saveMetaBoxData($object, 'featured_image', $request->input('featured_image'));
        }
    }, 231, 3);

    add_shortcode('trending-products', 'Trending Products', 'Trending Products', function ($shortCode) {
        return Theme::partial('short-codes.trending-products', [
            'title' => $shortCode->title,
        ]);
    });

    shortcode()->setAdminConfig('trending-products', Theme::partial('short-codes.trending-products-admin-config'));

    add_shortcode('product-blocks', 'Product Blocks', 'Product Blocks', function ($shortCode) {
        return Theme::partial('short-codes.product-blocks', [
            'featured_product_title'  => $shortCode->featured_product_title,
            'top_rated_product_title' => $shortCode->top_rated_product_title,
            'on_sale_product_title'   => $shortCode->on_sale_product_title,
        ]);
    });

    shortcode()->setAdminConfig('product-blocks', Theme::partial('short-codes.product-blocks-admin-config'));
}

add_shortcode('banners', 'Banners', 'Banners', function ($shortCode) {
    return Theme::partial('short-codes.banners', [
        'image1' => $shortCode->image1,
        'url1'   => $shortCode->url1,
        'image2' => $shortCode->image2,
        'url2'   => $shortCode->url2,
        'image3' => $shortCode->image3,
        'url3'   => $shortCode->url3,
    ]);
});

shortcode()->setAdminConfig('banners', Theme::partial('short-codes.banners-admin-config'));

add_shortcode('our-features', 'Our features', 'Our features', function ($shortCode) {
    return Theme::partial('short-codes.our-features', [
        'icon1'        => $shortCode->icon1,
        'title1'       => $shortCode->title1,
        'description1' => $shortCode->description1,
        'icon2'        => $shortCode->icon2,
        'title2'       => $shortCode->title2,
        'description2' => $shortCode->description2,
        'icon3'        => $shortCode->icon3,
        'title3'       => $shortCode->title3,
        'description3' => $shortCode->description3,
    ]);
});

shortcode()->setAdminConfig('our-features', Theme::partial('short-codes.our-features-admin-config'));

if (is_plugin_active('testimonial')) {
    add_shortcode('testimonials', 'Testimonials', 'Testimonials', function ($shortCode) {
        return Theme::partial('short-codes.testimonials', [
            'title'        => $shortCode->title,
        ]);
    });

    shortcode()->setAdminConfig('testimonials', Theme::partial('short-codes.testimonials-admin-config'));
}

if (is_plugin_active('newsletter')) {
    add_shortcode('newsletter-form', 'Newsletter Form', 'Newsletter Form', function ($shortCode) {
        return Theme::partial('short-codes.newsletter-form', [
            'title'       => $shortCode->title,
            'description' => $shortCode->description,
        ]);
    });

    shortcode()->setAdminConfig('newsletter-form', Theme::partial('short-codes.newsletter-form-admin-config'));
}

if (is_plugin_active('contact')) {
    add_filter(CONTACT_FORM_TEMPLATE_VIEW, function () {
        return Theme::getThemeNamespace() . '::partials.short-codes.contact-form';
    }, 120);
}

if (is_plugin_active('blog')) {
    add_shortcode('featured-news', 'Featured News', 'Featured News', function ($shortCode) {
        return Theme::partial('short-codes.featured-news', [
            'title'       => $shortCode->title,
            'description' => $shortCode->description,
        ]);
    });
    shortcode()->setAdminConfig('featured-news', Theme::partial('short-codes.featured-news-admin-config'));
}
