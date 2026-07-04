<?php

declare(strict_types=1);

/**
 * @psalm-import-type Route from types
 * @psalm-import-type Configs from types
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

    if (empty($name)) {
        $_SESSION['flash']['error'] = 'O nome é obrigatório.';
        $configs['redirect']('/sobre', 302);
        return;
    }

    if (empty($email)) {
        $_SESSION['flash']['error'] = 'O e-mail é obrigatório.';
        $configs['redirect']('/sobre', 302);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash']['error'] = 'E-mail inválido.';
        $configs['redirect']('/sobre', 302);
        return;
    }

    if (empty($phone)) {
        $_SESSION['flash']['error'] = 'O telefone é obrigatório.';
        $configs['redirect']('/sobre', 302);
        return;
    }

    if (!preg_match('/^\(\d{2}\)\d{4,5}-\d{4}$/', $phone)) {
        $_SESSION['flash']['error'] = 'Telefone inválido. Use o formato (00)94878-4541.';
        $configs['redirect']('/sobre', 302);
        return;
    }

    $cleanPhone = '+55' . preg_replace('/\D/', '', $phone);

    $result = dbPrepareAndExecute(
        $configs['connection'],
        'INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)',
        [
            ['type' => 's', 'value' => $name],
            ['type' => 's', 'value' => $email],
            ['type' => 's', 'value' => $cleanPhone],
        ]
    );

    if ($result) {
        $_SESSION['flash']['success'] = 'Mensagem enviada com sucesso!';
    } else {
        $_SESSION['flash']['error'] = 'Erro ao enviar mensagem. Tente novamente.';
    }

    $configs['redirect']('/sobre', 302);
}
