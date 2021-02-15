<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		Поступила новая заявка на литературу <a href="{{ route('add.literature.to.catalog.page', ['id' => $literature->id]) }}">{{ $literature->name }}</a>
	</body>
</html>
 