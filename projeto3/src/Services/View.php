<?php

/**
 * @psalm-import-type View from types
 */

/**
 * @return View
 */
function createView(): Closure
{
    return function (string $viewPath, array $args = []): string {
        ob_start();

        extract($args);

        require_once PAGES . getRequirePath($viewPath . '.php');

        return ob_get_clean();
    };
}
