<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<p>Добро пожаловать в Miras Education!</p>
		<p>Мы рады приветствовать Вас в числе наших студентов!</p>
		<p>Для активации профиля пройдите по ссылке:: {!! link_to(route('emailConfirmCode', ['code' => $confirmation_code])) !!}.<br></p>
		<br />

		<p>Ваш логин: {{$email}}</p>
		@if(isset($password))
		<p>Ваш пароль: {{$password}}</p>
		@endif

		<p>
		<br />
		———————————————————————<br />
		Команда Miras Education<br />
		Tel.: +7 7252 33 77 77, +7 7252 33 77 77<br />
		e-mail: info@miras.edu.kz | www.miras.edu.kz www.miras.app<br />
		<img src="{{ URL::asset('assets/img/logo.png') }}" style="
		    background: #820933;
		    width: 100px;
		    margin-top: 10px;
		/>
		</p>





	</body>
</html>
 