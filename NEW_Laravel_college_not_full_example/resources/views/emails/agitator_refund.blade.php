<p>
    Запрос на агитаторскую выплату:
</p>
ФИО: {{$user->studentProfile->fio}} (id {{ $user->id }}) <br />
ИИН: {{$user->studentProfile->iin}} <br />
Банк {{$bank->name}}<br />
БИК Банка {{$bank->bic}}<br />
20-ти значный IBAN номер карт-счета (KZ{{ $iban }}) <br />
Общая сумма  {{ $cost }} тенге <br />
Дата: {{ date('Y-m-d H:i:s') }} <br />