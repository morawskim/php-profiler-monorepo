<?php

namespace Mmo\PhpProfilerCli\Test\Infrastructure\Symfony\Command;

use Mmo\PhpProfilerCli\FlameGraph\FakeFlameGraphSvg;
use Mmo\PhpProfilerCli\FlameGraph\InMemoryWriter;
use Mmo\PhpProfilerCli\Infrastructure\Symfony\Command\CreateFlameGraph;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateFlameGraphTest extends TestCase
{
    public function testCommand(): void
    {
        $inMemoryWriter = new InMemoryWriter();
        $command = new CreateFlameGraph($inMemoryWriter, new FakeFlameGraphSvg());

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => __DIR__ . '/fixture/dump-one.profiler',
            'outputDirectory' => __DIR__, // SVG file is stored in memory not in filesystem
        ]);
        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertEmpty($output);

        $this->assertCount(1, $inMemoryWriter->getData());
        $this->assertStringEqualsFile(__DIR__ . '/fixture/fake-flame-graph-svg-probe1.txt', $inMemoryWriter->getData()[0]);
    }
}
