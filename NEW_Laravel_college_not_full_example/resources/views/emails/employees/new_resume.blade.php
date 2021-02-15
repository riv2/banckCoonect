<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<p>Новая анкета на вакансию</p>

		<p>Анкета: {{ route('employees.show.candidate.resume', ['id' => $id]) }}</p>
	</body>
</html>
 