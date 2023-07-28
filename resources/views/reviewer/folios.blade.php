@extends('layouts.app')

@section('content')
    <folios-component :userid="{{auth()->user()->id}}" :role="'reviewer'"></folios-component>
@endsection