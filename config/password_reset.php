<?php

return [
    'ttl_minutes' => (int) env('PASSWORD_RESET_TTL_MINUTES', 15),
    'max_attempts' => (int) env('PASSWORD_RESET_MAX_ATTEMPTS', 3),
];
