<?php

namespace Mmo\PhpProfiler\FlameGraph;

interface FlameGraphWriterInterface
{
    public function write(string $svgContent, $context): void;
}
