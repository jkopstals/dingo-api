<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\User;

class UserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return User::getRules();
    }

    /**
     * Get the input param names that can be passed to User create method
     *
     * @return array
     */
    public function acceptableParams()
    {
        return array_keys(User::getRules());
    }
}
