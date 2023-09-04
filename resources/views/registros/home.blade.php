@extends('layouts.app')

@section('content')
    <registers-component role="{{auth()->user()->role}}" :registers="{{$students}}" :userid="{{auth()->user()->id}}"></registers-component>
@endsection