<?php
    use com\linways\api\v1\helloWorld\controller\HelloWorldController;
use com\linways\api\v1\user\controller\PostController;
use com\linways\api\v1\user\controller\UserController;

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