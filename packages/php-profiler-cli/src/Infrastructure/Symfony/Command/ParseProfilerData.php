<?php

namespace Mmo\PhpProfilerCli\Infrastructure\Symfony\Command;

use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\Converter\SQLiteConverter;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseProfilerData extends Command
{
    protected static $defaultName = 'parse-profiler-data';
    protected static $defaultDescription = 'Parse profiler data and output SQL statements to pass to sqlite';

    private SQLiteConverter $converter;

    public function __construct(SQLiteConverter $converter = null, string $name = null)
    {
        $this->converter = $converter ?? new SQLiteConverter();

        parent::__construct($name);
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

        $reader = new FilePutContentsReader($filePath, new JsonSerializer());
        $sqlQueries = $this->converter->convert($reader);

        $output->writeln(SQLiteConverter::getCreateTableSQL());
        $output->write($sqlQueries);

        return 0;
    }
}
