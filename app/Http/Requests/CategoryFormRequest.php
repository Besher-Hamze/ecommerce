<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryFormRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'eng_name' => [
                'required',
                'string'
            ],
            'eng_meta_keyword' => [
                'nullable',
                'string'
            ],
            'eng_meta_description' => [
                'nullable',
                'string'
            ],
           
            'eng_meta_keyword' => [
                'nullable',
                'string'
            ],
            'eng_meta_description' => [
                'nullable',
                'string'
            ],
            'name' => [
                'required',
                'string'
            ],
           
            'description' => ['required'],
            'image' => ['nullable'],
           
            
        ];
    }
}
