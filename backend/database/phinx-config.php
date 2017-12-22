<?php
/**
 * https://siipo.la/blog/how-to-use-eloquent-orm-migrations-outside-laravel
 */
$dbSettings = \codeassessor\app\Application::getInstance()->getSetting('db');
return [
    'paths' => [
        'migrations' => __DIR__ . '/migrations',
    ],
    'migration_base_class' => \codeassessor\database\migrations\Migration::class,
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'db',
        'db' => [
            'adapter' => $dbSettings['driver'],
            'host' => $dbSettings['host'],
            'name' => $dbSettings['database'],
            'user' => $dbSettings['username'],
            'pass' => $dbSettings['password']
        ]
    ]
];
