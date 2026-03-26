<?php

namespace App\Enums;

enum EstadoOperacion: string
{
    case Pendiente = 'pendiente';
    case Completada = 'completada';
    case Cancelada = 'cancelada';
}
