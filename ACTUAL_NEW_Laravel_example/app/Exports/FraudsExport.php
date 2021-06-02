<?php

namespace App\Exports;

use App\Filters\QueryFilter;
use App\Models\BannedFraud;
use App\Models\Order;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class FraudsExport implements FromQuery, ShouldQueue, WithHeadings, WithColumnFormatting, WithMapping, WithCustomChunkSize, Responsable, WithColumnWidths
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
        return [
            'Рекламодатель',
            'ЮЛ партнера',
            'Партнер',
            'Email',
            'Подсетка',
            'Номер заявки',
            'Дата заявки',
            'Ссылка',
            'Статус заявки',
            'Сумма',
            'Фродовый заказ',
            'Комментарий',
            'Доказательство',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 10,
            'D' => 20,
            'E' => 20,
            'F' => 24,
            'G' => 24,
            'H' => 15,
            'I' => 10,
            'J' => 20,
            'K' => 8,
            'L' => 50,
            'M' => 50,
        ];
    }

    /** @return \Illuminate\Database\Query\Builder */
    public function query()
    {
        /** @var \Illuminate\Database\Query\Builder */
        $frauds = BannedFraud::query()
            ->filter($this->filter)
            ->with([
                'order',
            ])
            ->orderBy('created_at', 'desc');
        return $frauds;

    }

    /**
     * @return array
     * @var Order $row
     */
    public function map($row): array
    {
        $advert = User::query()
            ->where('id', '=', auth()->user()->pp->user_id)
            ->first();
        $partner = User::query()
            ->where('id', $row->order->partner_id)
            ->with('pay_methods')->first();
        if (!empty($partner->pay_methods[0])) {
            $company = $partner->pay_methods[0]->pivot->company_name;
        } else {
            $company = '';
        }
        $order = $row->order;
        return [
            $advert->name,
            $company,
            $partner->id,
            $partner->email,
            $order->web_id,
            $order->order_id,
            $order->datetime,
            $order->link_id,
            $order->status,
            $order->amount,
            'Да',
            $row->comment,
            $row->evidence,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
