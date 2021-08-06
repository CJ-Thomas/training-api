<?php

	namespace com\linways\api\v1\curriculum\controller;

	use Slim\Http\Request;
	use Slim\Http\Response;
	use com\linways\api\v1\BaseController;
	use Linways\Slim\Utils\ResponseUtils;

	use stdClass;

	class HelloWorldController extends BaseController
    {
        public $permissions_getHelloWorld = [''];
        
        protected function getHelloWorld ( Request $request, Response $response ) {
            $params = $request->getQueryParams();
            try {
                $data = new \stdClass;
                $data->message = "Hello World!";
                
            } catch (\Exception $e) {
                return ResponseUtils::fault($response,$e);
            }
            return $response->withJson($data);
        }

    }

