<?php

const DUMMY_PASSWORD_HASH = '$2y$10$wH8Qw1Qw1Qw1Qw1Qw1Qw1u1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1Qw1';
const DEFAULT_LOGIN_ERROR = [
    'success' => false,
    'error' => 'Usuário ou senha inválidos.'
];

/**
 * @psalm-import-type LoginInfo from types
 */

/**
 * @param string $email
 * @param string $password
 * @return LoginInfo
 */
function validateLoginInfo(string $email, string $password): array
{
    if ($email === '' || $password === '') {
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

    return ['success' => true, 'error' => false];
}

/**
 * @return LoginInfo
 */
function adminLoginAuthenticate(mysqli $connection): array
{
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $loginInfoIsValid = validateLoginInfo($email, $password);

    if (!$loginInfoIsValid['success']) {
        return $loginInfoIsValid;
    }

    $result = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM users WHERE email = ? AND active = true AND admin = true LIMIT 1',
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

    $_SESSION['admin'] = $user;

    return [
        'success' => true,
        'error' => null
    ];
}
