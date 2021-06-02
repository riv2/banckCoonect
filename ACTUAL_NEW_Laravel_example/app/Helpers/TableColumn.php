<?php
namespace App\Helpers;

/**
 * Undocumented class
 */
class TableColumn
{
    public $column;
    public $title;
    public $type;
    public $format;

    public function __construct(array $params = null)
    {
        if ($params) {
            foreach ($params as $param_name => $param_value) {
                $this->{'set' . $param_name}($param_value);
            }
        }
    }

    public function setColumn($value)
    {
        $this->column = $value;
    }

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function setType($value)
    {
        $this->type = $value;
        if ($this->type === 'date' || $this->type === 'datetime') {
            $this->format = $this->format ?? 'd F Y H:i';
        }
    }

    public function setFormat($value)
    {
        $this->format = $value;
    }
}
