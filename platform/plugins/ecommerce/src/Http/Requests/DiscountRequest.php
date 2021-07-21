<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class DiscountRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'  => 'nullable|required_if:type,promotion|max:255',
            'code'   => 'nullable|required_if:type,coupon|max:255|unique:ec_discounts,code',
            'value'  => 'required|numeric|min:0',
            'target' => 'required',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'title.required_if' => __('Please enter the name of the promotion'),
            'code.required_if'  => __('Please enter the promotion code'),
            'value.required'    => __('Amount must be greater than 0'),
            'target.required'   => __('No object selected for promotion'),
        ];
    }
}
