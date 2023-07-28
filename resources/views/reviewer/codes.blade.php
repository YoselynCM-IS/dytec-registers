@extends('layouts.app')

@section('content')
   <codes-component :registers1="{{$digitales}}" :registers2="{{$fisicos}}"></codes-component> 
@endsection