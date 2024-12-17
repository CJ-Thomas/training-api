<?php

// $app->group('/helloWorld', function () use ($app) {
//     require SOURCE_DIR . '/v1/helloWorld/routes.php';
// });

$app->group('/user', function () use ($app) {
    require_once SOURCE_DIR . '/v1/user/routes.php';
});

$app->group('/post', function () use ($app) {
    require_once SOURCE_DIR . '/v1/post/routes.php';
});

$app->group('/comment', function () use ($app) {
    require_once SOURCE_DIR . '/v1/comment/routes.php';
});