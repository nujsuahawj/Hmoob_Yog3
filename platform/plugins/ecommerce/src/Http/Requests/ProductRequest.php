<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;
use Carbon\Carbon;

class ProductRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'       => 'required',
            'price'      => 'numeric|nullable',
            'start_date' => 'date|nullable|required_if:sale_type,1',
            'end_date'   => 'date|nullable|after:' . ($this->input('start_date') ?? Carbon::now(config('app.timezone'))
                        ->toDateTimeString()),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'          => __('Please enter product\'s name'),
            'sale_price.max'         => __('The discount must be less than the original price'),
            'sale_price.required_if' => __('Must enter a discount when you want to schedule a promotion'),
            'end_date.after'         => __('End date must be after start date'),
            'start_date.required_if' => __('Discount start date cannot be left blank when scheduling is selected'),
            'sale_price'             => __('Discounts cannot be left blank when scheduling is selected'),
        ];
    }
}
