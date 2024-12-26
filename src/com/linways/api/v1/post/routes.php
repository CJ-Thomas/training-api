<?php
$app->post('[/]', 'PostController:createPost');
$app->put('/{id}[/]', 'PostController:editPost');
$app->delete('/{id}[/]', 'PostController:deletePost');
$app->get('[/]', 'PostController:fetchPost');
$app->get('/{id}[/]', 'PostController:fetchPost');

$app->post('/interact[/]', 'PostController:interactWithPost');
$app->delete('/interact/{id}[/]', 'PostController:interactWithPost');


