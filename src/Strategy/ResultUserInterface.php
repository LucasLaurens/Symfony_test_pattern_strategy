<?php

namespace App\Strategy;

interface ResultUserInterface
{
    public function readData(string $file): array;
    public function dateParser(array $arr): array;
    public function getPointsByPeriod(array $data, array $dates, array $points): array;
    public function getEurosByPeriod(array $points, array $euros): array;
    public function getPeriods(array $dataArray): array;
    public function addLastValuesInFinalArray(array $dataArray, array $points, array $euros): array;
    public function getUser(array $data): string;
}