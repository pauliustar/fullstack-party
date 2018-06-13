<?php
return [
    'settings' => [
        'displayErrorDetails' => false,
        'addContentLengthHeader' => false,


        // Renderer settings
        'renderer' => [
            'views_path' => __DIR__ . '/../views/',
        ],
    ],
    'config' => [
        'github' => __DIR__ . '/../config/github.php',
    ],
];
