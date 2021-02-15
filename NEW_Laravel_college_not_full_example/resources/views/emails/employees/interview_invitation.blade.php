<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		@if($status == 'отклонено')
			<p>Ваша заявка на должность {{ $vacancy }} отклонена.</p>
			<p><b>Причина:</b> {{ $reason }}</p>
		@else
			<p>Ваше резюме на вакансию {{ $vacancy }} {{ $status }}.</p>
			@if($status == 'одобрено')
				<p>Вы приглашены на собеседование.</p>
			@endif
		@endif
	</body>
</html>
 