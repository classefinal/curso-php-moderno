<?php

declare(strict_types=1);

/**
 * @psalm-import-type Dispatcher from types
 * @psalm-import-type Response from types
 */

/**
 * @param Dispatcher $dispatcher
 * @return Response
 */
function createResponse(Closure $dispatcher): Closure
{
    return function (int $httpStatusCode = 200, ?string $content = null) use ($dispatcher): void {
        $response = ob_get_contents();

        ob_end_clean();

        if ($content) {
            $response .= $content;
        }

        header("Connection: close");
        header("Content-Length: " . strlen($response));
        http_response_code($httpStatusCode);
        echo $response;

        flush();

        $dispatcher();
    };
}
