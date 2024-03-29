<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: "0.1", title: "dev-ocean-api")]
#[OA\Server(url: 'http://172.18.0.1:86/api/')]
#[OA\Components(
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: 'bearerAuth',
            type: 'http',
            name: 'bearerAuth',
            in: 'header',
            bearerFormat: 'JWT',
            scheme: 'bearer',
        )
    ]
)]
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
