<?php

return [
    'send_email' => filter_var(env('SEND_EMAIL', false), FILTER_VALIDATE_BOOLEAN),
    'ttl_minutes' => (int) env('PASSWORD_RESET_TTL_MINUTES', 15),
    'max_attempts' => (int) env('PASSWORD_RESET_MAX_ATTEMPTS', 3),
];
