@extends('layouts.app')

@section('content')
    <registers-component :role="'reviewer'" :registers="{{$students}}" :userid="{{auth()->user()->id}}"></registers-component>
@endsection