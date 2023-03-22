<?php

namespace Mmo\PhpProfilerCli\TextUI;

abstract class Application
{
    public function run(array $argv): void
    {
        try {
            $this->validateArguments($argv);
            $this->doRun($argv);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    abstract protected function validateArguments($argv): void;
    abstract protected function doRun($argv): void;

    private function handleException(\Throwable $e): void
    {
        $message = $e->getMessage();

        if (empty(trim($message))) {
            $message = '(no message)';
        }

        fprintf(
            STDERR,
            '%s%sAn error occurred.%s%sMessage:  %s%sLocation: %s:%d%s%s%s%s',
            PHP_EOL,
            PHP_EOL,
            PHP_EOL,
            PHP_EOL,
            $message,
            PHP_EOL,
            $e->getFile(),
            $e->getLine(),
            PHP_EOL,
            PHP_EOL,
            $e->getTraceAsString(),
            PHP_EOL
        );

        exit(1);
    }
}
