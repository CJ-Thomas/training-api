<?php

use com\linways\api\v1\comment\controller\CommentController;
use com\linways\api\v1\post\controller\PostController;
use com\linways\api\v1\user\controller\UserController;

// use com\linways\api\v1\helloWorld\controller\HelloWorldController;
//     /* Attaching the QUestionController to the Slim application container */
//     $container['HelloWorldController'] = function ($container) {
//         return new HelloWorldController($container);
// };

$container['UserController'] = function ($container) {
    return new UserController($container);
};

$container['PostController'] = function ($container) {
    return new PostController($container);
};

$container['CommentController'] = function ($container) {
    return new CommentController($container);
};