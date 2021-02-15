Ошибка пополениния 1с на сайте {{getcong('site_name')}}:

<p><strong>Подробные данные:</strong></p>
<p> <strong>ФИО:</strong> {{ $orderData['fio'] }} </p>
<p> <strong>ИИН:</strong> {{ $orderData['iin'] }} </p>
<p> <strong>Номер транзакции в Kaspi банке:</strong> {{ $orderData['kaspi_transaction'] }} </p>

<p> <strong>Сумма:</strong> {{ $orderData['amount'] /100 }} тенге</p>

<p>Дата: {{ date('Y-m-d H:i:s') }} </p>
