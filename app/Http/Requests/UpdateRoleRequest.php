<?php

namespace App\Http\Requests;

use App\DTO\RoleDTO;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'update-role')) 
                    return true;
        }
        return abort(403,'update-role permission required');
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
           
           
            'name'           => ['string',  Rule::unique('roles','name')->ignore($this->route('id'))],
            'description'    => ['string'],
            'cipher'         => ['string',  Rule::unique('roles','cipher')->ignore($this->route('id'))],
        ];
    }
}
