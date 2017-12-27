<?php

namespace codeassessor\app\commands;

use Symfony\Component\Console\Application;

class CodeassManager extends Application {
    public function __construct() {
        parent::__construct('Code Assessor', \codeassessor\app\Application::version());
        $this->addCommands([
            new ImportCodeSamplesFromFilesCommand(),
            new MigrateDbCommand(),
            new ScoreBoardCommand(),
        ]);
    }
}
