<?php
$app->post('[/]', 'CommentController:createComment');
$app->put('/{id}[/]', 'CommentController:editComment');
$app->delete('/{id}[/]', 'CommentController:deleteComment');
$app->get('[/]', 'CommentController:fetchComment');