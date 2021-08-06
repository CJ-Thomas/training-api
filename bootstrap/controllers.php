<?php
    use com\linways\api\v1\helloWorld\controller\HelloWorldController;

    /* Attaching the QUestionController to the Slim application container */
    $container['HelloWorldController'] = function ($container) {
        return new HelloWorldController($container);
};