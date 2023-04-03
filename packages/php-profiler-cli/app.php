#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new \Mmo\PhpProfilerCli\Kernel();
$app->run();
