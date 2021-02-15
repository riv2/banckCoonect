@include('pdf._headInvisible')


<div style="font-size: 55%;">

	<table style="font-size: 0.8em; width: 100%;">
		<tr>
			<td style="text-align: left; width: 50%;">
			</td>
			<td style="text-align: right; width: 50%;">
				Мемлекеттік әлеуметтік<br />
				сақтандыру қорынан төленетін<br />
				әлеуметтік төлемдерді тағайындау,<br />
				мөлшерін есептеу (айқындау),<br />
				қайта есептеу, сондай-ақ<br />
				олардың жүзеге асырылу<br />
				қағидаларына<br />
				6-қосымша<br />
				
			</td>
		</tr>
	</table>


	<h2 style="width: 100%; text-align: center;">АНЫҚТАМА</h2>



	<p>Азамат{{$profile->sex?'':'ша'}} {{$profile->fio}} {{date('d-m-Y', strtotime($profile->bdate))}}</p>


	<p style="text-align: center;">Ол іс жүзінде <strong style="text-decoration: underline;">«Мирас» Университетінде</strong><br />   
	<span style="font-size: 0.8em;">(оқу орнының толық атауы)</span><br />
	серия  АА  №0000170  2003ж.  18  маусым </p>

	<p style="text-align: center;"><strong style="text-decoration: underline;">қолдану  мерзімі  шектелмеген</strong> <br />   
	<span style="font-size: 0.8em;">(білім беру қызметін жүзеге асыруға құқық беретін  лицензияның №, берілген күні және қолданылу мерзімі көрсетілсін)</span></p>

	<p>{{$profile->course}} курсының {{ !empty($user->mgApplication) ? "магистратура" : "студенті" }}  болып табылады , оқу нысаны күндізгі<br />
	білім алушысы болып табылатындығы жөнінде берілді.<br />
	Анықтама  2019-2020 оқу жылына жарамды. Анықтама  Мемлекеттік корпорацияның бөлімшесіне ұсыну үшін берілді.<br />
	
	@if(isset($enterDate))
		Оқу орнындағы оқу мерзімі {{ $studyPeriod }} жыл. Оқу кезеңі 
		{{--@if($transferedStudent)
			{{date('d.m.Y', $enterDate)}} 
		@else
			01.09.{{date('Y', $enterDate)}} 
		@endif--}}
		{{date('d.m.Y', $enterDate)}} 
		нан 01.07.{{ date('Y') + $studyPeriodLeft }} дейін<br />
	@endif
	Ескертпе: анықтама 1 жылға жарамды.</p>
	<p style="font-size: 0.8em;">Білім алушы оқу орнынан шығарылған немесе сырттай оқу нысанына ауыстырылған  жағдайларда, оқу орнының басшысы әлеуметтік төлемді  алушының тұрғылықты жері бойынша  Мемлекеттік корпорацияның  бөлімшесін хабардар етеді.</p>
 

</div>



@include('pdf._footSign')