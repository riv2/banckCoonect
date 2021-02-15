<p> </p>

<div style="text-decoration: underline;">
	<p style="font-size: 12px;">Файл был сгенерирован сайтом miras.app Для проверки документа отсканируйте QR-код ниже:</p>
	<p><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(150)->generate( route('documentCheck',[$filename]) )) !!} ">
	</p>
</div>


</body>

</html>