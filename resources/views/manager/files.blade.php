@extends('layouts.app')

@section('content')
    <files-component :registers="{{$files}}"></files-component>
@endsection