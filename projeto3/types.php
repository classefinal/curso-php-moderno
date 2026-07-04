<?php

/**
 * @psalm-type Defer = Closure(Closure $action):void
 * @psalm-type Dispatcher = Closure(): void
 * @psalm-type Response = Closure(int $httpStatusCode = 200, ?string $content = null): void
 * @psalm-type Redirect = Closure(string $to, int $httpStatusCode = 303): void
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
 *  active: ?bool,
 *  allowedRoutes: string[]|null,
 *  methods: string[],
 *  middlewares: null|string[]
 * }
 * 
 * @psalm-type User = array{
 *  id: int,
 *  name: string,
 *  active: bool,
 *  admin: bool,
 *  password: ?string,
 *  created_at: string,
 *  updated_at: string
 * }
 * 
 * @psalm-type Configs = array{
 *  routes: Route[],
 *  connection: mysqli,
 *  defer: Defer,
 *  response: Response,
 *  redirect: Redirect,
 *  eventDispatcher: EventDispatcher,
 *  view: View,
 *  user: ?User
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
 *  short_description: string,
 *  description_line: string,
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
 * 
 * @psalm-type LoginInfo = array{
 *  success: bool,
 *  error: ?string
 * }
 * 
 * @psalm-type UserUpdateInfo = array{
 *  success: bool,
 *  user: ?User,
 *  error: ?string,
 * }
 * 
 * @psalm-type CartItem = array{
 *  id: int,
 *  cart_id: int,
 *  product_id: int,
 *  quantity: int,
 *  name: string,
 *  price: int,
 *  image: string,
 *  stock: int,
 *  description_line: string,
 *  created_at: string,
 *  updated_at: string
 * }
 */