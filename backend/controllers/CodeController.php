<?php

namespace codeassessor\controllers;

use Assert\Assertion;
use codeassessor\app\Application;
use codeassessor\model\CodeSample;
use codeassessor\model\CodeSampleAssessment;

class CodeController extends BaseController {
    public function getRandomAction() {
        $rules = $this->getApp()->getSetting('rules');
        $skipIds = $this->request()->getParam('skipIds', '');
        $skipIds = implode(',', array_filter(array_map('intval', explode(',', $skipIds))));
        if (!$skipIds) $skipIds = 0;
        $sample = $this->getApp()->db->getConnection()->selectOne(<<<QUERY
            SELECT id, filename FROM (
              SELECT cs.*, COUNT(csa.id) count, IFNULL(SUM(csa.score), 0) score 
              FROM code_samples cs 
              LEFT JOIN code_sample_assessments csa ON cs.id=csa.sample_id 
              WHERE cs.id NOT IN($skipIds)
              GROUP BY cs.id
              HAVING 
              (score != 0 AND ABS(score) < $rules[min_score_to_decide] AND count < $rules[max_votes_to_give_up])
              OR (score = 0 AND count < $rules[min_score_to_decide])
              ORDER BY created_at,id
              LIMIT $rules[samples_into_consideration]
            ) AS t
            ORDER BY RAND()
            LIMIT 1;
QUERY
        );
//        $sample->filename = '0.txt';
        return $this->response([
            'id' => $sample->id,
            'diff' => file_get_contents(Application::VAR_PATH . "/diffs/$sample->filename"),
        ]);
    }

    public function getTestAction() {
        $rules = $this->getApp()->getSetting('rules');
        $sample = $this->getApp()->db->getConnection()->selectOne(<<<QUERY
            SELECT id, filename, score FROM (
              SELECT cs.*, COUNT(csa.id) count, IFNULL(SUM(csa.score), 0) score 
              FROM code_samples cs 
              LEFT JOIN code_sample_assessments csa ON cs.id=csa.sample_id 
              GROUP BY cs.id
              HAVING ABS(score) >= $rules[min_score_to_decide]
              ORDER BY created_at,id
            ) AS t
            ORDER BY RAND()
            LIMIT 1;
QUERY
        );
        return $this->response([
            'id' => $sample->id,
            'diff' => file_get_contents(Application::VAR_PATH . "/diffs/$sample->filename"),
            'positive' => $sample->score > 0
        ]);
    }

    public function assessAction($params) {
        /** @var CodeSample $codeSample */
        $codeSample = $this->ensureExists(CodeSample::find($params['id']));
        $body = $this->request()->getParsedBody();
        Assertion::keyExists($body, 'score');
        $score = intval($body['score']);
        Assertion::inArray($score, [-1, 0, 1]);
        $codeSample->assessments()->create([CodeSampleAssessment::SCORE => $score])->save();
        return $this->response(['ok' => true])->withStatus(201);
    }
}
