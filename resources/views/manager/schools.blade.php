@extends('layouts.app')

@section('content')
    <schools-component :registers="{{$schools}}"></schools-component>
@endsection