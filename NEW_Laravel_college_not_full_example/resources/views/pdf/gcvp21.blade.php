@include('pdf._headInvisible')


<div style="font-size: 55%;">
	<table style="font-size: 0.8em; width: 100%;">
		<tr>
			<td style="text-align: left; width: 50%;">
			</td>
			<td style="text-align: right; width: 50%;">
				Приложение 2-1<br />
				к стандарту государственноой услуги<br />
				«Назначение государственных социальных<br />
				пособий по инвалидности, по случаю потери<br />
				кормильца и по возрасту»<br />
 				Форма
			</td>
		</tr>
	</table>


	<h2 style="width: 100%; text-align: center;">СПРАВКА</h2>



	<p>Дана гражданину {{$profile->fio}} {{date('d-m-Y', strtotime($profile->bdate))}}</p>
	 
	<p style="text-align: center;"><strong style="text-decoration: underline;">«Мирас» Университетінде</strong><br />
	<span style="font-size: 0.8em;">(полное название учебного заведения)</span></p>

	<p style="text-align: center;">серия АА №0000170 2003г. 18 август </p>


	<p style="text-align: center;"><span style="text-decoration: underline;">Дата истечения срока действия</span><br />
	<span style="font-size: 0.8em;">(указать №, дату и срок действия лицензии, дающей право на осуществление образовательной деятельности)</span></p>


	<p> {{$profile->course}} курса, формы обучения {{__($profile->education_study_form)}}<br />   
	Справка действительна на {{date('Y')}}/{{date('Y')+1}} учебный год.</p>


	<p>Справка выдана для предъявление в отделение Государственной корпорации.</p>

	@if(isset($enterDate))
		<p>Срок обучения в учебном заведении {{ $studyPeriod }} года, период обучения с 
		{{--@if($transferedStudent)
			{{date('d.m.Y', $enterDate)}} 
		@else
			01.09.{{date('Y', $enterDate)}} 
		@endif--}}
		{{date('d.m.Y', $enterDate)}} 
	по 01.07.{{ date('Y') + $studyPeriodLeft }}</p>
	@endif

	<p>Примечание: справка действительна 1 год.</p>

	<p style="font-size: 0.8em;">В случаях отчисления обучающегося из учебного заведения или перевода на заочную форму обучения, руководитель учебного заведения извещает отделение Государственной корпорации по месту жительства  получателя пособия.</p>
  
</div>




@include('pdf._footSign')