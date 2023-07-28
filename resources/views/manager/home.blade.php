@extends('layouts.app')

@section('content')
    <registers-component :role="'manager'" :registers="{{$students}}" :userid="{{auth()->user()->id}}"></registers-component>
@endsection