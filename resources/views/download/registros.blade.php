<!doctype html>
<html>
    <head>
        <title>students</title>
    </head>
    <body>
        <table>
            <tr>
                <th><b>Fecha de registro</b></th>
                <th><b>Alumno</b></th>
                <th><b>Correo</b></th>
                <th><b>Escuela</b></th>
                <th><b>Libro</b></th>
                <th><b>Cantidad</b></th>
                <th><b>Precio</b></th>
                <th><b>Total</b></th>
            </tr>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->created_at->format('Y-m-d h:m:s') }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->school->name }}</td>
                    <td>{{ $student->book }}</td>
                    <td>{{ $student->quantity }}</td>
                    <td>${{ number_format($student->price, 2) }}</td>
                    <td>${{ number_format($student->total, 2) }}</td>
                </tr>
            @endforeach 
        </table>
    </body>
</html>
