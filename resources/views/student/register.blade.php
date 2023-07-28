@extends('layouts.app')

@section('content')
    <!-- TUTORIAL -->
    <b-row class="mb-5">
        <b-col>
            <h5><b>Registro de pago</b></h5>
            <img src="{{ asset('images/logo-dytec.png') }}" alt="" width="450" height="200">
        </b-col>
        <b-col>
            <b-card class="mb-3 mt-2">
                <b-row>
                    <b-col><b>Requisitos</b></b-col>
                    <b-col class="text-right"><a href="#" class="text-right">Tutorial</a></b-col>
                </b-row>
                <hr>
                <ul>
                    <li>Acceder con el navegador Firefox o Chrome.</li>
                    <li>Un correo electrónico valido.</li>
                    <li>Numero de orden</li>
                    <li>Tener a la mano tu comprobante de pago.</li>
                </ul>
            </b-card>
        </b-col>
    </b-row>
    <pre-register-component :registers="{{$schools}}" :tipo="'externo'"></pre-register-component>
@endsection

@section('footer')
    <footer class="footer">
        <div class="container">
            <div class="row mt-3">
                <div class="col">
                    <strong>Horario de atención</strong><br>
                    <ul>
                        <li>Lunes a Viernes de 10:00 am - 5:00 pm</li>
                        <li>Sábado de 10:00 am - 1:00 pm </li>
                    </ul>
                </div>
                <div class="col">
                    <h6><strong>Contacto</strong></h6>
                    <ul>
                        <li>
                            <b-icon-telephone></b-icon-telephone> 56 2741 1481 <br>
                            <a target="_blank" href="https://wa.me/525627411481">
                                <b-icon-chat></b-icon-chat> Ir a WhatsApp
                            </a>
                        </li>
                        <li>
                            <b-icon-telephone></b-icon-telephone> 56 2741 0930 <br>
                            <a target="_blank" href="https://wa.me/525627410930">
                                <b-icon-chat></b-icon-chat> Ir a WhatsApp
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
@endsection
