<?php

	namespace com\linways\api\v1\curriculum\controller;

	use Slim\Http\Request;
	use Slim\Http\Response;
	use com\linways\api\v1\BaseController;
	use Linways\Slim\Exception\CoreException;
	use Linways\Slim\Utils\ResponseUtils;

	use stdClass;

	class HelloWorldController extends BaseController
    {
        public $permissions_getHelloWorld = [''];
        
        protected function getHelloWorld ( Request $request, Response $response ) {
            $params = $request->getQueryParams();
            try {
                $init->message = "Hello World!";
                
            } catch (\Exception $e) {
                throw new CoreException('Error occurred while fetching data', "ERROR_OCCURRED");
            }
            return $response->withJson($init);
        }

    }

