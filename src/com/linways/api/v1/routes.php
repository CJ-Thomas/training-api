<?php
    
    $app->group('/helloWorld', function () use ($app) {
        require SOURCE_DIR . '/v1/helloWorld/routes.php';
    });