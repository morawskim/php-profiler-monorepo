<?php

namespace Mmo\PhpProfilerCli\TextUI\Command;

use Mmo\PhpProfiler\Dto\Probe;
use Mmo\PhpProfiler\Serializer\JsonSerializer;
use Mmo\PhpProfilerCli\Dto\QueueProbeFrame;
use Mmo\PhpProfilerCli\FlameGraph\FlameGraphWriterInterface;
use Mmo\PhpProfilerCli\Reader\FilePutContentsReader;
use Mmo\PhpProfilerCli\TextUI\Application;

class CreateFlameGraph extends Application
{
    /**
     * @var FlameGraphWriterInterface
     */
    private $flameGraphWriter;

    public function __construct(FlameGraphWriterInterface $flameGraphWriter)
    {
        $this->flameGraphWriter = $flameGraphWriter;
    }

    protected function validateArguments($argv): void
    {
        if (count($argv) < 3) {
            throw new \BadMethodCallException(sprintf(
                'Required arguments has not been passed. Usage %s profilerFilePath outputDirectory',
                $argv[0] ?? 'create-flame-graph'
            ));
        }

        $filePath = $argv[1];

        if (!file_exists($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist', $filePath));
        }

        if (!is_readable($filePath)) {
            throw new \RuntimeException(sprintf('File "%s" is not readable', $filePath));
        }

        $outputDir = $argv[2];

        if (!is_dir($outputDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" does not exist', $outputDir));
        }

        if (!is_writable($outputDir)) {
            throw new \RuntimeException(sprintf('Directory "%s" is not writeable', $outputDir));
        }
    }

    protected function doRun($argv): void
    {
        $filePath = $argv[1];
        $outputDirectory = $argv[2];
        $reader = new FilePutContentsReader($filePath, new JsonSerializer());

        foreach ($reader->readProfilerData() as $probe)
        {
            $queue = new \SplQueue();
            $this->buildFlatProbesCollection($probe, $queue, [], []);
            $svg = $this->createSVG($this->convertToFlameGraphInput($queue));
            $this->flameGraphWriter->write($svg, $outputDirectory);
        }
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

    private function createSVG(string $content)
    {
        $pipes = [];
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open(implode(' ', [__DIR__ . '/../../../external/flamegraph.pl']), $descriptors, $pipes);
        if (is_resource($process)) {
            // send stdio
            fwrite($pipes[0], $content);
            fclose($pipes[0]);

            // read stdout
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $errors = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            $exitCode = proc_close($process);

            return $stdout;
        }

        throw new \RuntimeException('Cannot exec flamegraph.pl script');
    }
}
