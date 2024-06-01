<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class GetListLogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userRoles = Auth::user()->roles;
        foreach ($userRoles as $role)
        {
            if ($role->permissions->contains('name', 'get-list-logs')) 
                    return true;
        }
        return abort(403,'create-role permission required');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {   try{
        return [
                'sortBy' => 'array',
                'filter' => 'array',
                'page' => 'integer',
                'count' => 'integer'
        ];
    }catch (\Exception $e){ dd($e->getMessage());}
    }
}
