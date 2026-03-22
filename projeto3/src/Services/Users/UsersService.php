<?php

declare(strict_types=1);

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
        [['type' => 'i', 'value' => $userId]]
    );

    if (mysqli_num_rows($stmt) === 0) {
        return null;
    }

    return mysqli_fetch_assoc($stmt);
}

/**
 * @param User $user
 * @return array{success: bool, error: ?string}
 */
function validateUpdateUserPassword(array $user): array
{
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (!password_verify($oldPassword, $user['password'])) {
        return ['success' => false, 'error' => 'Senha atual incorreta.'];
    }

    if ($newPassword === '' || $passwordConfirmation === '') {
        return ['success' => false, 'error' => 'Preencha a nova senha duas vezes.'];
    }

    if ($newPassword !== $passwordConfirmation) {
        return ['success' => false, 'error' => 'As novas senhas não coincidem.'];
    }

    if (strlen($newPassword) < 8) {
        return ['success' => false, 'error' => 'A nova senha deve ter pelo menos 8 caracteres.'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param User $user
 * @return void
 */
function setUpdatedUserIntoSession(array $user): void{
    unset($user['password']);

    $_SESSION['user'] = $user;
    $_SESSION['profile_updated'] = true;
}
/**
 * @param mysqli $connection
 * @param integer $userId
 * @return UserUpdateInfo
 */
function updateUserProfile(mysqli $connection, int $userId): array
{
    $user = getUserById($connection, $userId);

    if (!$user) {
        return [
            'success' => false,
            'error' => 'Usuário não encontrado.',
            'user' => null
        ];
    }

    $name = trim($_POST['name'] ?? '');

    if (strlen($name) < 3) {
        return [
            'success' => false,
            'error' => 'O nome deve ter pelo menos 3 caracteres.',
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

        $user = getUserById($connection, $userId);

        setUpdatedUserIntoSession($user);

        return ['success' => true, 'error' => null, 'user' => $user];
    }

    $passwordIsValid = validateUpdateUserPassword($user);

    if (!$passwordIsValid['success']) {
        return [
            'success' => false,
            'error' => $passwordIsValid['error'],
            'user' => $user
        ];
    }

    $hash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    dbPrepareAndExecute(
        $connection,
        'UPDATE users SET name = ?, password = ? WHERE id = ?',
        [
            ['type' => 's', 'value' => $name],
            ['type' => 's', 'value' => $hash],
            ['type' => 'i', 'value' => $userId]
        ]
    );

    $user = getUserById($connection, $userId);

    setUpdatedUserIntoSession($user);

    return ['success' => true, 'error' => null, 'user' => $user];
}
