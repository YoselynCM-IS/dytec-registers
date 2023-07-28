@extends('layouts.app')

@section('content')
    <registers-component :role="'administrator'" :registers="{{$students}}" :userid="{{auth()->user()->id}}"></registers-component>
@endsection