<?php

namespace codeassessor\model;

use codeassessor\app\Application;

trait HasApp
{
    protected function getApp(): Application
    {
        return Application::getInstance();
    }
}
