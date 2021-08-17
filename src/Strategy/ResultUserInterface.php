<?php

namespace App\Strategy;

interface ResultUserInterface
{
    public function read(string $file): array;
}