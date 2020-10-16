<?php
/**
 * @author Bartosz Bielecki <bartosz.bielecki at itdeveloper.pl>
 * @copyright (c) 2020, Bartosz Bielecki
 */

namespace itdeveloperpl\CSVGenerator;

use itdeveloperpl\CSVGenerator\DataSources\DataSourceInterface;

class Generator
{
    /**
     * @var \itdeveloperpl\CSVGenerator\DataSources\DataSourceInterface
     */
    protected $source;
    protected $file;
    protected $config = [
        'delimeter' => "\t",
        'column_names_add' => true,
        'column_source' => [
            'ID ' => 'id',
            'Data zamówienia' => 'created_at',
            'Źródło zamówienia' => 'agent.name',
            'Dodane przez' => 'broker.name',
            "Imię " => 'customer.first_name',
            'Nazwisko ' => 'customer.last_name'
        ],
        'output_charset' => 'iso-8859-2',
        'output_filename'=>'file.csv'
    ];

    public function __construct(array $config = null)
    {
        if ($config) {
            $this->config = array_merge($this->config,$config);
        }
    }

    public function from(DataSourceInterface $source)
    {
        $this->source = $source;
        $this->source->addConfig($this->config);
        return $this;
    }

    protected function addColumnNames()
    {
        $row = [];
        foreach ($this->config['column_source'] as $columnName => $name) {
            $row[] = $columnName;
        }
        $this->addRow($row);
    }

    protected function addRow(array $data)
    {
        fputcsv(
            $this->file, $this->convertEncoding($data),
            $this->config['delimeter']
        );
    }

    protected function convertEncoding(array $data): array
    {
        if ($this->config['output_charset'] != 'utf-8') {
            foreach ($data as $key => $val) {
                $data[$key] = mb_convert_encoding($val,$this->config['output_charset']);
            }
        }
        return $data;
    }

    protected function generate()
    {
        $this->file = fopen('php://temp', 'w');

        $row = [];
        if ($this->config['column_names_add']) {
            $this->addColumnNames();
        }

        foreach ($this->source->getRows() as $row) {
            $data = $this->source->getRowData($row);
            $this->addRow($data);
        }
        rewind($this->file);
    }

    public function run()
    {
        $this->generate();
        echo stream_get_contents($this->file);
        fclose($this->file);
    }

    public function download()
    {
        $this->generate();
        header("Content-type:charset=utf-8");
        header("Content-Disposition:attachment; filename=".$this->config['output_filename']);
        header("Pragma:no-cache");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Expires:0");

        echo stream_get_contents($this->file);
        fclose($this->file);
    }

    public function stream()
    {
        $this->generate();
        return $this->file;
        fclose($this->file);
    }
}