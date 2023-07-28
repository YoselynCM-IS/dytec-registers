@extends('layouts.app')

@section('content')
    <books-component :registers="{{$books}}"></books-component>
@endsection