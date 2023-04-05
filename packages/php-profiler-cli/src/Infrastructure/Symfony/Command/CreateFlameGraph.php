<?php

namespace Mmo\PhpProfilerCli\Infrastructure\Symfony\Command;

use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\Dto\QueueProbeFrame;
use Mmo\PhpProfilerCli\FlameGraph\FlameGraphSvgInterface;
use Mmo\PhpProfilerCli\FlameGraph\FlameGraphWriterInterface;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateFlameGraph extends Command
{
    protected static $defaultName = 'create-flame-graph';
    protected static $defaultDescription = 'Create flame graphs from profiler data';

    private FlameGraphWriterInterface $flameGraphWriter;
    private FlameGraphSvgInterface $flameGraphSvg;

    public function __construct(FlameGraphWriterInterface $flameGraphWriter, FlameGraphSvgInterface $flameGraphSvg, string $name = null)
    {
        $this->flameGraphWriter = $flameGraphWriter;
        $this->flameGraphSvg = $flameGraphSvg;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'The profiler file'),
                new InputArgument('outputDirectory', InputArgument::REQUIRED, 'The output directory where SVG files will be saved'),
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

        $outputDir = $input->getArgument('outputDirectory');

        if (!is_dir($outputDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" does not exist', $outputDir));
        }

        if (!is_writable($outputDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" is not writeable', $outputDir));
        }

        $reader = new FilePutContentsReader($filePath, new JsonSerializer());

        foreach ($reader->readProfilerData() as $probe)
        {
            $queue = new \SplQueue();
            $this->buildFlatProbesCollection($probe, $queue, [], []);
            $svg = $this->flameGraphSvg->createSVG($this->convertToFlameGraphInput($queue));
            $this->flameGraphWriter->write($svg, $outputDir);
        }

        return 0;
    }

    private function buildFlatProbesCollection(Probe $probe, \SplQueue $stack, array $parentFunctions, array $parentProbes): void
    {
        $functionName = preg_replace('/[^a-zA-Z_\-0-9]/', '_', $probe->getName());
        $result = max((int) round($probe->getDuration()), 1);
        $parentFunctions[] = $functionName;

        $a = new QueueProbeFrame($result, $parentFunctions);

        $parentProbes[] = $a;
        foreach ($probe->getChildren() as $child) {
            $this->buildFlatProbesCollection($child, $stack, $parentFunctions, $parentProbes);
        }
        array_pop($parentProbes);
        $a->setParentProbes($parentProbes);

        $stack->enqueue($a);
    }

    private function convertToFlameGraphInput(\SplQueue $queue): string
    {
        $line = '';

        /** @var QueueProbeFrame $probeFrame */
        while (!$queue->isEmpty()) {
            $probeFrame = $queue->dequeue();

            if (0 === count($probeFrame->parentsFrame)) {
                continue;
            }

            $line .= sprintf(
                '%s %d',
                implode(';', $probeFrame->calledFunctionNamesStack),
                max($probeFrame->time, 1)
            );
            $line .= PHP_EOL;

            while ($p = array_pop($probeFrame->parentsFrame)) {
                /** @var QueueProbeFrame $p */
                $p->time -= $probeFrame->time;
            }
        }

        return $line;
    }
}
