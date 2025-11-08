@extends('errors.layout')

@section('title')
    @yield('title')
@endsection

@section('content')

<!-- @.  vite(['resources/css/error.css']) -->

<div class="container container p-4 bg-dark rounded-bottom-3">
    <div class="row">
        <div class="col-lg-12">
            <!-- <img src="{ Vite:    :asset('resources/img/error.svg') }}" alt="Nocarga"
                style="width: 291px; display: block; margin: auto;"> -->
        </div>
        <div class="col-lg-12">
            <h1 class="text-white text-center" style="font-size: calc(4.375rem + 1.5vw);">@yield('numberError')</h1>
        </div>
        <div class="col-lg-12">
            <h1 class="text-white text-center">@yield('stringError')</h1>
        </div>
        <div class="col-lg-12">
            <a class="btn btn-secondary btn-lg rounded-3" href="/">Ir al Home</a>
        </div>
    </div>
</div>
@endsection
