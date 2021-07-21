class EcommerceProduct {
    constructor() {
        this.$body = $('body');

        this.initElements();
        this.handleEvents();
        this.handleChangeSaleType();
        this.handleShipping();
        this.handleStorehouse();

        this.handleModifyAttributeSets();
        this.handleAddVariations();
        this.handleDeleteVariations();
    }

    handleEvents() {
        let _self = this;

        _self.$body.on('click', '.select-all', event => {
            event.preventDefault();
            let $select = $($(event.currentTarget).attr('href'));
            $select.find('option').attr('selected', true);
            $select.trigger('change');
        });

        _self.$body.on('click', '.deselect-all', event => {
            event.preventDefault();
            let $select = $($(event.currentTarget).attr('href'));
            $select.find('option').removeAttr('selected');
            $select.trigger('change');
        });

        _self.$body.on('change', '#attribute_sets', event => {
            let $groupContainer = $('#attribute_set_group');

            let value = $(event.currentTarget).val();

            $groupContainer.find('.panel').hide();

            if (value) {
                _.forEach(value, (value) => {
                    $groupContainer.find('.panel[data-id="' + value + '"]').show()
                })
            }
            $('.select2-select').select2();
        });
        $('#attribute_sets').trigger('change');

        _self.$body.on('change', '.is-variation-default input', event => {
            let $current = $(event.currentTarget);
            let isChecked = $(event.currentTarget).is(':checked');
            $('.is-variation-default input').prop('checked', false);
            if (isChecked) {
                $current.prop('checked', true);
            }
        })
    }

    initElements() {
        $('.select2-select').select2();

        $('.form-date-time').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            toolbarPlacement: 'bottom',
            showTodayButton: true,
            stepping: 1
        });

        $('#attribute_set_group .panel-collapse').on('shown.bs.collapse', () => {
            $('.select2-select').select2();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', () => {
            $('.select2-select').select2();
        });

        $('.list-gallery-media-images').each((index, item) => {
            let $current = $(item);
            if ($current.data('ui-sortable')) {
                $current.sortable('destroy');
            }
            $current.sortable();
        });
    }

    handleChangeSaleType() {
        let _self = this;

        _self.$body.on('click', '.turn-on-schedule', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);
            let $group = $current.closest('.price-group');
            $current.addClass('hidden');
            $group.find('.turn-off-schedule').removeClass('hidden');
            $group.find('.detect-schedule').val(1);
            $group.find('.scheduled-time').removeClass('hidden');
        });
        _self.$body.on('click', '.turn-off-schedule', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);
            let $group = $current.closest('.price-group');
            $current.addClass('hidden');
            $group.find('.turn-on-schedule').removeClass('hidden');
            $group.find('.detect-schedule').val(0);
            $group.find('.scheduled-time').addClass('hidden');
        })
    }

    handleStorehouse() {
        let _self = this;

        _self.$body.on('click', 'input.storehouse-management-status', event => {
            let $storehouseInfo = $('.storehouse-info');
            if ($(event.currentTarget).prop('checked') === true) {
                $storehouseInfo.removeClass('hidden');
            } else {
                $storehouseInfo.addClass('hidden');
            }
        })
    }

    handleShipping() {
        let _self = this;

        _self.$body.on('click', '.change-measurement .dropdown-menu a', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);
            let $parent = $current.closest('.change-measurement');
            let $input = $parent.find('input[type=hidden]');
            $input.val($current.attr('data-alias'));
            $parent.find('.dropdown-toggle .alias').html($current.attr('data-alias'));
        })
    }

    handleModifyAttributeSets() {
        let _self = this;

        _self.$body.on('click', '#store-related-attributes-button', event => {
            event.preventDefault();

            let $current = $(event.currentTarget);

            let attributeSets = [];
            $current.closest('.modal-content').find('.attribute-set-item:checked').each((index, item) => {
                attributeSets[index] = $(item).val();
            });

            $.ajax({
                url: $current.data('target'),
                type: 'POST',
                data: {
                    'attribute_sets': attributeSets,
                },
                beforeSend: () => {
                    $current.addClass('button-loading');
                },
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        Botble.showSuccess(res.message);

                        $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', () => {
                            _self.initElements();
                        });
                        $('#select-attribute-sets-modal').modal('hide');
                    }
                    $current.removeClass('button-loading');
                },
                complete: () => {
                    $current.removeClass('button-loading');
                },
                error: data => {
                    Botble.handleError(data);
                    $current.removeClass('button-loading');
                },
            })
        })
    }

    handleAddVariations() {
        let _self = this;

        let createOrUpdateVariation = ($current) => {
            let formData = $current.closest('.modal-content').find('.variation-form-wrapper').find('select,textarea,input').serialize();

            $.ajax({
                url: $current.data('target'),
                type: 'POST',
                data: formData,
                beforeSend: () => {
                    $current.addClass('button-loading');
                },
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        Botble.showSuccess(res.message);
                        $current.closest('.modal.fade').modal('hide');

                        $('#product-variations-wrapper').load(window.location.href + ' #product-variations-wrapper > *', () => {
                            _self.initElements()
                        })
                    }
                    $current.removeClass('button-loading');
                },
                complete: () => {
                    $current.removeClass('button-loading');
                },
                error: data => {
                    Botble.handleError(data);
                    $current.removeClass('button-loading');
                },
            })
        };

        _self.$body.on('click', '#store-product-variation-button', event => {
            event.preventDefault();
            createOrUpdateVariation($(event.currentTarget));
        });

        _self.$body.on('click', '#update-product-variation-button', event => {
            event.preventDefault();
            createOrUpdateVariation($(event.currentTarget));
        });

        _self.$body.on('click', '#generate-all-versions-button', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);

            $.ajax({
                url: $current.data('target'),
                type: 'POST',
                beforeSend: () => {
                    $current.addClass('button-loading');
                },
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        Botble.showSuccess(res.message);

                        $('#generate-all-versions-modal').modal('hide');
                        $('#product-variations-wrapper').load(window.location.href + ' #product-variations-wrapper > *', () => {
                            _self.initElements()
                        })
                    }
                    $current.removeClass('button-loading');
                },
                complete: () => {
                    $current.removeClass('button-loading');
                },
                error: data => {
                    Botble.handleError(data);
                    $current.removeClass('button-loading');
                },
            })
        });

        $(document).on('click', '.btn-trigger-edit-product-version', event => {
            event.preventDefault();
            $('#update-product-variation-button').data('target', $(event.currentTarget).data('target'));
            let $current = $(event.currentTarget);
            $.ajax({
                url: $current.data('load-form'),
                type: 'GET',
                beforeSend: () => {
                    $current.addClass('button-loading');
                },
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $('#edit-product-variation-modal .modal-body').html(res.data);
                        _self.initElements();
                        Botble.initResources();
                        $('#edit-product-variation-modal').modal('show');
                    }
                    $current.removeClass('button-loading');
                },
                complete: () => {
                    $current.removeClass('button-loading');
                },
                error: data => {
                    $current.removeClass('button-loading');
                    Botble.handleError(data);
                },
            })
        });

        _self.$body.on('click', '.btn-trigger-add-attribute-to-simple-product', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);

            let added_attributes = [];
            let added_attribute_sets = [];

            $.each($('.list-product-attribute-wrap-detail .product-attribute-set-item'), (index, el) => {
                if (!$(el).hasClass('hidden')) {
                    if ($(el).find('.product-select-attribute-item-value').val() !== '') {
                        added_attributes.push($(el).find('.product-select-attribute-item-value').val());
                        added_attribute_sets.push($(el).find('.product-select-attribute-item-value').data('set-id'));
                    }
                }
            });

            if (added_attributes.length) {
                $.ajax({
                    url: $current.data('target'),
                    type: 'POST',
                    data: {
                        added_attributes: added_attributes,
                        added_attribute_sets: added_attribute_sets,
                    },
                    beforeSend: () => {
                        $current.addClass('button-loading');
                    },
                    success: res => {
                        if (res.error) {
                            Botble.showError(res.message);
                        } else {
                            $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', () => {
                                _self.initElements();
                            });
                            $('#confirm-delete-version-modal').modal('hide');
                            Botble.showSuccess(res.message);
                        }
                        $current.removeClass('button-loading');
                    },
                    complete: () => {
                        $current.removeClass('button-loading');
                    },
                    error: data => {
                        $current.removeClass('button-loading');
                        Botble.handleError(data);
                    },
                });
            } else {
                Botble.showError('Không có thuộc tính nào được chọn!');
            }
        });
    }

    handleDeleteVariations() {
        let _self = this;

        $(document).on('click', '.btn-trigger-delete-version', event => {
            event.preventDefault();
            $('#delete-version-button').data('target', $(event.currentTarget).data('target'));
            $('#confirm-delete-version-modal').modal('show');
        });

        _self.$body.on('click', '#delete-version-button', event => {
            event.preventDefault();
            let $current = $(event.currentTarget);

            $.ajax({
                url: $current.data('target'),
                type: 'POST',
                beforeSend: () => {
                    $current.addClass('button-loading');
                },
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $('#main-manage-product-type').load(window.location.href + ' #main-manage-product-type > *', () => {
                            _self.initElements()
                        });
                        $('#confirm-delete-version-modal').modal('hide');
                        Botble.showSuccess(res.message);
                    }
                    $current.removeClass('button-loading');
                },
                complete: () => {
                    $current.removeClass('button-loading');
                },
                error: data => {
                    $current.removeClass('button-loading');
                    Botble.handleError(data);
                },
            })
        })
    }
}

