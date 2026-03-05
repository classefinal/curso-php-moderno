<?php

declare(strict_types=1);

/**
 * @psalm-import-type Events from types
 */

/**
 * @var Events $events
 */
$events = [
    'UserCreated' => [
        'UserCreated/UserCreatedEmailListener' => 'handleUserCreatedEmailEvent',
        'UserCreated/UserCreatedWhatsappListener' => 'handleUserCreatedWhatsappEmailEvent',
        // 'UserCreatedFacebookListener' => fn() => print('Nao implementado')
    ]
];

return $events;
