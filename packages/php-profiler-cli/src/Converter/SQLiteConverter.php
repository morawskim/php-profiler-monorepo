<?php

namespace Mmo\PhpProfilerCli\Converter;

use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\CorrelationIdGeneratorInterface;
use Mmo\PhpProfilerCli\Converter\CorrelationIdGenerator\RandomGenerator;
use Mmo\PhpProfilerCli\Reader\ReaderInterface;

class SQLiteConverter implements ConverterInterface
{
    /**
     * @var CorrelationIdGeneratorInterface|null
     */
    private $correlationIdGenerator;

    public function __construct(CorrelationIdGeneratorInterface $correlationIdGenerator = null)
    {
        $this->correlationIdGenerator = $correlationIdGenerator ?? new RandomGenerator();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function getCreateTableSQL(): string
    {
        return <<<EOS
CREATE TABLE IF NOT EXISTS profiler (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
   	name VARCHAR(255) NOT NULL,
   	correlation_id VARCHAR(128) NOT NULL,
    duration DECIMAL(6,4) DEFAULT 0,
    depth SMALLINT DEFAULT 0,
    metadata JSON
);
EOS;
    }

    public function convert(ReaderInterface $reader): string
    {
        $sql = '';

        foreach ($reader->readProfilerData() as $probe) {
            $correlationId = $this->correlationIdGenerator->getCorrelationId();
            $sql .= $this->processProbe($probe, $correlationId, 0);
        }

        return $sql;
    }

    private function processProbe(Probe $probe, $correlationId, $depth): string
    {
        $sql = sprintf(
            'INSERT INTO `profiler` (name, duration, depth, correlation_id, metadata) VALUES (\'%s\', %f, %d, \'%s\', json(\'%s\'));',
            addslashes($probe->getName()),
            addslashes($probe->getDuration()),
            $depth,
            addslashes($correlationId),
            json_encode($probe->getMetadata())
        );
        $sql .= PHP_EOL;

        foreach ($probe->getChildren() as $child) {
            $sql .= $this->processProbe($child, $correlationId, $depth + 1);
        }

        return $sql;
    }
}
