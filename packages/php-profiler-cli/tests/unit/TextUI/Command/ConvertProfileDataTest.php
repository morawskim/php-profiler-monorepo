<?php

namespace Mmo\PhpProfilerCli\Test\TextUI\Command;

use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\IncrementalGenerator;
use Mmo\PhpProfilerCli\Converter\SQLiteConverter;
use Mmo\PhpProfilerCli\TextUI\Command\ConvertProfileData;
use PHPUnit\Framework\TestCase;

class ConvertProfileDataTest extends TestCase
{
    public function testCommand(): void
    {
        $sut = new ConvertProfileData(new SQLiteConverter(new IncrementalGenerator(1)));

        ob_start();
        $sut->run(['', __DIR__ . '/fixture/profiler.data']);
        $output = ob_get_clean();

        $expectedString = file_get_contents(__DIR__ . '/fixture/expected-output.txt');
        $this->assertEquals($expectedString, $output);
    }
}
