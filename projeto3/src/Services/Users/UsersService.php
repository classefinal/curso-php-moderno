<?php

/**
 * @psalm-import-type User from types
 * @psalm-import-type UserUpdateInfo from types
 */

/**
 * @param mysqli $connection
 * @param integer $userId
 * @return User|null
 */
function getUserById(mysqli $connection, int $userId): ?array
{
    $stmt = dbPrepareAndExecute(
        $connection,
        'SELECT * FROM users WHERE id = ? AND active = true LIMIT 1',
        [
            [
                'type' => 'i',
                'value' => $userId
            ]
        ]
    );

    if (mysqli_num_rows($stmt) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($stmt);
}

/**
 * @param User $user
 * @return array
 */
function validateUpdateUserPassword(array $user): array
{
    $oldPassoword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (!password_verify($oldPassoword, $user['password'])) {
        return ['success' => false, 'error' => 'Senha atual incorreta'];
    }

    if (empty($newPassword)) {
        return ['success' => false, 'error' => 'Preencha a senha'];
    }

    if ($newPassword !== $passwordConfirmation) {
        return ['success' => false, 'error' => 'A confirmaçao de senha deve ser igual a nova senha'];
    }

    if (strlen($newPassword) < 8) {
        return ['success' => false, 'error' => 'A senha deve ter pelo menos 8 caracteres'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param User $user
 * @return void
 */
function setUpdatedUserIntoSession(array $user): void
{
    unset($user['password']);

    $_SESSION['user'] = $user;
    $_SESSION['profile_updated'] = true;
}

/**
 * @param mysqli $connection
 * @param User $user
 * @return array{success: bool, error: ?string}
 */
function updateUserProfile(mysqli $connection, array $user): array
{
    $userId = $user['id'];
    $name = strip_tags(trim($_POST['name'] ?? ''));

    if (strlen($name) < 3 || strlen($name) > 255) {
        return [
            'success' => false,
            'error' => 'O nome deve ter entre 3 e 255 caracteres',
            'user' => $user
        ];
    }

    if (empty($_POST['new_password'])) {
        dbPrepareAndExecute(
            $connection,
            'UPDATE users SET name = ? WHERE id = ?',
            [
                ['type' => 's', 'value' => $name],
                ['type' => 'i', 'value' => $userId]
            ]
        );

        $user['name'] = $name;
        setUpdatedUserIntoSession($user);

        return [
            'success' => true,
            'error' => null,
            'user' => $user
        ];
    }

    $passwordIsValid = validateUpdateUserPassword($user);

    if (!$passwordIsValid['success']) {
        return [
            'success' => false,
            'error' => $passwordIsValid['error'],
            'user' => $user
        ];
    }

    $hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT, ['cost' => 16]);

    dbPrepareAndExecute(
        $connection,
        'UPDATE users SET name = ?, password = ? WHERE id = ?',
        [
            ['type' => 's', 'value' => $name],
            ['type' => 's', 'value' => $hash],
            ['type' => 'i', 'value' => $userId]
        ]
    );

    $user['name'] = $name;
    setUpdatedUserIntoSession($user);

    return [
        'success' => true,
        'error' => null,
        'user' => $user
    ];
}
