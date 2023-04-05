<?php

namespace Mmo\PhpProfilerCli\FlameGraph;

class ProcFlameGraphSvg implements FlameGraphSvgInterface
{
    public function createSVG(string $content): string
    {
        $pipes = [];
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];
        $process = proc_open(implode(' ', [__DIR__ . '/../../external/flamegraph.pl']), $descriptors, $pipes);
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
