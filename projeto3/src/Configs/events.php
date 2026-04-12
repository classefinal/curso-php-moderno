<?php

declare(strict_types=1);

/**
 * @psalm-import-type Events from types
 */

/**
 * @var Events $events
 */
$events = [
    'AdminLoginRecused' => [
        'AdminLogin/AdminLoginErrorListener' => 'handleAdminLoginErrorEvent',
    ]
];

return $events;
