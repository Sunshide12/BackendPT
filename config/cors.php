<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',                                                           // Dev local
        'https://test.sunshide.com',                                                      // Dominio personalizado
        'https://frontend-orvwtikys-stebanbusiness-gmailcoms-projects.vercel.app',        // URL de Vercel
    ],
    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',   // Cualquier preview de Vercel
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];