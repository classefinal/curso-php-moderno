<?php

/**
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
 *  connection: mysqli
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
 */