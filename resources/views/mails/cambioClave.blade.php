@extends('layout.layout')

@section('title', 'Código de caja')

@section('content')
<div class="container">
    <img src="{{ asset('img/adminLig.svg') }}" alt="No carga" class="w-50 d-block m-auto">
    <h1 class="text-center mt-2">AdminLig</h1>

    <p class="text-center">
        <strong>Hola!</strong> {{ $usuario ?? 'vacio'}}
    </p>

    <p class="text-center">
        El código para ingresar a su caja el día de hoy: <strong>{{ now()->format('Y-m-d H:i:s') }}</strong>
    </p>

    <p class="text-center">
        <strong>La clave es: {{ $claveCajaNueva }}</strong>
    </p>

    @include('layout.footer')
</div>
@endsection
