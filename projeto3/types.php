<?php

/**
 * @psalm-type Defer = Closure(Closure $action):void
 * @psalm-type Dispatcher = Closure(): void
 * @psalm-type Response = Closure(int $httpStatusCode = 200, ?string $content = null): void
 * @psalm-type EventHandler = Closure(Configs $configs, array $args): void
 * @psalm-type Events = array<string, array<string, EventHandler|string>>
 * @psalm-type EventDispatcher = Closure(string $eventName, array $args): void
 * @psalm-type View = Closure(string $viewPath, array $args): string
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
 *  eventDispatcher: EventDispatcher,
 *  view: View
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
 * @psalm-type Product = array{
 *  id: int,
 *  name: string,
 *  description: string,
 *  active: int,
 *  price: int,
 *  stock: int,
 *  image: string,
 *  category_id: string,
 *  category_name: string,
 *  created_at: string,
 *  updated_at: string
 * }
 * 
 * @psalm-type Category = array{
 *  id: int,
 *  name: string,
 *  active: int,
 *  description: string,
 *  created_at: string,
 *  updated_at: string
 * }
 * 
 * @psalm-type ActiveProductsList = array{
 *  page: int,
 *  limit: int,
 *  categoryId: ?int,
 *  products: Product[]
 * }
 * 
 * @psalm-type EmptyLinkConfig = array{
 *  link: string,
 *  text: string,
 *  title: ?string,
 *  icon: ?string,
 * }
 */