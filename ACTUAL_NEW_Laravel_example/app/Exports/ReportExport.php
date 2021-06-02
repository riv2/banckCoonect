<?php
/**
 * Project qpartners
 * Created by danila 29.06.2020 @ 13:17
 */

namespace App\Exports;
use App\Lists\OrderStateList;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ReportExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

    public $query = null;
    public $statuses = null;

    public function __construct($query)
    {
        $this->query = $query;
        $this->statuses = OrderStateList::getList();
    }

    public function headings(): array
    {
        return [
            "ID",
            "Дата",
            "Оффер",
            "Ссылка",
            "Сумма",
            "Комиссия",
            "Статус"
        ];
    }


    public function query()
    {
        return $this->query;
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }



    public function map($item): array
    {
        $res = [
            $item->order_id,
            $item->datetime,
            $item->offer_name,
            $item->link_name,
            $item->gross_amount,
            $item->amount_val,
            $this->statuses[$item->order_status],
        ];
        return $res;
    }

}
