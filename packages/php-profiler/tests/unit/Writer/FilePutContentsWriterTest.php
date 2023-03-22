<?php

namespace Mmo\PhpProfiler\Test\Writer;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfiler\Test\ObjectMother\ProfilerMother;
use Mmo\PhpProfiler\Writer\FilePutContentsWriter;
use PHPUnit\Framework\TestCase;

class FilePutContentsWriterTest extends TestCase
{
    public function testGeneratedOutput(): void
    {
        $profiler = ProfilerMother::basic();
        $sut = new FilePutContentsWriter(
            new JsonSerializer(),
            $tmpFileName = tempnam(sys_get_temp_dir(), "profiler-")
        );
        $sut->writeProfileData($profiler);

        $this->assertFileExists($tmpFileName);

        $fileAsArray = file($tmpFileName);
        $this->assertCount(1, $fileAsArray);

        $this->assertRegExp(
            '/{"span":{"name":"foo","duration":0.\d+,"metadata":{"tag1":"val1","tag2":"val2"}},"children":\[{"span":{"name":"bar","duration":0.\d+,"metadata":\[\]},"children":\[\]}\]}/',
            file_get_contents($tmpFileName)
        );
    }
}
