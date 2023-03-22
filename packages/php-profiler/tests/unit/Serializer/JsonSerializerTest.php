<?php

namespace Mmo\PhpProfiler\Test\Serializer;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfiler\Test\ObjectMother\ProfilerMother;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    public function testSerialize(): void
    {
        $profiler = ProfilerMother::basic();

        $sut = new JsonSerializer();
        $json = $sut->serialize($profiler);

        $this->assertJson($json);
        $decodedJson = json_decode($json, true);

        $this->assertArrayHasKey('children', $decodedJson);
        $this->assertArrayHasKey('span', $decodedJson);

        $this->assertArrayHasKey('duration', $decodedJson['span']);
        $this->assertArrayHasKey('metadata', $decodedJson['span']);
        $this->assertArrayHasKey('name', $decodedJson['span']);

        $this->assertEquals(['tag1' => 'val1', 'tag2' => 'val2'], $decodedJson['span']['metadata']);
        $this->assertEquals('foo', $decodedJson['span']['name']);
        $this->assertGreaterThan(0, $decodedJson['span']['duration']);

        $this->assertCount(1, $decodedJson['children']);
        $this->assertSame('bar', $decodedJson['children'][0]['span']['name']);
    }

    public function testDeserialize(): void
    {
        $serializedString = file_get_contents(__DIR__ . '/fixture/profiler.data');

        $sut = new JsonSerializer();
        $probe = $sut->deserialize($serializedString);

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
