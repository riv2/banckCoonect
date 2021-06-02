<?php


namespace App\Exports\Sheets;

use App\Models\Order;
use App\Models\Reestr;
use App\Models\UsersPayMethod;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;

class AgentReportSheet implements FromCollection, WithTitle
{
    private $reestr;

    public function __construct(int $reestr)
    {
        $this->reestr = $reestr;
    }

    public function collection(): Collection
    {
        $contracts = [];
        $reports = [];
        $partners = [];
        //Getting partner_ids from reestr orders
        $partner_ids = array_unique(Order::query()
            ->where('reestr_id', '=', $this->reestr)
            ->select('partner_id')
            ->pluck('partner_id')
            ->toArray()
        );

        //Getting partners and their payMethods what was used in reestr
        $totalPartnerPayments = 0;
        foreach ($partner_ids as $partner_id) {
            $partner = User::query()
                ->where('users.id', '=', $partner_id)
                ->first();
            $partnerPayment = $partner
                ->partner_payments()
                ->where('reestr_id', '=', $this->reestr)
                ->where('partner_id', '=', $partner->id)
                ->first();
            $usersPayMethod = UsersPayMethod::query()
                ->where('id', '=', $partnerPayment->pay_account)
                ->withoutGlobalScopes()
                ->first();

            if (!is_null($usersPayMethod)) {
                $contracts[] = [
                    '',
                    $usersPayMethod->company_name,
                    $usersPayMethod->company_inn,
                    $partner->contract_number
                ];

                if ($usersPayMethod->getOriginal('pivot_pay_method_id') == 1) {
                    $personType = 'ЮЛ';
                } else {
                    $personType = 'ФЛ';
                }
                $partners[] = [
                    '',
                    $partner->name,
                    $personType,
                    $usersPayMethod->company_inn,
                    $usersPayMethod->contract_number,
                    $partnerPayment->revenue,
                ];
                $totalPartnerPayments += $partnerPayment->revenue;
                $reports[] = [
                    '',
                    $usersPayMethod->contract_number,
                    $usersPayMethod->contract_date,
                    'выполнено',
                ];
            } else {
                $contracts[] = ['', 'Внимание, партнёр не заполнил способ оплаты!'];
            }
        }
        $reestr = Reestr::query()->find($this->reestr);
        $sumFee = $reestr->total * 0.1;

        return new Collection([
            ['1.', 'Заключенные договоры с Партнерами за отчетный период'],
            ['', 'ЮЛ', 'ИНН', 'номер договора'],
            $contracts,
            ['2.', 'Выполненные работы по управлению и оптимизации работы с CPA-сетями и партнерскими программами для интернет-сайтов Рекламодателей согласно п. 2.1.1.7 Договора'],
            ['', 'Отчет по заявкам за отчетный период'],
            ['', 'Наименование отчета', 'Дата и номер отчета', 'Статус'],
            $reports,
            ['', ''],
            ['', 'Партнеры, по которым в отчетном периоде было выявлено нарушение "Контекст на бренд"'],
            ['', 'ЮЛ', 'ИНН'],
            ['', 'X', 'XXX'],
            ['', ''],
            ['3.', 'Размер вознаграждения Агента за управление и оптимизацию работы с CPA-сетями и партнерскими программами'],
            ['', 'Наименование', 'Сайт рекламодателя', 'Стоимость услуг (руб. с НДС)'],
            ['', 'Мониторинг фрода', 'сайт почты банка', '102000'],
            ['', 'Мониторинг брендового контекста', 'сайт почты банка', '102000'],
            ['', 'Итого', '', '102000'],
            ['', 'Размер вознаграждения Партнеров к начислению за отчетный период'],
            ['', 'Наименование', 'ЮЛ/ФЛ', 'ИНН', 'номер договора', 'Сумма вознаг-ия, руб.'],
            $partners,
            ['', 'Итого', '', '', '', $totalPartnerPayments],
            ['', 'Детализация по целевым действиям на https://cpadroid.ru/'],
            ['', ''],
            ['', 'Размер вознаграждения Агента согласно п.3.1.1. Договора за отчетный период:'],
            ['', 'Сумма вознаграждения Партнерам за отчетный период', 'Размер вознаграждения Агента от суммы вознаграждения партнеров с учетом применимых налогов, %', 'Сумма вознаг-ния (руб.с НДС)'],
            ['', $reestr->total, '10', $sumFee],
            ['', 'Итого оплате (вкл.НДС):', '', $sumFee],
            ['', ''],
            ['', 'Итого размер вознаграждения Агента (вкл.НДС):', '', $sumFee],

        ]);
    }


    /**
     * @return string
     */
    public function title(): string
    {
        return 'Отчёт агента';
    }
}
