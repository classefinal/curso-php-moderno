<?php

/**
 * @psalm-import-type LoginInfo from types
 * @psalm-import-type EventDispatcher from types
 */
const DUMMY_PASSWORD_HASH = '$2y$16$QJ/fCuE4x29bPKzW0Rgm5ukGB8xwnMajGBPefvamFYFsYbpz7kSOe';
const DEFAULT_LOGIN_ERROR = [
    'success' => false,
    'error' => 'Usuário ou senha incorretos'
];

/**
 * Undocumented function
 *
 * @param string $email
 * @param string $password
 * @return LoginInfo
 */
function validateLoginInfo(string $email, string $password): array
{
    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Usuário e senha são obrigatórios.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'E-mail inválido.'];
    }

    if (!filter_var($password, FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^.{8,}$/']
    ])) {
        return ['success' => false, 'error' => 'A senha deve ter pelo menos 8 caracteres.'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param mysqli $connection
 * @param EventDispatcher $eventDispatcher
 * @return LoginInfo
 */
function adminLoginAuthenticate(mysqli $connection, closure $eventDispatcher): array
{
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $loginInfoIsValid = validateLoginInfo($email, $password);

    $dispatchAdminLoginErrorEvent = fn() => $eventDispatcher('AdminLoginRecused', [
        'email' => $email,
        'date' => date('Y-m-d H:i:s'),
    ]);

    if (!$loginInfoIsValid['success']) {
        $dispatchAdminLoginErrorEvent();

        return $loginInfoIsValid;
    }

    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM users WHERE email = ? AND active = true AND admin = true LIMIT 1',
        [
            [
                'type' => 's',
                'value' => $email
            ]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        $dispatchAdminLoginErrorEvent();

        password_verify($password, DUMMY_PASSWORD_HASH);

        return DEFAULT_LOGIN_ERROR;
    }

    $user = mysqli_fetch_assoc($result);

    $isPasswordCorrect = password_verify($password, $user['password']);

    if (!$isPasswordCorrect) {
        $dispatchAdminLoginErrorEvent();

        return DEFAULT_LOGIN_ERROR;
    }

    $_SESSION['admin'] = $user;

    return [
        'success' => true,
        'error' => 'Um erro foi detectado'
    ];
}

/**
 * @return LoginInfo
 */
function loginAuthenticate(mysqli $connection): array
{
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $loginInfoIsValid = validateLoginInfo($email, $password);

    if (!$loginInfoIsValid['success']) {
        return $loginInfoIsValid;
    }

    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM users WHERE email = ? AND active = true AND admin = false LIMIT 1',
        [
            ['type' => 's', 'value' => $email]
        ]
    );

    if (mysqli_num_rows($result) === 0) {
        password_verify($password, DUMMY_PASSWORD_HASH);

        return DEFAULT_LOGIN_ERROR;
    }

    $user = mysqli_fetch_assoc($result);

    $isPasswordCorrect = password_verify($password, $user['password']);

    if (!$isPasswordCorrect) {
        return DEFAULT_LOGIN_ERROR;
    }

    $_SESSION['user'] = $user;

    return [
        'success' => true,
        'error' => null
    ];
}
