<?php

namespace App\Enums;

enum EstadoLicencia: string
{
    case Activa = 'activa';
    case Inactiva = 'inactiva';
    case Vencida = 'vencida';
}