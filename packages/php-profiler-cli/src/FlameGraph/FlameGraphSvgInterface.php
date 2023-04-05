<?php

namespace Mmo\PhpProfilerCli\FlameGraph;

interface FlameGraphSvgInterface
{
    public function createSVG(string $content): string;
}
