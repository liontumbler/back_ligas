<?php

namespace App\Enums;

enum TipoEntreno: string
{
    case Individial = 'individual';
    case Mensualidad = 'mensualidad';
    case Equipo = 'equipo';
}