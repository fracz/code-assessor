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
            ->setDescription('Imports code samples from files.')
            ->addOption('update');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        Application::getInstance();
        $dir = Application::VAR_PATH . '/diffs';
        $files = array_diff(scandir($dir), ['.', '..', 'README.txt', '.gitignore']);
        $extraFilesInDb = CodeSample::whereNotIn(CodeSample::FILENAME, $files)->get()->pluck(CodeSample::FILENAME)->toArray();
        $sameFiles = CodeSample::whereIn(CodeSample::FILENAME, $files)->get()->pluck(CodeSample::FILENAME)->toArray();
        $extraFilesInFs = array_diff($files, $sameFiles);
        unset($files);
        shuffle($sameFiles);
        shuffle($extraFilesInDb);
        shuffle($extraFilesInFs);
        $output->writeln('Not changed files: ' . count($sameFiles));
        foreach (array_slice($sameFiles, 0, 10) as $example) {
            $output->writeln("\t$example");
        }
        $output->writeln('New files in dataset: ' . count($extraFilesInFs));
        foreach (array_slice($extraFilesInFs, 0, 10) as $example) {
            $output->writeln("\t$example");
        }
        $output->writeln('Files not present in dataset: ' . count($extraFilesInDb));
        foreach (array_slice($extraFilesInDb, 0, 10) as $example) {
            $output->writeln("\t$example");
        }
        if ($extraFilesInDb || $extraFilesInFs) {
            if ($input->getOption('update')) {
                foreach ($extraFilesInFs as $file) {
                    CodeSample::firstOrNew([CodeSample::FILENAME => $file])->save();
                }
                if ($extraFilesInDb) {
                    CodeSample::whereIn(CodeSample::FILENAME, $extraFilesInDb)->delete();
                }
            } else {
                $output->writeln('Changes has not been applied. To apply, add an --update option.');
            }
        }
    }
}
