@include('pdf._head')



	
	<h2 style="width: 100%; text-align: center; font-size: 130%;">СПРАВКА</h2>


<div style="font-size: 100%;">
	<p>Дана {{$user->studentProfile->fio}}</p>
	<p>В том, что он(-а) в {{date('Y', $enterDate)}} году поступил в Университет «Мирас» и в настоящее время обучается на:</p>
	<p>Курс: {{$user->studentProfile->course}}</p>
	@if(isset($disciplines[0]->speciality))<p>Специальность:  {{ $disciplines[0]->speciality }} </p>@endif
	<p>Форма обучения:  {{__($user->studentProfile->education_study_form)}}</p>
	<p>(Приказ о зачислении {{$enderDocId}}).</p>
	<p>Доступный остаток денежных средств на {{date('d-m-Y')}}  {{$balance}} тенге</p>
	<p> </p>
	<p>Студент обучается на платной основе, без выплаты стипендии.</p>
	<p>Справка дана для предъявления по месту требования.</p>
	<p>Дата выдачи: {{date('d-m-Y')}}</p>

</div>



@include('pdf._footQR')