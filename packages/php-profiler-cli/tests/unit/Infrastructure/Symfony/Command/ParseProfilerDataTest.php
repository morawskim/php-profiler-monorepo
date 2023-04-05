<?php

namespace Mmo\PhpProfilerCli\Test\Infrastructure\Symfony\Command;

use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\IncrementalGenerator;
use Mmo\PhpProfilerCli\Converter\SQLiteConverter;
use Mmo\PhpProfilerCli\Infrastructure\Symfony\Command\ParseProfilerData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ParseProfilerDataTest extends TestCase
{
    public function testCommand(): void
    {
        $command = new ParseProfilerData(new SQLiteConverter(new IncrementalGenerator(1)));

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => __DIR__ . '/fixture/profiler.data',
        ]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringEqualsFile(__DIR__ . '/fixture/expected-output.txt', $output);
    }
}
