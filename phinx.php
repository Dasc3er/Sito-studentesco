<?php

require_once __DIR__.'/vendor/autoload.php';

$settings = \App\App::getSettings();

$config = [
    'paths' => [
        'migrations' => $settings['database']['migrations_path'],
        'seeds' => $settings['database']['seeds_path'],
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => key($settings['connections']),
    ],
];

foreach ($settings['connections'] as $key => $connection) {
    $config['environments'][$key] = [
        'adapter' => $connection['driver'],
        'host' => $connection['host'],
        'name' => $connection['database'],
        'user' => $connection['username'],
        'pass' => $connection['password'],
        'port' => $connection['port'],
    ];
}

return $config;
