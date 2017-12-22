<?php

namespace codeassessor\app\commands;

use codeassessor\app\Application;
use Illuminate\Database\Capsule\Manager;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDbCommand extends Command {
    protected function configure() {
        $this
            ->setName('db:migrate')
            ->setDescription('Performs the database schema migration.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->waitForDbConnection($output);
        $app = new PhinxApplication();
        $configPath = addcslashes(__DIR__, '\\') . '/../../database/phinx-config.php';
        $app->setAutoExit(false);
        $app->run(new StringInput("migrate -c $configPath"), $output);
    }

    private function waitForDbConnection(OutputInterface $output, $dbConnectionRetries = 10) {
        $db = Application::getInstance()->db;
        $connected = false;
        do {
            try {
                $db->getConnection()->query()->addSelect(Manager::raw('now()'))->get();
                $connected = true;
            } catch (\PDOException $e) {
                if ($dbConnectionRetries <= 0) {
                    throw new \RuntimeException('Could not connect to to the database.', 0, $e);
                } else {
                    $output->writeln("Waiting for database connection ($dbConnectionRetries)...");
                    --$dbConnectionRetries;
                    sleep(3);
                }
            }
        } while (!$connected);
    }
}
