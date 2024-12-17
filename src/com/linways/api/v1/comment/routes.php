<?php
$app->post('[/]', 'CommentController:create');
$app->put('/{id}[/]', 'CommentController:edit');
$app->delete('/{id}[/]', 'CommentController:delete');
$app->get('[/]', 'CommentController:fetch');