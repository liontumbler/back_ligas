<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="Edwin Velasquez Jimenez" />
        <meta name="copyright" content="Copyright AdminLig Edwin Velasquez Jimenez" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <style>
            .text-blanco {
                color: #ffffff !important;
            }
            
            .bg-body {
                background-image: linear-gradient(#000000, #00000047, #000000);/*, url("{{ asset('img/cheer fondo.jpg') }}"); */
                background-color: "#7a7049" !important;
                background-position: center center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-size: cover;
                background-color: #ffffff;
            }
        </style>
        <link rel="icon" type="image/png" href="{{ asset('img/adminLig.ico') }}">

        <meta name="description" content="Descripcion de pagina. No superar los 155 caracteres." />
        <meta name="keywords" content="keyword 1, keyword 2, keyword 3"/>
        <meta name="revisit" content="2 days">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="canonical" href="{{ env('APP_URL') }}"/>

        <title>@yield('title')</title>

        @yield('importar')
    </head>
    <body class="bg-body">
        @yield('content')
    </body>
</html>
