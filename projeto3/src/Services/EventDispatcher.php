<?php

/**
 * @psalm-import-type Configs from Types
 * @psalm-import-type Events from Types
 * @psalm-import-type EventDispather from Types
 */

/**
 * @param Configs &$configs
 * @param Events $events
 * @return void
 */
function createEventDispatcher(array &$configs, array $events): void
{
    /** @var EventDispather $eventDispatcher */
    $eventDispatcher = function (string $eventName, array $args) use ($events, &$configs): void {
        if (!isset($events[$eventName])) {
            return;
        }

        $listeners = $events[$eventName];

        array_walk($listeners, function (Closure|string $event, string $listenerName) use (&$configs, $args): void {
            if ($event instanceof Closure) {
                $event($configs, $args);

                return;
            }

            $listenerPath = LISTENERS . getRequirePath($listenerName . '.php');

            if (file_exists($listenerPath)) {
                require_once $listenerPath;

                if (function_exists($event)) {
                    $event($configs, $args);
                }
            }
        });
    };

    $configs['eventDispatcher'] = $eventDispatcher;
}
