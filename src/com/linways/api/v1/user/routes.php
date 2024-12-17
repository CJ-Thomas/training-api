<?php
$app->post('[/]', 'UserController:register');
$app->put('/{id}[/]', 'UserController:edit');
$app->delete('/{id}[/]', 'UserController:delete');
$app->get('[/]', 'UserController:fetch');
$app->get('/{id}[/]', 'UserController:fetch');

$app->post('/login[/]', 'UserController:login');
$app->post('/logout[/]', 'UserController:logout');
