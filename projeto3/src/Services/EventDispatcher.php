<?php

/**
 * @psalm-import-type Configs from types
 * @psalm-import-type Event from types
 * @psalm-import-type EventDispatcher from types
 */

 /**
  * @param Configs &$configs
  * @param Event[] $events
  * @return EventDispatcher
  */
function createEventDispatcher(array &$configs, array $events): Closure
{
    $eventDispatcher = function (string $eventName, array $args) use ($events, $configs): void {
        if (!isset($events[$eventName])) {
            return;
        }

        $selectedEvents = $events[$eventName];

        array_walk($selectedEvents, function (string|Closure $event, string $listenerName) use ($configs, $args): void {
            if ($event instanceof Closure) {
                $event($configs, $args);

                return;
            }

            $lisneterPath = LISTENERS . $listenerName . '.php';

            if (file_exists($lisneterPath)) {
                require_once $lisneterPath;

                if (function_exists($event)) {
                    $event($configs, $args);
                }
            }
        });
    };

    $configs['eventDispatcher'] = $eventDispatcher;

    return $eventDispatcher;
}
