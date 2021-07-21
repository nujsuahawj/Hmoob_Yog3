<div class="list-photo-hover-overlay">
    <ul class="photo-overlay-actions">
        <li>
            <a class="mr10 btn-trigger-edit-product-image" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ __('Change image') }}">
                <i class="fa fa-edit"></i>
            </a>
        </li>
        <li>
            <a class="mr10 btn-trigger-remove-product-image" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ __('Delete image') }}">
                <i class="fa fa-trash"></i>
            </a>
        </li>
    </ul>
</div>
<div class="custom-image-box image-box">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="image-data">
    <img src="{{ $thumb }}" alt="preview image" class="preview_image">
    <div class="image-box-actions">
        <a class="btn-images" data-result="{{ $name }}" data-action="{{ $attributes['action'] ?? 'select-image' }}">
            {{ trans('core/base::forms.choose_image') }}
        </a> |
        <a class="btn_remove_image">
            <span></span>
        </a>
    </div>
</div>
