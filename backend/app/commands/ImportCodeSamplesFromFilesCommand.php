<?php

namespace codeassessor\app\commands;

use codeassessor\app\Application;
use codeassessor\model\CodeSample;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCodeSamplesFromFilesCommand extends Command {
    protected function configure() {
        $this
            ->setName('db:import')
            ->setDescription('Imports code samples from files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $dir = Application::VAR_PATH . '/diffs';
        $files = array_diff(scandir($dir), ['.', '..', 'README.txt']);
        Application::getInstance();
        foreach ($files as $file) {
            CodeSample::firstOrNew([CodeSample::FILENAME => $file])->save();
        }
    }
}
