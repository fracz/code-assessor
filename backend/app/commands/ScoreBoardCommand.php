<?php

namespace codeassessor\app\commands;

use codeassessor\app\Application;
use codeassessor\model\ArrayToTextTable;
use codeassessor\model\CodeSampleAssessment;
use codeassessor\model\Respondent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScoreBoardCommand extends Command {
    protected function configure() {
        $this
            ->setName('scoreboard');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $app = Application::getInstance();
        $results = array_map(function ($row) {
            return ['Score' => $row->score, 'Count' => $row->count];
        }, $app->db->getConnection()->select(<<<QUERY
            SELECT score, COUNT(*) count FROM (
              SELECT cs.*, COUNT(csa.id) count, IFNULL(SUM(csa.score), 0) score 
              FROM code_samples cs 
              LEFT JOIN code_sample_assessments csa ON cs.id=csa.sample_id 
              GROUP BY cs.id
              HAVING count > 0
              ORDER BY created_at,id
            ) AS t
            GROUP BY score
            ORDER BY score DESC;
QUERY
        ));
        $output->writeln("Respondents: " . Respondent::count());
        $output->writeln("Assessments: " . CodeSampleAssessment::count());
        $output->writeln("Average assessment time: " . CodeSampleAssessment::avg(CodeSampleAssessment::TIME));
        $renderer = new ArrayToTextTable($results);
        $renderer->showHeaders(true);
        $renderer->render();
        echo PHP_EOL;
    }
}
