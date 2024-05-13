<?php

namespace App\Http\Requests;

use App\DTO\PermissionDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
           
           
            'name'           => ['string',  Rule::unique('permissions','name')->ignore($this->route('id'))],
            'description'    => ['string'],
            'cipher'         => ['string',  Rule::unique('permissions','cipher')->ignore($this->route('id'))],
        ];
    }
}
