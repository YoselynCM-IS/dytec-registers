<!doctype html>
<html>
    <head>
        <title>students</title>
    </head>
    <body>
        <table>
            @foreach($schools as $school)
                <tr>
                    <th><b>{{ $school->name }}</b></th>
                    <th><b>Libros</b></th>
                    <th><b>Precio</b></th>
                </tr>
                @foreach($school->books as $book)
                    <tr>
                        <td></td>
                        <td>{{ $book->name }}</td>
                        <td>${{ number_format($book->pivot->price, 2) }}</td>
                    </tr>
                @endforeach 
                <tr></tr>
            @endforeach 
        </table>
    </body>
</html>
