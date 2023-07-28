<!-- <li class="nav-item">
    <a class="nav-link" href="{{ route('manager.files') }}">{{ __('Archivos') }}</a>
</li> -->
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.home') }}">{{ __('Inicio') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.folios') }}">{{ __('Depósitos') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.movimientos') }}">{{ __('Movimientos') }}</a>
</li>
<li class="nav-item">
    
</li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Revisiones
    </a>
    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    <li><a class="dropdown-item" href="{{ route('manager.categories.lista') }}">Lista</a></li>
        <li>
            <a class="dropdown-item" href="{{ route('manager.categories.revisions') }}">{{ __('Revisión') }}</a>
        </li>
        <li><a class="dropdown-item" href="{{ route('manager.categories.pagos') }}">Pagos</a></li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.codes') }}">{{ __('Codigos') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.schools') }}">{{ __('Escuelas') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('manager.books') }}">{{ __('Libros') }}</a>
</li>