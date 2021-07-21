<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SaveCheckoutInformationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     */
    public function rules()
    {
        $rules = [];

        $rules['address.address_id'] = 'required_without:address.name';
        if (!$this->has('address.address_id') || $this->input('address.address_id') === 'new') {
            $rules['address.name'] = 'required|min:3|max:120';
            $rules['address.phone'] = 'required|numeric';
            $rules['address.email'] = 'required|email';
            $rules['address.state'] = 'required';
            $rules['address.city'] = 'required';
            $rules['address.address'] = 'required|string';
        }

        if ($this->input('create_account') == 1) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|same:password';
            $rules['address.email'] = 'required|max:60|min:6|email|unique:ec_customers,email';
            $rules['address.name'] = 'required|min:3|max:120';
        }

        return $rules;
    }

    /**
     * @return array
     *
     */
    public function messages()
    {
        $messages = [
            'address.name.required'    => __('The name field is required.'),
            'address.phone.required'   => __('The phone field is required.'),
            'address.email.required'   => __('The email field is required.'),
            'address.email.unique'     => __('The customer with that email is existed, please choose other email or login with this email!'),
            'address.state.required'   => __('The state field is required.'),
            'address.city.required'    => __('The city field is required.'),
            'address.address.required' => __('The address field is required.'),
        ];

        return array_merge(parent::messages(), $messages);
    }
}
