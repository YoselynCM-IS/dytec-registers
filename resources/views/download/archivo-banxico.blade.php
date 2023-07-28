<!doctype html>
<html>
    <head>
        <title>students</title>
    </head>
    <body>
        <ul>
            @foreach($registros as $registro)
                <li>{{ $registro['date'] }},{{ $registro['clave'] }},{{ $registro['clave_emisor'] }},40012,012180001724272063,{{ $registro['total'] }}</li>
            @endforeach 
        </ul>
    </body>
</html>
