<?php

namespace Mmo\PhpProfiler\Writer;

use Mmo\PhpProfiler\Profiler;

interface WriterInterface
{
    public function writeProfileData(Profiler $profiler): void;
}
