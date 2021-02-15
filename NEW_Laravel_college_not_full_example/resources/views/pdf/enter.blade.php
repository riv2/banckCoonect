@include('pdf._headInvisible')


<div style="font-size: 55%;">
	<table style="font-size: 1.2em; width: 100%;">
		<tr>
			<td style="text-align: left; width: 50%;">
				
			</td>
			<td style="text-align: right; width: 50%;">
				Лицензия номер № 0000170
			</td>
		</tr>
	</table>

	<h2 style="width: 100%; text-align: center;">СПРАВКА</h2>

	<p>Дана {{$profile->fio}}</p>
	<p>

	@if(isset($enterDate))
		В том, что он{{$profile->sex?'':'а'}} в {{date('Y', $enterDate)}} году поступил{{$profile->sex?'':'а'}} в Университет «Мирас» и 
	@endif

	в настоящее время обучается на: {{$speciality}}</p>
	<p>Курс: {{$profile->course}}<br/>
	Специальность/образовательная программа: {{$speciality}}<br/>
	Форма обучения: {{__($profile->education_study_form)}}<br/>
	(Приказ о зачислении {{$enderDocId}}).</p>
	<p> </p>
	<p>Студент обучается на платной основе, без выплаты стипендии.</p>
	<p>Справка дана для предъявления по месту требования.</p>
	<p>Дата выдачи: {{date('d-m-Y')}}</p>

</div>

@include('pdf._footSign')