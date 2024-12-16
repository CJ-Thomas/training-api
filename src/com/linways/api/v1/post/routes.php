<?php
    $app->post('[/]', 'PostController:create');
    $app->get('[/]', 'PostController:fetch');
    $app->get('/{id}[/]', 'PostController:fetch');
    $app->put('/{id}[/]', 'PostController:edit');
    $app->delete('/{id}[/]', 'PostController:delete');