<?php

namespace App\Strategy;

interface ResultUserInterface
{
    public function readData(string $file): array;
    public function getUser(array $data): string;
    public function getData(array $data): array;
}