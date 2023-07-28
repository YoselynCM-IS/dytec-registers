<!doctype html>
<html>
    <head>
        <title>students</title>
    </head>
    <body>
        <table>
            <tr>
                <th><b>Fecha de registro</b></th>
                <th><b>Escuela</b></th>
                <th><b>Alumno</b></th>
                <th><b>Libro</b></th>
                <th><b>Fecha de pago</b></th>
                <th><b>Tipo de pago</b></th>
                <th><b>Banco</b></th>
                <th><b>Referencia</b></th>
                <th><b>Concepto</b></th>
                <th><b>Importe</b></th>
                <th><b>Fecha</b></th>
                <th><b>Concepto</b></th>
                <th><b>Abono</b></th>
            </tr>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->created_at->format('Y-m-d h:m:s') }}</td>
                    <td>{{ $student->school->name }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->book }}</td>
                    <!-- <td> -->
                        @foreach($student->registros as $registro)
                            <!-- <tr> -->
                                <td>{{ $registro->date }}</td>
                                <td>{{ $registro->type }}</td>
                                <td>{{ $registro->bank }}</td>
                                <td>{{ $registro->invoice }}</td>
                                <td>{{ $registro->auto }}</td>
                                <td>{{ $registro->total }}</td>
                                @if($registro->folio !== NULL)
                                    <td>{{ $registro->folio->fecha }}</td>
                                    <td>{{ $registro->folio->concepto }}</td>
                                    <td>{{ $registro->folio->abono }}</td>
                                @endif
                            <!-- </tr> -->
                        @endforeach
                    <!-- </td> -->
                </tr>
            @endforeach 
        </table>
    </body>
</html>
