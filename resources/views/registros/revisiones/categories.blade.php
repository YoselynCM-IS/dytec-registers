@extends('layouts.app')

@section('content')
    <rev-list-categories-component role="{{auth()->user()->role}}"></rev-list-categories-component>
@endsection