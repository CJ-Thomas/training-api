<?php
if(ini_get('zlib.output_compression') === "1"){
    header('Content-Encoding: gzip');
}
include getenv('AMS_CONFIG');
include getenv('NUCLEUS_CONF');
ob_clean();
require '../vendor/autoload.php';
// Constant refers to the base source folder
define('SOURCE_DIR', getcwd() . '/../src/com/linways/api');
// Define directory where template files resides
define('TEMPLATE_DIR', 'template');

$app = new \Slim\App([
    'settings' => [
        // 'displayErrorDetails' => true,
        'debug' =>  getenv('DEBUG') === "true" ,  // change to false in production
        'addContentLengthHeader' => false
    ]
]); // change to false for production

$container = $app->getContainer();

// require __DIR__ . '/config/db.php';
require __DIR__ . '/middlewares.php';
require __DIR__ . '/controllers.php';

// $app->group('/api', function () use ($app) {
    require __DIR__ . '/routes.php';
// });