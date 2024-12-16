<?php

$app->group('/v1', function () use ($app) {
    require SOURCE_DIR . '/v1/routes.php';
});