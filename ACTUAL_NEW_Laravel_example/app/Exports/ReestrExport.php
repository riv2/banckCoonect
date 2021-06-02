<?php

namespace App\Exports;

use App\Exports\Sheets\AgentReportSheet;
use App\Exports\Sheets\OrdersSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReestrExport implements WithMultipleSheets
{
    use Exportable;

    /** @var int */
    public int $timeout = 0;

    private int $registry;

    /**
     * ReestrExport constructor.
     * @param int $registry
     */
    public function __construct(int $registry)
    {
        $this->registry = $registry;
    }

    public function sheets(): array
    {
        $sheets = [];
        array_push($sheets, new OrdersSheet($this->registry));
        array_push($sheets, new AgentReportSheet($this->registry));

        return $sheets;
    }
}
