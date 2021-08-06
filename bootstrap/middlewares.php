<?php

/*
 * $app and $container are available.
 */

// For showing informative error page in debug mode and hiding implementation details in production
$app->add(new \Linways\Slim\Middleware\LinwaysWhoops());


// $app->add(new \Slim\Middleware\JwtAuthentication([
//     "secret" => "rohithisagreatperson",
//     "algorithm" => ["HS256", "HS384"],
//     "error" => function ($request, $response, $arguments) {
//         return MessageUtils::showError($response, 'authentication-failed', $arguments["message"]);
//      }
// ]));


//$app->add(function (Request $request, Response $response, $next) {
//
//});

/**
 * For adding Content Encoding header if the browser accepts it.
 * This is required if zlib.output_compression is on in php.ini
 * NB: On server output_compression is on.
 */
// $app->add(function ($request, $response, $next) {
//     if ($request->hasHeader('Accept-Encoding') &&
//         stristr($request->getHeaderLine('Accept-Encoding'), 'gzip') === false) {
//         // Browser doesn't accept gzip compression
//         return $next($request, $response);
//     }
//     /** @var Response $response */
//     $response = $next($request, $response);
    
//     if ($response->hasHeader('Content-Encoding')) {
//         return $next($request, $response);
//     }
//     return $response;
//     return $response->withHeader('Content-Encoding', 'gzip');
// });
