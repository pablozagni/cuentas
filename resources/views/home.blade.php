@extends('layouts.app')
@section('body')
    <h1>Home</h1>
    <a href="{{ route('cuentas.index') }}">
        <button class="btn btn-primary">Cuentas</button>
    </a>
@endsection