<?php

namespace App\Http\Resources\SwaggerSchemes;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use OpenApi\Attributes as OA;

class BearerResource extends JsonResource
{
    public static $wrap = 'Bearer';

    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'Bearer',
        description: 'Shows Bearer token',
        properties: [
            new OA\Property(
                property: 'accessToken',
                description: 'Bearer token',
                type: 'string',
            ),
            new OA\Property(
                property: 'token',
                description: 'Token info',
                properties: [
                    new OA\Property(
                        property: 'id',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'user_id',
                        type: 'integer',
                    ),
                    new OA\Property(
                        property: 'client_id',
                        type: 'integer',
                    ),
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'scopes',
                        type: 'array',
                        items: new OA\Items(
                            properties: [],
                        ),
                    ),
                    new OA\Property(
                        property: 'revoked',
                        type: 'bool',
                    ),
                    new OA\Property(
                        property: 'created_at',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'updated_at',
                        type: 'string',
                    ),
                    new OA\Property(
                        property: 'expires_at',
                        type: 'string',
                    ),
                ],
                type: 'object'
            ),
        ]
    )]
    public function toArray(Request $request): array
    {
        /** @var PersonalAccessTokenResult $resource */
        $resource = $this->resource;

        return [
            'accessToken' => $resource->accessToken,
            'token' => [
                'id'            => $resource->token->id,
                'user_id'       => $resource->token->user_id,
                'client_id'     => $resource->token->client_id,
                'name'          => $resource->token->name,
                'scopes'        => $resource->token->scopes,
                'revoked'       => $resource->token->revoked,
                'created_at'    => $resource->token->created_at,
                'updated_at'    => $resource->token->updated_at,
                'expires_at'    => $resource->token->expires_at,
            ]
        ];
    }
}
