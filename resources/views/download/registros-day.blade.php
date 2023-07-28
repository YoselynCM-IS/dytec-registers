<!doctype html>
<html>
    <body>
        <table>
            <tr>
                <th colspan="4"><b>DATOS DEL ALUMNO</b></th>
                <th colspan="8"><b>REGISTRO DE PAGO DEL ALUMNO</b></th>
                <th colspan="3"><b>REFERENCIA DEL EDO. DE CTA.</b></th>
            </tr>
            <tr>
                <th><b>Fecha de registro</b></th>
                <th><b>Escuela</b></th>
                <th><b>Alumno</b></th>
                <th><b>Libro</b></th>
                <th><b>Num cuenta</b></th>
                <th><b>Fecha de pago</b></th>
                <th><b>Tipo de pago</b></th>
                <th><b>Banco</b></th>
                <th><b>Referencia</b></th>
                <th><b>Concepto</b></th>
                <th><b>Cajero / Sucursal</b></th>
                <th><b>Importe</b></th>
                <th><b>Fecha de pago</b></th>
                <th><b>Concepto</b></th>
                <th><b>Abono</b></th>
            </tr>
            @foreach($students as $registro)
                <tr>
                    <td>{{ $registro->fecha_registro }}</td>
                    <td>{{ $registro->school }}</td>
                    <td>{{ $registro->name }}</td>
                    <td>{{ $registro->book }}</td>
                    <td>{{ $registro->numcuenta }}</td>
                    <td>{{ $registro->date }}</td>
                    <td>{{ $registro->type }}</td>
                    <td>{{ $registro->bank }}</td>
                    <td>{{ $registro->invoice }}</td>
                    <td>{{ $registro->auto }}</td>
                    <td>{{ $registro->cajero }}</td>
                    <td>{{ $registro->total }}</td>
                    <td>{{ $registro->fecha }}</td>
                    <td>{{ $registro->concepto }}</td>
                    <td>{{ $registro->abono }}</td>
                </tr>
            @endforeach 
        </table>
    </body>
</html>