$(window).on('load', () => {
    new EcommerceProduct();

    $('body').on('click', '.list-gallery-media-images .btn_remove_image', event => {
        event.preventDefault();
        $(event.currentTarget).closest('li').remove();
    });

    $(document).on('click', '.btn-trigger-remove-product-image', event => {
        event.preventDefault();
        $(event.currentTarget).closest('.product-image-item-handler').remove();
        if ($('.list-gallery-media-images').find('.product-image-item-handler').length === 0) {
            $('.default-placeholder-product-image').removeClass('hidden');
        }
    });

    $(document).on('click', '.btn-trigger-select-product-attributes', event => {
        event.preventDefault();
        $('#store-related-attributes-button').data('target', $(event.currentTarget).data('target'));
        $('#select-attribute-sets-modal').modal('show');
    });

    $(document).on('click', '.btn-trigger-add-new-product-variation', event => {
        event.preventDefault();
        $('#store-product-variation-button').data('target', $(event.currentTarget).data('target'));
        $('#add-new-product-variation-modal').modal('show');
    });

    $(document).on('click', '.btn-trigger-generate-all-versions', event => {
        event.preventDefault();
        $('#generate-all-versions-button').data('target', $(event.currentTarget).data('target'));
        $('#generate-all-versions-modal').modal('show');
    });

    $(document).on('click', '.btn-trigger-add-attribute', event => {
        event.preventDefault();
        $('.list-product-attribute-wrap').toggleClass('hidden');
        $(event.currentTarget).toggleClass('adding_attribute_enable');

        if ($(event.currentTarget).hasClass('adding_attribute_enable')) {
            $('#is_added_attributes').val(1);
        } else {
            $('#is_added_attributes').val(0);
        }

        let toggle_text = $(event.currentTarget).data('toggle-text');
        $(event.currentTarget).data('toggle-text', $(event.currentTarget).text());
        $(event.currentTarget).text(toggle_text);
    });


    $(document).on('change', '.product-select-attribute-item', () => {

        let selected_items = [];

        $.each($('.product-select-attribute-item'), (index, el) => {
            if ($(el).val() !== '') {
                selected_items.push(index);
            }
        });

        if (selected_items.length) {
            $('.btn-trigger-add-attribute-to-simple-product').removeClass('hidden');
        } else {
            $('.btn-trigger-add-attribute-to-simple-product').addClass('hidden');
        }
    });

    let handleChangeAttributeSet = () => {
        $.each($('.product-attribute-set-item:visible .product-select-attribute-item option'), (index, el) => {
            if ($(el).prop('value') !== $(el).closest('select').val()) {
                if ($('.list-product-attribute-wrap-detail .product-select-attribute-item-value-id-' + $(el).prop('value')).length === 0) {
                    $(el).prop('disabled', false);
                } else {
                    $(el).prop('disabled', true);
                }
            }
        });
    };
    $(document).on('change', '.product-select-attribute-item', event => {
        $(event.currentTarget).closest('.product-attribute-set-item').find('.product-select-attribute-item-value-wrap').html($('.list-product-attribute-values-wrap .product-select-attribute-item-value-wrap-' + $(event.currentTarget).val()).html());
        $(event.currentTarget).closest('.product-attribute-set-item').find('.product-select-attribute-item-value-id-' + $(event.currentTarget).val()).prop('name', 'added_attributes[' + $(event.currentTarget).val() + ']');
        handleChangeAttributeSet();
    });

    $(document).on('click', '.btn-trigger-add-attribute-item', event => {
        event.preventDefault();
        let $template = $('.list-product-attribute-values-wrap .product-select-attribute-item-template');
        let selected_value = null;
        $.each($('.product-attribute-set-item:visible .product-select-attribute-item option'), (index, el) => {
            if ($(el).prop('value') !== $(el).closest('select').val() && $(el).prop('disabled') === false) {
                $template.find('.product-select-attribute-item-value-wrap').html($('.list-product-attribute-values-wrap .product-select-attribute-item-value-wrap-' + $(el).prop('value')).html());
                selected_value = $(el).prop('value');
            }
        });

        let $list_detail_wrap = $('.list-product-attribute-wrap-detail');

        $list_detail_wrap.append($template.html());
        $list_detail_wrap.find('.product-attribute-set-item:last-child .product-select-attribute-item').val(selected_value);
        $list_detail_wrap.find('.product-select-attribute-item-value-id-' + selected_value).prop('name', 'added_attributes[' + selected_value + ']');

        if ($list_detail_wrap.find('.product-attribute-set-item').length === $('.list-product-attribute-values-wrap .product-select-attribute-item-wrap-template').length) {
            $(event.currentTarget).addClass('hidden');
        }

        $('.product-set-item-delete-action').removeClass('hidden');

        handleChangeAttributeSet();
    });

    $(document).on('click', '.product-set-item-delete-action a', event => {
        event.preventDefault();
        $(event.currentTarget).closest('.product-attribute-set-item').remove();
        let $list_product_attribute_wrap = $('.list-product-attribute-wrap-detail');
        if ($list_product_attribute_wrap.find('.product-attribute-set-item').length < 2) {
            $('.product-set-item-delete-action').addClass('hidden');
        }

        if ($list_product_attribute_wrap.find('.product-attribute-set-item').length < $('.list-product-attribute-values-wrap .product-select-attribute-item-wrap-template').length) {
            $('.btn-trigger-add-attribute-item').removeClass('hidden');
        }
        handleChangeAttributeSet();
    });

    new RvMediaStandAlone('.js-btn-trigger-add-image', {
        onSelectFiles: (files, $el) => {
            let $currentBoxList = $el.closest('.product-images-wrapper').find('.images-wrapper .list-gallery-media-images');

            $currentBoxList.removeClass('hidden');

            $('.default-placeholder-product-image').addClass('hidden');

            _.forEach(files, (file) => {
                let template = $(document).find('#product_select_image_template').html();

                let imageBox = template
                    .replace(/__name__/gi, $el.attr('data-name'));

                let $template = $('<li class="product-image-item-handler">' + imageBox + '</li>');

                $template.find('.image-data').val(file.url);
                $template.find('.preview_image').attr('src', file.thumb).show();

                $currentBoxList.append($template);
            });
        }
    });

    new RvMediaStandAlone('.images-wrapper .btn-trigger-edit-product-image', {
        onSelectFiles: (files, $el) => {
            let firstItem = _.first(files);

            let $currentBox = $el.closest('.product-image-item-handler').find('.image-box');
            let $currentBoxList = $el.closest('.list-gallery-media-images');

            $currentBox.find('.image-data').val(firstItem.url);
            $currentBox.find('.preview_image').attr('src', firstItem.thumb).show();

            _.forEach(files, (file, index) => {
                if (!index) {
                    return;
                }
                let template = $(document).find('#product_select_image_template').html();

                let imageBox = template
                    .replace(/__name__/gi, $currentBox.find('.image-data').attr('name'));

                let $template = $('<li class="product-image-item-handler">' + imageBox + '</li>');

                $template.find('.image-data').val(file.url);
                $template.find('.preview_image').attr('src', file.thumb).show();

                $currentBoxList.append($template);
            });
        }
    });

    $(document).on('click', '.list-search-data .selectable-item', event => {
        event.preventDefault();
        let _self = $(event.currentTarget);
        let $input = _self.closest('.form-group').find('input[type=hidden]');

        let existed_values = $input.val().split(',');
        $.each(existed_values, (index, el) => {
            existed_values[index] = parseInt(el);
        });

        if ($.inArray(_self.data('id'), existed_values) < 0) {
            if ($input.val()) {
                $input.val($input.val() + ',' + _self.data('id'));
            } else {
                $input.val(_self.data('id'));
            }

            let template = $(document).find('#selected_product_list_template').html();

            let productItem = template
                .replace(/__name__/gi, _self.data('name'))
                .replace(/__id__/gi, _self.data('id'))
                .replace(/__url__/gi, _self.data('url'))
                .replace(/__image__/gi, _self.data('image'))
                .replace(/__attributes__/gi, _self.find('a span').text());
            _self.closest('.form-group').find('.list-selected-products').removeClass('hidden');
            _self.closest('.form-group').find('.list-selected-products table tbody').append(productItem);
        }
        _self.closest('.panel').addClass('hidden');
    });

    $(document).on('click', '.textbox-advancesearch', event => {
        let _self = $(event.currentTarget);
        let $form_body = _self.closest('.box-search-advance').find('.panel');
        $form_body.removeClass('hidden');
        $form_body.addClass('active');
        if ($form_body.find('.panel-body').length === 0) {
            Botble.blockUI({
                target: $form_body,
                iconOnly: true,
                overlayColor: 'none'
            });

            $.ajax({
                url: _self.data('target'),
                type: 'GET',
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $form_body.html(res.data);
                        Botble.unblockUI($form_body);
                    }
                },
                error: data => {
                    Botble.handleError(data);
                    Botble.unblockUI($form_body);
                },
            });
        }
    });

    $(document).on('keyup', '.textbox-advancesearch', event => {
        let _self = $(event.currentTarget);
        let $form_body = _self.closest('.box-search-advance').find('.panel');
        setTimeout(() => {
            Botble.blockUI({
                target: $form_body,
                iconOnly: true,
                overlayColor: 'none'
            });

            $.ajax({
                url: _self.data('target') + '?keyword=' + _self.val(),
                type: 'GET',
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $form_body.html(res.data);
                        Botble.unblockUI($form_body);
                    }
                },
                error: data => {
                    Botble.handleError(data);
                    Botble.unblockUI($form_body);
                },
            });
        }, 500);
    });

    $(document).on('click', '.box-search-advance .page-link', event => {
        event.preventDefault();
        let _self = $(event.currentTarget);
        if (!_self.closest('.page-item').hasClass('disabled') && _self.prop('href')) {
            let $form_body = _self.closest('.box-search-advance').find('.panel');
            Botble.blockUI({
                target: $form_body,
                iconOnly: true,
                overlayColor: 'none'
            });

            $.ajax({
                url: _self.prop('href') + '&keyword=' + _self.val(),
                type: 'GET',
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $form_body.html(res.data);
                        Botble.unblockUI($form_body);
                    }
                },
                error: data => {
                    Botble.handleError(data);
                    Botble.unblockUI($form_body);
                },
            });
        }
    });

    $(document).on('click', 'body', (e) => {
        let container = $('.box-search-advance');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.find('.panel').addClass('hidden');
        }
    });

    $(document).on('click', '.btn-trigger-remove-selected-product', event => {
        event.preventDefault();
        let $input = $(event.currentTarget).closest('.form-group').find('input[type=hidden]');

        let existed_values = $input.val().split(',');
        $.each(existed_values, (index, el) => {
            el = el.trim();
            if (!_.isEmpty(el)) {
                existed_values[index] = parseInt(el);
            }
        });
        let index = existed_values.indexOf($(event.currentTarget).data('id'));
        if (index > -1) {
            existed_values.splice(index, 1);
        }

        $input.val(existed_values.join(','));

        if ($(event.currentTarget).closest('tbody').find('tr').length < 2) {
            $(event.currentTarget).closest('.list-selected-products').addClass('hidden');
        }
        $(event.currentTarget).closest('tr').remove();
    });

    let loadRelationBoxes = () => {
        let $wrap_body = $('.wrap-relation-product');
        if ($wrap_body.length) {
            Botble.blockUI({
                target: $wrap_body,
                iconOnly: true,
                overlayColor: 'none'
            });

            $.ajax({
                url: $wrap_body.data('target'),
                type: 'GET',
                success: res => {
                    if (res.error) {
                        Botble.showError(res.message);
                    } else {
                        $wrap_body.html(res.data);
                        Botble.unblockUI($wrap_body);
                    }
                },
                error: data => {
                    Botble.handleError(data);
                    Botble.unblockUI($wrap_body);
                },
            });
        }
    };

    loadRelationBoxes();
});

