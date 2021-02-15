<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		@if($status == 'declined')
			<p>Ваша заявка на должность {{ $position }} отклонена.</p>
			<p><b>Причина:</b> {{ $reason }}</p>
		@else
			<p>Ваша заявка на должность {{ $position }} подтверждена.</p>
		@endif
	</body>
</html>
 