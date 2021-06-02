<?php

namespace App\View\Components;

use App\Helpers\TableColumn;
use Illuminate\View\Component;

class Table extends Component
{
    public $format;
    public $data;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(object $data, $format = null)
    {
        $this->data = $data;
        $this->format = $format;
        // collect();
        // if ($format) {
        //     foreach ($format as $column) {
        //         $col = new TableColumn($column);
        //         $this->format->push($col);
        //     }
        // } elseif ($data) {
        //     $first = $data[0];
        //     if ($first) {
        //         $first = array_keys($first->toArray());
        //         foreach ($first as $column_name) {
        //             $col = new TableColumn();
        //             $col->column = $column_name;
        //             $col->title = $column_name;

        //             $this->format->push($col);
        //         }
        //     }
        // }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.table', [
            'format' => $this->format,
            'data' => $this->data,
        ]);
    }
}
