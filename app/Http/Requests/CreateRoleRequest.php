<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'create-role')) 
                    return true;
        }
        return abort(403,'create-role permission required');
    
    }


    public function toRoleDTO() : RoleDTO
    {

        return new RoleDTO(
            $this->input('name'),
            $this->input('description'),
            $this->input('cipher'),
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'           => ['required' , 'unique:roles' , 'string'],
            'description'    => ['required', 'string'],
            'cipher'         => ['required',  'unique:roles' , 'string'],
        ];
    }
}
