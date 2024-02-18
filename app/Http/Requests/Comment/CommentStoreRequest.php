<?php

namespace App\Http\Requests\Comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'parentId'  => ['nullable', 'integer', 'exists:comments,id'],
            'postId'    => ['required', 'integer', 'exists:posts,id'],
            'body'      => ['required', 'string', 'max:10000'],
        ];
    }
}
