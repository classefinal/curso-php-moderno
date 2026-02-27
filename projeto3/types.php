<?php

/**
 * @psalm-type Defer = Closure(Closure $action): void
 * @psalm-type Dispatcher = Closure(): void
 * @psalm-type Response = Closure(int $httpStatusCode = 200, ?string $content = null): void
 * @psalm-type Event = array<string, array<string, string|Closure>
 * @psalm-type EventDispatcher = Closure(string $eventName, array $args): void
 * 
 * @psalm-type Route = array{
 *  id: string,
 *  value: string,
 *  controller: string,
 *  call: string,
 *  isRegex: bool,
 *  inMenu: ?bool,
 *  label: ?string,
 *  order: ?int,
 *  active: ?bool
 * }
 * 
 * @psalm-type Configs = array{
 *  routes: Route[],
 *  connection: mysqli,
 *  defer: Defer,
 *  response: Response,
 *  eventDispatcher: EventDispatcher
 * }
 * 
 * @psalm-type StmArg = array{
 *  type: string,
 *  value: mixed
 * }
 * 
 * @psalm-type Migration = array{
 *  up: Closure(mysqli $connection): mixed
 * }
 * 
 * @psalm-type DeferConfig = array{
 *  defer: Defer,
 *  dispatcher: Dispatcher
 * }
 * 
 */