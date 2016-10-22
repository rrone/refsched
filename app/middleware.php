<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));
        return $response->withRedirect((string)$uri, 301);
    }

    return $next($request, $response);
});

$app->add(function (Request $request, Response $response, callable $next) {

//    print_r('pre');
//    var_dump($request);
//    print_r('*******************************************<br>');

    $token = getenv('WP_TOKEN_RS');

    $request = $request->withHeader("Authorization", "Basic " . $token);

//    print_r('token');
//    var_dump($request->getHeader('Authorization'));
//    print_r('*******************************************<br>');

//    print_r('post');
//    var_dump($request);die();

    return $next($request, $response);

});
