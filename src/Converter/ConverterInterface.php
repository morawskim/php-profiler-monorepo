<?php

namespace Mmo\PhpProfiler\Converter;

use Mmo\PhpProfiler\Reader\ReaderInterface;

interface ConverterInterface
{
    public function convert(ReaderInterface $reader): string;
}
