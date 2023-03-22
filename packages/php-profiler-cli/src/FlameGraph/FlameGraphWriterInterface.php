<?php

namespace Mmo\PhpProfilerCli\FlameGraph;

interface FlameGraphWriterInterface
{
    public function write(string $svgContent, $context): void;
}
