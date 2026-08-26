<?php

/**
 * @psalm-import-type Response from types
 * @psalm-import-type Redirect from types
 * @psalm-import-type Dispatcher from types
 */

/**
 * @param Dispatcher $dispatcher
 * 
 * @return array{response: Response, redirect: Redirect}
 */
function createResponse(Closure $dispatcher): array
{
    $response = function (int $httpStatusCode = 200, ?string $content = null) use ($dispatcher): void {
        $response = $content ?? '';

        set_time_limit(0);
        ignore_user_abort(true);

        header('Connection: close');
        header('Content-length: ' . strlen($response));

        http_response_code($httpStatusCode);

        echo $response;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
        }

        $dispatcher();
    };

    $redirect = function (string $to, int $httpStatusCode = 302) use ($dispatcher): void {
        header('Connection: close');
        header('Location: ' . $to, true, $httpStatusCode);

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            flush();
        }

        $dispatcher();
    };

    return [
        'response' => $response,
        'redirect' => $redirect
    ];
}
