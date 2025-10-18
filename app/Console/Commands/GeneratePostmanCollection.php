<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class GeneratePostmanCollection extends Command
{
    protected $signature = 'generate:postman';
    protected $description = 'Generate a Postman collection for API routes';

    public function handle()
    {
        $routes = Route::getRoutes();
        $collection = [
            'info' => [
                '_postman_id' => uniqid(),
                'name' => 'Laravel API',
                'description' => 'To get the auth_token, you need to call the api/auth/login or api/auth/register endpoint. The token will be in the response. You can then set it as a variable in Postman.',
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => [],
        ];

        foreach ($routes as $route) {
            if (strpos($route->uri(), 'api/') === 0) {
                $methods = $route->methods();
                // Skip HEAD requests
                if (in_array('HEAD', $methods)) {
                    continue;
                }
                $method = $methods[0];

                $item = [
                    'name' => $route->uri(),
                                    'request' => [
                                        'method' => $method,
                                        'header' => [
                                            [
                                                'key' => 'Authorization',
                                                'value' => 'Bearer {{auth_token}}',
                                                'type' => 'text'
                                            ]
                                        ],
                                        'url' => [                            'raw' => '{{base_url}}/' . $route->uri(),
                            'host' => ['{{base_url}}'],
                            'path' => explode('/', $route->uri()),
                        ],
                    ],
                    'response' => [],
                ];

                if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
                    $item['request']['body'] = [
                        'mode' => 'raw',
                        'raw' => json_encode([], JSON_PRETTY_PRINT),
                        'options' => [
                            'raw' => [
                                'language' => 'json',
                            ],
                        ],
                    ];
                }

                $collection['item'][] = $item;
            }
        }

        $this->info('Postman collection generated successfully.');
        file_put_contents('api_postman_collection.json', json_encode($collection, JSON_PRETTY_PRINT));
    }
}
