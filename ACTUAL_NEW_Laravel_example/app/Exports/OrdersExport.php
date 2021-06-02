<?php

namespace App\Exports;

use App\Filters\QueryFilter;
use App\Models\Order;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrdersExport implements FromQuery, ShouldQueue, WithHeadings, WithColumnFormatting, WithMapping, WithCustomChunkSize, Responsable
{
    use Exportable;

    public $timeout = 0;

    private $filter;
    private $user;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     */
    private $fileName = 'orders.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLSX;

    /** @var array Заголовки, отправляемые с файлом */
    private $headers = [
        'Pragma' => 'no-cache',
        'Cache-Control' => 'no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0',
        'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT'
    ];

    /** @return int */
    public function chunkSize(): int
    {
        return 10000;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(QueryFilter $filter)
    {
        $this->filter = $filter;
    }

    /** @return array */
    public function headings(): array
    {
        if (auth()->user()->role == 'partner')
        {
            return [
                'ID заявки',
                'Дата и время заявки',
                'ID Оффера',
                'Название оффера',
                'ID ссылки',
                'Название ссылки',
                'CLICK_ID',
                'WEB_ID',
                'Статус',
                'Сумма вознаграждения',
            ];
        }
        return [
            'ID заявки',
            'Дата и время заявки',
            'ID Оффера',
            'Название оффера',
            'ID партнера',
            'E-mail партнера',
            'ID ссылки',
            'Название ссылки',
            'CLICK_ID',
            'WEB_ID',
            'Статус',
            'Сумма вознаграждения',
        ];
    }

    /** @return \Illuminate\Database\Query\Builder */
    public function query()
    {
        /** @var \Illuminate\Database\Query\Builder */
        return Order::query()
            ->filter($this->filter)
            ->with([
                'partner',
                'link',
                'offer',
            ])
            ->orderBy('datetime', 'desc');
    }

    /**
     * @var Order $row
     * @return array
     */
    public function map($row): array
    {
        if ($row->partner) {
            $partner_id = $row->partner->id;
            $partner_email = $row->partner->email;
        } else {
            $partner_id = '';
            $partner_email = '';
        }

        if ($row->offer) {
            $offer_id = $row->offer->id;
            $offer_name = $row->offer->offer_name;
        } else {
            $offer_id = '';
            $offer_name = '';
        }

        if ($row->link) {
            $link_id = $row->link->id;
            $link_name = $row->link->link_name;
        } else {
            $link_id = '';
            $link_name = '';
        }
        if (auth()->user()->role == 'partner')
        {
            return [
                $row->order_id,
                $row->datetime,
                $offer_id,
                $offer_name,
                $link_id,
                $link_name,
                $row->click_id,
                $row->web_id,
                $row->readableStatus,
                $row->amount,
            ];
        }

        return [
            $row->order_id,
            $row->datetime,
            $offer_id,
            $offer_name,
            $partner_id,
            $partner_email,
            $link_id,
            $link_name,
            $row->click_id,
            $row->web_id,
            $row->readableStatus,
            $row->amount,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
            'C' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
