<?php

namespace Mmo\PhpProfilerCli\Test\Reader;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use PHPUnit\Framework\TestCase;

class FilePutContentsReaderTest extends TestCase
{
    public function testReader(): void
    {
        $sut = new FilePutContentsReader(__DIR__ . '/fixture/profiler-reader-test.data', new JsonSerializer());

        $probes = $sut->readProfilerData();
        $probesAsArray = iterator_to_array($probes);

        $this->assertCount(2, $probesAsArray);

        $probe = $probesAsArray[0];
        $this->assertSame('test', $probe->getName());
        $this->assertEquals(['key' => 'value'], $probe->getMetadata());
        $this->assertCount(5, $probe->getChildren());
        $this->assertEqualsWithDelta(3909.7571, $probe->getDuration(), 0.0001);

        // check child and merged metadata
        $child = $probe->getChildren()[0];
        $this->assertSame('test1', $child->getName());
        $this->assertEquals(['key' => 'value', 'foo' => 'bar'], $child->getMetadata());
        $this->assertCount(0, $child->getChildren());
        $this->assertEqualsWithDelta(1000.1199, $child->getDuration(), 0.0001);
    }
}
