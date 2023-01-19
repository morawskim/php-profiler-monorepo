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
}
