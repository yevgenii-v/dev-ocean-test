<?php

namespace App\Http\Requests\Comment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class CommentStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    #[OA\RequestBody(
        request: 'CommentStoreRequest',
        required: true,
        content: new OA\JsonContent(
            examples: [
                new OA\Examples(
                    example: 'No parent ID',
                    summary: '',
                    value: [
                        'parentId'      => null,
                        'postId'        => 2,
                        'body'          => 'Donec eu mattis ligula, sit amet vehicula tortor.',
                    ],
                ),
                new OA\Examples(
                    example: 'With parent ID',
                    summary: '',
                    value: [
                        'parentId'      => 6,
                        'postId'        => 2,
                        'body'          => 'Donec eu mattis ligula, sit amet vehicula tortor.',
                    ],
                ),
            ],
            properties: [
                new OA\Property(
                    property: 'parentId',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'postId',
                    type: 'integer',
                ),
                new OA\Property(
                    property: 'body',
                    type: 'string',
                ),
            ],
            type: 'object',
        ),
    )]
    public function rules(): array
    {
        return [
            'parentId'  => ['nullable', 'integer', 'exists:comments,id'],
            'postId'    => ['required', 'integer', 'exists:posts,id'],
            'body'      => ['required', 'string', 'max:10000'],
        ];
    }
}
