<?php

namespace Mmo\PhpProfilerCli\Infrastructure\Symfony\Command;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SqlMatrix extends Command
{
    protected static $defaultName = 'sql-matrix';
    protected static $defaultDescription = 'Display SQL query to generate matrix';

    private \Closure $normalizeEventName;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->normalizeEventName = static function (string $name) {
            return strtolower(preg_replace('/[^a-zA-Z0-9\_]/', '', str_replace([' ', '-'], '_', $name)));
        };
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'The profiler file'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" is not readable', $filePath));
        }

        $uniqueNames = [];
        $reader = new FilePutContentsReader($filePath, new JsonSerializer());
        foreach ($reader->readProfilerData() as $probe) {
            foreach ($probe->getChildren() as $childProbe) {
                $uniqueNames[] = $childProbe->getName();
            }
        }
        $uniqueNames = array_values(array_unique($uniqueNames));

        $sql = $this->createSql($uniqueNames);
        $output->writeln($sql);

        return 0;
    }

    private function createSql(array $uniqueEvents): string
    {
        $eventColumns = $this->getEventColumns($uniqueEvents);
        $maxColumns = $this->getMaxColumns($uniqueEvents);
        $percentColumns = $this->getPercentColumns($uniqueEvents);

        return <<<EOS
        SELECT
            o2.correlation_id,
            MAX(o2.total) totalMS,
            $maxColumns,
            $percentColumns
        FROM (
            SELECT
                o.correlation_id,
                o.total,
                (o.duration / o.total) as share,
                $eventColumns
            FROM (
                SELECT p.name,
                    p.correlation_id,
                    p.duration,
                    (SELECT duration FROM profiler WHERE depth = 0 AND correlation_id = p.correlation_id) as total
                FROM profiler p
                WHERE p.depth = 1
            ) o
        ) o2
        GROUP BY correlation_id
EOS;
    }

    private function getEventColumns(array $uniqueEventNames): string
    {
        $rowsToColumns = function (string $name) {
            return sprintf(
                "(SELECT o.duration FROM profiler p WHERE o.correlation_id = p.correlation_id AND o.name = '%s' ) as '%s'",
                $name,
                call_user_func($this->normalizeEventName, $name)
            );
        };

        return implode(",\n", array_map($rowsToColumns, $uniqueEventNames));
    }

    private function getMaxColumns(array $uniqueEventNames): string
    {
        $createColumnStatement = function (string $name) {
            return sprintf(
                'MAX(o2.%1$s) %1$sMS',
                call_user_func($this->normalizeEventName, $name)
            );
        };

        return implode(",\n", array_map($createColumnStatement, $uniqueEventNames));
    }

    private function getPercentColumns(array $uniqueEventNames): string
    {
        $createColumnStatement = function (string $name) {
            return sprintf(
                'ROUND((MAX(o2.%1$s) / MAX(o2.total)) * 100, 2) %1$sPercent',
                call_user_func($this->normalizeEventName, $name)
            );
        };

        return implode(",\n", array_map($createColumnStatement, $uniqueEventNames));
    }
}
