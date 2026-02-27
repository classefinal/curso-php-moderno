<?php

/**
 * @psalm-import-type DeferConfig from types
 */

/**
 * @return DeferConfig
 */
function createDefer(): array
{
    $deferrableActions = [];

    $dispatcher = function () use (&$deferrableActions): void {
        array_walk($deferrableActions, fn(Closure $deferrableAction) => $deferrableAction());
    };

    $defer = function (Closure $action) use (&$deferrableActions): void {
        $deferrableActions[] = $action;
    };


    return [
        'defer' => $defer,
        'dispatcher' => $dispatcher
    ];
}
