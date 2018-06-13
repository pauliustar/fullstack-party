<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'configGithub' => __DIR__ . '/../config/github.php',

        // Renderer settings
        'renderer' => [
            'views_path' => __DIR__ . '/../views/',
        ],
    ],
];
