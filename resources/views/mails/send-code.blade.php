<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>Majestic Education</title>
	</head>
	<body style="background-color: white; color: black;">
        <h2>Hola {{ $name }}</h2>
		<p>
			Te enviamos el código para acceder al libro {{ $book }}:
		</p>
	    @if($editorial === 'MAJESTIC EDUCATION')
			<ol>
				<li>Ingresar a: https://www.blinklearning.com/home</li>
				<li>Descarga el <a href="https://dl.dropbox.com/s/kbv18zto09ky76w/Manual_alumno_blink_ME.pdf"><b>Manual</b></a> para poder registrarte.</li>
			</ol>
			@if($code2 === 'BLINK')
				<b>TU CÓDIGO ES: {{ $code }}</b>
			@else
				<b>TU CÓDIGO DE BAE 2 ES: {{ $code }}</b><br>
				<b>TU CÓDIGO DE BAE 4 ES: {{ $code2 }}</b>
			@endif
		@endif
		@if($editorial === 'EXPRESS PUBLISHING')
			<p>Ingresa a la URL: <a target="_blank" href="https://www.expressdigibooks.com/site/register">https://www.expressdigibooks.com/site/register</a></p>
			<b>CÓDIGO(S):</b>
			<ol>
				<li>{{ $code }}</li>
				<li>{{ $code2 }}</li>
			</ol>
			<p>Descarga la <a href="https://dl.dropbox.com/s/8lwce27hv6bc69s/ep-digibooks.pdf"><b>Guia</b></a> para poder acceder al libro</p>
		@endif
		@if($editorial === 'CENGAGE')
			<b>TU CÓDIGO DE VITALSOURCE: {{ $code }}</b><br>
			<b>TU CÓDIGO DE MyELT: {{ $code2 }}</b>
			<p>Descarga los instructivos para poder acceder al libro</p>
			<ol>
				<li><a href="https://dl.dropbox.com/s/e267o0cll0auzz4/Carta%20de%20entrega%20ebook%20VitalSource.pdf">Carta de entrega ebook VitalSource</a></li>
				<li><a href="https://dl.dropbox.com/s/0ulozdfblany6g1/myELT_template.pdf">MyELT</a></li>
				<li><a href="https://dl.dropbox.com/s/7keq4kzef7kiz7h/VitalSource_template.pdf">VitalSource</a></li>
			</ol>
		@endif
		@if($editorial === 'RICHMOND')
			<p>Revisa con tu profesor para poder acceder a tu libro.</p>
			<b>TU CÓDIGO ES: {{ $code }}</b>
			<p>Descarga el <a href="https://dl.dropbox.com/s/r4gj72fr5nmix60/student-book_richmond.pdf"><b>instructivo</b></a> para poder acceder al libro.</p>
		@endif
		@if($editorial === 'CLE')
			<p>Ingresar a: https://biblio.manuel-numerique.com/</p>
			<ol>
				<li><b>TU CÓDIGO ES: {{ $code }}</b></li>
				<li>TU USUARIO ES: {{ $code2 }}</li>
				<li>TU CONTRASEÑA ES: {{ $code3 }}</li>
			</ol>
		@endif
	    <hr>
		<p>Por favor no respondas este correo, ya que solo es de envió y tus respuestas no serán leídas. Si tienes alguna duda o aclaración, contáctanos al siguiente número.</p>
		<hr>
		<h2><b>Majestic Education</b></h2>
		Dudas o Aclaraciones: <br>
		56 2741 1481 <br>
		56 2741 0930 <br>
		<strong>Horario de atención</strong><br>
		<ul>
			<li>Lunes a Viernes de 10:00 am - 5:00 pm</li>
			<li>Sábado de 10:00 am - 1:00 pm </li>
		</ul>
	</body>
</html>