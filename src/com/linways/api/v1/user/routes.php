<?php
$app->post('[/]', 'UserController:registerUser');
$app->put('/{id}[/]', 'UserController:editUser');
$app->delete('/{id}[/]', 'UserController:deleteUser');
$app->get('[/]', 'UserController:fetchUser');
$app->get('/{id}[/]', 'UserController:fetchUser');

$app->post('/login[/]', 'UserController:loginUser');
$app->post('/logout[/]', 'UserController:logoutUser');
