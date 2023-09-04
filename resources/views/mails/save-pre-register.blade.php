<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
	    <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <title>D&TEC</title>
	</head>
	<body style="background-color: white; color: black;">
        <h2>Estimado {{ $student->name }}</h2>
		@if($student->check == 'accepted')
			<p>Tu pago ha sido verificado exitosamente, nos contactaremos contigo en un periodo de 24-48 hrs, para así acordar los detalles necesarios y guiarte a realizar tu curso/certificación exitosamente.</p>
		@endif
		@if($student->check == 'rejected')
			<p>No pudimos validar tu pago, por favor te solicitamos que vuelvas a registrarte y verifiques los datos que ingreses de tu comprobante de pago.</p>
		@endif
		<p>En caso de que tengas alguna duda/comentario, favor de comunicarte por llamada o whatsapp al 55 7458 6102 para darte un acompañamiento inmediato.</p>
        <hr>
		<p>Por favor no respondas este correo, ya que solo es de envió y tus respuestas no serán leídas.</p>
		<hr>
		<p>ATTE.</p>
		<h2><b>Digital and Technological English Center</b></h2>
	</body>
</html>