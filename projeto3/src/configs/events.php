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
        'UserCreatedEmailListener' => 'handleUserCreatedEmailEvent',
        'UserCreatedWhatsappListener' => 'handleUserCreatedWhatsappEmailEvent',
        // 'UserCreatedFacebookListener' => fn() => print('Nao implementado')
    ]
];

return $events;
