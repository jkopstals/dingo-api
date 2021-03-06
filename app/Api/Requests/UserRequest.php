<?php

namespace Api\Requests;

use Dingo\Api\Http\FormRequest;
use App\Models\User;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorised to make this request.
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
        if ( ($this->route()->uri() == '/api/users/{id}') && 
             $this->route()->parameter('id') )
        {
            return User::getUpdateRules($this->route()->parameter('id'));
            //slightly sketchy way to decide if store or update is called
        }
        return User::getRules();
    }

    /**
     * Get the input parameter names that can be passed to User create method
     *
     * @return array
     */
    public function fillable()
    {
        $accept = array_keys(User::getRules());
        if (isset($accept['password_confirmation'])) {
            unset($accept['password_confirmation']);
        }
        return $accept;
    }
}
