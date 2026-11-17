<?php

return [
    // Public demo access is deliberately unavailable in a production application.
    // Use APP_ENV=demo and an isolated demo database for a publicly accessible preview.
    'enabled' => filter_var(env('DEMO_MODE', false), FILTER_VALIDATE_BOOLEAN)
        && in_array(env('APP_ENV', 'production'), ['local', 'demo', 'testing'], true),
    'email' => 'showcase-access@fasilitas-unai.invalid',
    'name' => 'Pengunjung Demo',
];
