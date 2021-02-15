<p>
Банк отклонил заявку на возврат {{ $statusWaiting->order_number }}</p>
ФИО: {{$user->studentProfile->fio}} <br /> 
ИИН: {{$user->studentProfile->iin}} <br /> 
20-ти значный IBAN номер карт-счета (KZ{{ $statusWaiting->user_iban}}) <br />
Сумма  {{ $statusWaiting->tiyn / 100}} тенге <br />
Коментарий банка "{{ $statusWaiting->bank_comment}}" <br />
Дата: {{ date('Y-m-d H:i:s') }} <br />
