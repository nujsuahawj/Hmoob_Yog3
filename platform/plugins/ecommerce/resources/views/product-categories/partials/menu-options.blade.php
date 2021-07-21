@if (!empty($productCategories))
    <div class="widget meta-boxes">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse_product_categories">
            <h4 class="widget-title" style="margin-top: 0">
                <span>{{ trans('plugins/ecommerce::product-categories.menu') }}</span>
                <i class="fa fa-angle-down narrow-icon"></i>
            </h4>
        </a>
        <div id="collapse_product_categories" class="panel-collapse collapse">
            <div class="widget-body">
                <div class="box-links-for-menu">
                    <div class="the-box">
                        <ul class="list-item">
                            {!! $productCategories !!}
                        </ul>
                        <div class="text-right">
                            <div class="btn-group btn-group-devided">
                                <a href="#" class="btn-add-to-menu btn btn-primary">
                                    <span class="text"><i class="fa fa-plus"></i> {{ trans('packages/menu::menu.add_to_menu') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
