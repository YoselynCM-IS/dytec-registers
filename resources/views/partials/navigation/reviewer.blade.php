<li class="nav-item">
    <a class="nav-link" href="{{ route('registros.home') }}">{{ __('Registros') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('registros.entregas') }}">{{ __('Entregas') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('folios.lista') }}">{{ __('Depósitos') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('books.lista') }}">{{ __('Certificaciones') }}</a>
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Cortes
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    <li><a class="dropdown-item" href="{{ route('registros.categories.lista') }}">Lista</a></li>
        <li>
            <a class="dropdown-item" href="{{ route('registros.categories.revisions') }}">{{ __('Revisión') }}</a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('registros.schools') }}">{{ __('Escuelas') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('registro-pago') }}">{{ __('Registro') }}</a>
</li>