<?php

namespace Mmo\PhpProfiler\Reader;

interface ReaderInterface
{
    public function readProfilerData(): iterable;
}
