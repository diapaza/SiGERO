<?php

namespace App\Http\Controllers;

/**
 * Clase base de la que heredan todos los controladores de la aplicación.
 *
 * En SiGERO los controladores son delgados: delegan la lógica de negocio en
 * los servicios de `App\Services`. Esta clase no añade comportamiento por
 * defecto; sirve como punto común de extensión (middleware, helpers, etc.).
 */
abstract class Controller
{
    //
}
