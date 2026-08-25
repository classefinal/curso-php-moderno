<?php

/**
 * @psalm-import-type Response from Types
 * @psalm-import-type Redirect from Types
 * @psalm-import-type Dispatcher from Types
 */

/**
 * @param Dispatcher $dispatcher
 * 
 * @return array{response: Response, redirect: Redirect}
 */
function createResponse(Closure $dispatcher): array
{
    $response = function (int $httpStatusCode = 200, ?string $content = null) use ($dispatcher): void {
        $response = ob_get_contents();

        ob_end_clean();

        if ($content) {
            $response .= $content;
        }

        header('Connection: close');
        header('Content-length: ' . strlen($response));

        http_response_code($httpStatusCode);

        echo $response;

        flush();

        $dispatcher();
    };

    $redirect = function (string $to, int $httpStatusCode = 302) use ($dispatcher): void {
        ob_clean();

        header('Connection: close');
        header('Location: ' . $to, true, $httpStatusCode);

        flush();

        $dispatcher();
    };

    return [
        'response' => $response,
        'redirect' => $redirect
    ];
}
