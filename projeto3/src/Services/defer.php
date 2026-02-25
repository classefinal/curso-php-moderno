<?php

declare(strict_types=1);

/**
 * @psalm-import-type DeferConfig from types
 */

/**
 * @return DeferConfig
 */
function createDefer(): array
{
    $deferrableActions = [];

    $dispatcher = function () use (&$deferrableActions) {
        array_walk($deferrableActions, fn(Closure $action) => $action());
    };

    $defer = function (Closure $action) use (&$deferrableActions) {
        $deferrableActions[] = $action;
    };

    return [
        'dispatcher' => $dispatcher,
        'defer' => $defer,
    ];
}
