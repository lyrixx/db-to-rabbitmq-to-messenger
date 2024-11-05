<?php

namespace database;

use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\io;
use function docker\docker_compose;
use function docker\docker_compose_run;

#[AsTask(description: 'Connect to the PostgreSQL database', name: 'client', aliases: ['postgres', 'pg'])]
function postgres_client(): void
{
    io()->title('Connecting to the PostgreSQL database');

    docker_compose(['exec', 'postgres', 'psql', '-U', 'app', 'app'], context()->toInteractive());
}

#[AsTask(description: 'Reset database', name: 'reset')]
function reset(bool $test = false): void
{
    io()->title('Resetting the database');

    $suffix = '';
    if ($test) {
        $suffix = '--env=test';
    }

    docker_compose_run("bin/console doctrine:database:drop --force --if-exists {$suffix}");
    docker_compose_run("bin/console doctrine:database:create {$suffix}");
    docker_compose_run("bin/console doctrine:migrations:migrate --no-interaction {$suffix}");
}
