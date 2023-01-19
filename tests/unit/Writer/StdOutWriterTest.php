<?php

namespace Mmo\PhpProfiler\Test\Writer;

use Mmo\PhpProfiler\Test\ObjectMother\ProfilerMother;
use Mmo\PhpProfiler\Writer\StdOutWriter;
use PHPUnit\Framework\TestCase;

class StdOutWriterTest extends TestCase
{
    public function testGeneratedOutput(): void
    {
        ob_start();
        $profiler = ProfilerMother::basic();
        $sut = new StdOutWriter();
        $sut->writeProfileData($profiler);
        $output = ob_get_clean();


        $expectedString = <<<EOS
foo [tag1=val1 tag2=val2] 0ms
  bar [] 0ms

EOS;

        $this->assertEquals($expectedString, $output);
    }

    public function testGeneratedOutputNested(): void
    {
        ob_start();
        $profiler = ProfilerMother::nested();
        $sut = new StdOutWriter();
        $sut->writeProfileData($profiler);
        $output = ob_get_clean();

        $expectedString = <<<EOS
foo [tag1=val1 tag2=val2] 0ms
  bar [] 0ms
    baz [key=value] 0ms

EOS;

        $this->assertEquals($expectedString, $output);
    }
}
