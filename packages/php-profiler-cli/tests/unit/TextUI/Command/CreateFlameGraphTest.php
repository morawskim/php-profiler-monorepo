<?php

namespace Mmo\PhpProfilerCli\Test\TextUI\Command;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\FlameGraph\InMemoryWriter;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use Mmo\PhpProfilerCli\TextUI\Command\CreateFlameGraph;
use PHPUnit\Framework\TestCase;

class CreateFlameGraphTest extends TestCase
{
    public function testRun(): void
    {
        $reader = new FilePutContentsReader(__DIR__ . '/fixture/dump-one.profiler', new JsonSerializer());
        $probes = iterator_to_array($reader->readProfilerData());

        $sut = new CreateFlameGraph(new InMemoryWriter());
        $getFlameGraphInput = \Closure::bind(function ($probe) {
            $queue = new \SplQueue();
            $this->buildFlatProbesCollection($probe, $queue, [], []);

            return $this->convertToFlameGraphInput($queue);
        }, $sut, $sut);

        $input = $getFlameGraphInput($probes[0]);

        $this->assertStringEqualsFile(__DIR__ . '/fixture/flame-graph-input.txt', $input);
    }
}
