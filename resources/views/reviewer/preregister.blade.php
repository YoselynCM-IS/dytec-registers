@extends('layouts.app')

@section('content')
    <pre-register-component :registers="{{$schools}}" :tipo="'interno'"></pre-register-component>
@endsection
