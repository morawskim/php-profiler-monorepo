<?php

namespace Mmo\PhpProfilerCli\Test\Infrastructure\Symfony\Command;

use Mmo\PhpProfilerCli\Infrastructure\Symfony\Command\SqlMatrix;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SqlMatrixTest extends TestCase
{
    public function testCommand(): void
    {
        $command = new SqlMatrix();
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => __DIR__ . '/fixture/profiler-data-for-sql-matrix',
        ]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringEqualsFile(__DIR__ . '/fixture/expected-sql-matrix-output', $output);
    }
}
