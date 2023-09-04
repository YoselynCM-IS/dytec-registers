@extends('layouts.app')

@section('content')
   <codes-component :registers="{{$students}}"></codes-component> 
@endsection