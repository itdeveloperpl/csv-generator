<?php
/**
 * @author Bartosz Bielecki <bartosz.bielecki at itdeveloper.pl>
 * @copyright (c) 2020, Bartosz Bielecki
 */

namespace itdeveloperpl\CSVGenerator\DataSources;

interface DataSourceInterface
{

    public function addSource($source);

    public function addConfig(array $config);

    public function run(array $config);

    public function getColumnNames(): array;

    public function getRows(): array;
}