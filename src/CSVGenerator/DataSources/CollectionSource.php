<?php
/**
 * @author Bartosz Bielecki <bartosz.bielecki at itdeveloper.pl>
 * @copyright (c) 2020, Bartosz Bielecki
 */

namespace itdeveloperpl\CSVGenerator\DataSources;

use Illuminate\Support\Collection;
use Exception;

class CollectionSource implements DataSourceInterface
{
    protected $collection;
    protected $config;

    public function addConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function addSource($collection)
    {
        if (false === (get_class($collection) === Collection::class )) {
            throw new Exception("Invalid source type");
        }
        $this->collection = $collection;
        return $this;
    }

    public function getColumnNames(): array
    {
        return array_keys((array) $this->collection->first());
    }

    public function getRows(): array
    {
        return $this->collection->toArray();
    }

    public function getRowData($row): array
    {
        $data = [];

        foreach ($this->config['column_source'] as $columnName => $name) {

            $obj = $row;
            foreach(explode(".", $name) as $val){

                if(gettype($obj) === 'object'){
                    $obj = $obj->$val;
                }
                
            }
            $data[] = $obj;

        }        
        
        return $data;
    }

    public function run(array $config)
    {

    }
}