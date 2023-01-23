<?php

namespace Mmo\PhpProfiler\Test\Converter;

use Mmo\PhpProfiler\Converter\CorrelationIdGenerator\StaticGenerator;
use Mmo\PhpProfiler\Converter\SQLiteConverter;
use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfiler\Reader\ReaderInterface;
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
