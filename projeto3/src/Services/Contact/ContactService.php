<?php

/**
 * @psalm-import-type OperationResult from Types
 */

/**
 * @param string $name
 * @return OperationResult
 */
function validateContactName(string $name): array
{
    if (empty($name)) {
        return ['success' => false, 'error' => 'O nome é obrigatório.'];
    }

    if (strlen($name) < 3) {
        return ['success' => false, 'error' => 'O nome deve ter no mínimo 3 caracteres.'];
    }

    if (strlen($name) > 255) {
        return ['success' => false, 'error' => 'O nome deve ter no máximo 255 caracteres.'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param string $email
 * @return OperationResult
 */
function validateContactEmail(string $email): array
{
    if (empty($email)) {
        return ['success' => false, 'error' => 'O e-mail é obrigatório.'];
    }

    if (strlen($email) > 255) {
        return ['success' => false, 'error' => 'O e-mail deve ter no máximo 255 caracteres.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'E-mail inválido.'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param string $phone
 * @return OperationResult
 */
function validateContactPhone(string $phone): array
{
    if (empty($phone)) {
        return ['success' => false, 'error' => 'O telefone é obrigatório.'];
    }

    if (strlen($phone) < 10 || strlen($phone) > 20) {
        return ['success' => false, 'error' => 'Telefone inválido. Use o formato (00)94878-4541.'];
    }

    if (!preg_match('/^\(\d{2}\)\d{4,5}-\d{4}$/', $phone)) {
        return ['success' => false, 'error' => 'Telefone inválido. Use o formato (00)94878-4541.'];
    }

    return ['success' => true, 'error' => null];
}

/**
 * @param mysqli $connection
 * @param string $name
 * @param string $email
 * @param string $phone
 * @return array{success: bool, error: ?string, data: array{name: string, email: string, phone: string}|null}
 */
function processContact(mysqli $connection, string $name, string $email, string $phone): array
{
    $nameValidation = validateContactName($name);
    if (!$nameValidation['success']) {
        return $nameValidation;
    }

    $emailValidation = validateContactEmail($email);
    if (!$emailValidation['success']) {
        return $emailValidation;
    }

    $phoneValidation = validateContactPhone($phone);
    if (!$phoneValidation['success']) {
        return $phoneValidation;
    }

    $cleanPhone = '+55' . preg_replace('/\D/', '', $phone);

    $result = dbPrepareAndExecute(
        $connection,
        'INSERT INTO contacts (name, email, phone) VALUES (?, ?, ?)',
        [
            ['type' => 's', 'value' => $name],
            ['type' => 's', 'value' => $email],
            ['type' => 's', 'value' => $cleanPhone],
        ]
    );

    if ($result) {
        return ['success' => true, 'error' => null];
    }

    return ['success' => false, 'error' => 'Erro ao enviar mensagem. Tente novamente.'];
}
