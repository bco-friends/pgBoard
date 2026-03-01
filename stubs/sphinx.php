<?php

/**
 * Sphinx search client stub for PHPStan static analysis.
 * The real implementation is loaded at runtime via lib/sphinx/sphinxapi.php.
 */

const SPH_MATCH_EXTENDED = 4;
const SPH_SORT_ATTR_DESC = 1;

class SphinxClient
{
    public function setServer(string $host, int $port): void {}
    public function SetLimits(int $offset, int $limit, int $max = 0): void {}
    public function SetMatchMode(int $mode): void {}
    public function SetSortMode(int $mode, string $sortby = ''): void {}
    /** @return array<mixed>|bool */
    public function Query(string $query, string $index = '*'): array|bool {}
}