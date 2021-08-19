<?php

namespace App\Strategy;

interface ResultUserInterface
{
    public function readData(string $file): array;
}