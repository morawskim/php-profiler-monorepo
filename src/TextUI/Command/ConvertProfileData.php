<?php

namespace Mmo\PhpProfiler\TextUI\Command;

use Mmo\PhpProfiler\Converter\SQLiteConverter;
use Mmo\PhpProfiler\Reader\FilePutContentsReader;
use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfiler\TextUI\Application;

class ConvertProfileData extends Application
{
    /**
     * @var SQLiteConverter
     */
    private $converter;

    public function __construct(SQLiteConverter $converter = null)
    {
        $this->converter = $converter ?? new SQLiteConverter();
    }

    protected function validateArguments($argv): void
    {
        if (count($argv) < 2) {
            throw new \BadMethodCallException('Required argument filePath has not been passed');
        }

        $filePath = $argv[1];

        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" is not readable', $filePath));
        }
    }

    protected function doRun($argv): void
    {
        $filePath = $argv[1];
        $reader = new FilePutContentsReader($filePath, new JsonSerializer());
        $sqlQueries = $this->converter->convert($reader);

        echo SQLiteConverter::getCreateTableSQL();
        echo PHP_EOL;
        echo $sqlQueries;
    }
}
