<?php

declare(strict_types=1);

/**
 * @psalm-import-type Event from types
 */

/**
 * @return Event[]
 */
return [
    'UserCreated' => [
        'UserCreatedEmailListener' => 'handleUserCreatedEmailEvent', 
        'UserCreatedWhatsappListener' => 'handleUserCreatedWhatsappEmailEvent', 
        'UserCreatedFacebookListener' => fn() => print('Nao implementado'), 
    ]
];
