<?php

declare(strict_types=1);

require_once SERVICES . getRequirePath('Contact/ContactService.php');

/**
 * @psalm-import-type Route from Types
 * @psalm-import-type Configs from Types
 */

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function makeAbout(array $configs, array $route, string $uri): void
{
    $success = $_SESSION['flash']['success'] ?? null;
    $error = $_SESSION['flash']['error'] ?? null;
    unset($_SESSION['flash']);

    $content = $configs['view']('about', [
        'title' => 'Página sobre',
        'routes' => getMenuItens($configs['routes'], $uri, $route),
        'success' => $success,
        'error' => $error,
    ]);

    $configs['response'](content: $content);
}

/**
 * @param Configs $configs
 * @param Route $route
 * @param string $uri
 * @return void
 */
function sendContact(array $configs, array $route, string $uri): void
{
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $result = processContact($configs['connection'], $name, $email, $phone);

    if (!$result['success']) {
        $_SESSION['flash']['error'] = $result['error'];
        $configs['redirect']('/sobre', 302);
        return;
    }

    $_SESSION['flash']['success'] = 'Mensagem enviada com sucesso!';
    $configs['redirect']('/sobre', 302);
}
