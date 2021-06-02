<?php


namespace App\Exports\Sheets;

use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Pp;
use App\Models\Reestr;
use App\Models\UsersPayMethod;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class OrdersSheet implements FromQuery, WithTitle, WithColumnFormatting, WithHeadings, WithMapping
{
    private int $registry;
    private int $rowNumber = 1;
    private $pp;

    public function __construct(int $registry)
    {
        $this->registry = $registry;
        $this->pp = Pp::query()->find(Reestr::query()->find($this->registry)->pp_id);
    }

    /**
     * @return Order | OrdersProduct | \Illuminate\Database\Eloquent\Builder | \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        if ($this->pp->pp_target == 'products') {
            $rows = OrdersProduct::query()
                ->where('reestr_id', $this->registry)
                ->with([
                    'order',
                ])
                ->orderBy('datetime', 'desc');
        } else {
            $rows = Order::query()
                ->where('reestr_id', $this->registry)
                ->with([
                    'link',
                    'pp',
                ])
                ->orderBy('datetime', 'desc');
        }

        return $rows;
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
            'C' => NumberFormat::FORMAT_DATE_DATETIME,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_NUMBER,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_NUMBER_00,
            'Q' => NumberFormat::FORMAT_NUMBER_00,
            'R' => NumberFormat::FORMAT_NUMBER_00,
            'S' => NumberFormat::FORMAT_NUMBER_00,
            'T' => NumberFormat::FORMAT_NUMBER_00,
            'U' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID заявки',
            'Дата и время заявки / заказа',
            'Дата и время совершения Целевого действия',
            'ID Оффера',
            'Рекламодатель',
            'ID партнера',
            'ID площадки',
            'ФЛ/ЮЛ',
            'Наименование партнёра',
            'ИНН',
            'НДС (да/нет)',
            'E-mail партнера',
            'Название ссылки',
            'Название продукта / Наименование товара',
            'Категория',
            'Стоимость товара, руб.',
            'Тариф выплаты, размер вознаграждения',
            'Дополнительное вознаграждение',
            'Коэф. Тарифа',
            'Выплата',
            'Сумма вознаг-ия, руб.',
        ];
    }

    /**
     * @param Order | OrdersProduct $row
     * @return array
     */
    public function map($row): array
    {
        $this->rowNumber++;
        $user = User::query()
            ->where('id', '=', $row->partner_id)
            ->with('pay_methods')
            ->first();
        $partnerPayment = $user
            ->partner_payments()
            ->where('reestr_id', '=', $this->registry)
            ->where('partner_id', '=', $user->id)
            ->first();
        $usersPayMethod = UsersPayMethod::query()
            ->where('id', '=', $partnerPayment->pay_account)
            ->withoutGlobalScopes()
            ->first();
        $tax = 1;
        $isVAT = 'Нет';
        $legalStatus = 'ФЛ';

        if ($usersPayMethod->vat_tax) {
            $tax = 1.2;
            $isVAT = 'Да';
        }

        if ($usersPayMethod->pay_method_id == 1) {
            $legalStatus = 'ЮЛ';
        }

        if (!is_null($usersPayMethod)) {
            $company_name = $user->pay_methods[0]->pivot->company_name;
            $company_inn = $user->pay_methods[0]->pivot->company_inn;
        } else {
            $company_name = 'Внимание, партнёр не заполнил способ оплаты!';
            $company_inn = 'Внимание, партнёр не заполнил способ оплаты!';
        }

        if ($this->pp->pp_target == 'products') {
            $map = [
                $row->order_id,
                $row->datetime,
                $row->order->datetime_sale,
                $row->offer_id,
                $this->pp->short_name,
                $row->partner_id,
                $row->web_id,
                $legalStatus,
                $company_name,
                $company_inn,
                $isVAT,
                $user->email,
                $row->order->link->link_name,
                $row->product_name, //Наименование товара product_name
                $row->category, //Категория - category
                $row->total, //Стоимость товара, руб. - total
                $row->amount, //Тариф выплаты, размер вознаграждения - fee / amount
                '', //Дополнительное вознаграждение - ??
                $tax, //Коэф. Тарифа - НДС - $tax
                '=(1+R' . $this->rowNumber . ')*Q' . $this->rowNumber . '*S' . $this->rowNumber . '', //Выплата - формула
                '=P' . $this->rowNumber . '*T' . $this->rowNumber . '', //Сумма вознаг-ия, руб. - формула - amount * $tax
            ];
        } else {
            $map = [
                $row->order_id,
                $row->datetime,
                $row->datetime_sale,
                $row->offer_id,
                $this->pp->short_name,
                $row->partner_id,
                $row->web_id,
                $legalStatus,
                $company_name,
                $company_inn,
                $isVAT,
                $user->email,
                $row->link->link_name,
                'Лиды', //Название продукта
                '', //Категория - category
                '', //Стоимость товара, руб. - total
                $row->fee, //Тариф выплаты, размер вознаграждения - fee / amount
                '', //Дополнительное вознаграждение - ??
                $tax, //Коэф. Тарифа - НДС - $tax
                '', //Выплата - НДС - ???
                '=Q' . $this->rowNumber . '*S' . $this->rowNumber . '', //Сумма вознаг-ия, руб. - формула - amount * $tax
            ];
        }

        return $map;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Детализация';
    }
}
