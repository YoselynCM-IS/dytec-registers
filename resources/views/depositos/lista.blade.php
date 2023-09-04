@extends('layouts.app')

@section('content')
    <folios-component role="{{auth()->user()->role}}"></folios-component>
@endsection