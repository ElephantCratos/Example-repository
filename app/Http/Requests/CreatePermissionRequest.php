<?php

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class CreatePermissionRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'create-permission')) 
                    return true;
        }
        return abort(403,'create-permission permission required');
    }


    public function toPermissionDTO() : PermissionDTO
    {

        return new PermissionDTO(
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
            'name'           => ['required' , 'unique:permissions' , 'string'],
            'description'    => ['required', 'string'],
            'cipher'         => ['required',  'unique:permissions' , 'string'],
        ];
    }
}
