<?php

namespace App\Enums;

/**
 * Tipo de movimiento de préstamo.
 */
enum TipoMovimientoEnum: string
{
    case Salida = 'salida';
    case Retorno = 'retorno';
}
