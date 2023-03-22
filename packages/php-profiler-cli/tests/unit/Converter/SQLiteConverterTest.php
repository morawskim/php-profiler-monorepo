<?php

namespace Mmo\PhpProfilerCli\Test\Converter;

use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\StaticGenerator;
use Mmo\PhpProfilerCli\Converter\SQLiteConverter;
use Mmo\PhpProfilerCli\Reader\ReaderInterface;
use PHPUnit\Framework\TestCase;

class SQLiteConverterTest extends TestCase
{
    public function testConverterToSqlInsertQueries(): void
    {
        $reader = new class() implements ReaderInterface {
            public function readProfilerData(): iterable
            {
                $probe = new Probe(
                    'abc',
                    100,
                    ['foo' => 'bar'],
                    [new Probe('zxc', 50, ['key' => 'value'], [])]
                );
                yield $probe;
            }
        };
        $sut = new SQLiteConverter(new StaticGenerator('1234567890'));
        $sql = $sut->convert($reader);

        $expectedString = file_get_contents(__DIR__ . '/fixture/sqlite-converter-output.txt');
        $this->assertEquals($expectedString, $sql);
    }
}
