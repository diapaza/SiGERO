<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de búsquedas internas de la aplicación.
 *
 * Contiene los únicos endpoints que responden JSON (no renderizan vistas
 * Inertia). Se usan desde el módulo de Movimientos para localizar objetos por
 * código y personas por DNI en tiempo real. Ambos requieren sesión iniciada y
 * el permiso correspondiente (ver rutas en `routes/web.php`).
 */
class ApiController extends Controller
{
    /**
     * Busca un objeto activo por su código (4 o 12 dígitos) con su marca y categoría.
     *
     * @param  string  $codigo  Código exacto del objeto.
     * @return JsonResponse Objeto encontrado con los campos públicos, o `404` con un mensaje.
     */
    public function searchObjeto(string $codigo): JsonResponse
    {
        $objeto = Objeto::with(['marca', 'categoria'])
            ->where('codigo', $codigo)
            ->first();

        if (! $objeto) {
            return response()->json(['message' => 'Objeto no encontrado.'], 404);
        }

        return response()->json([
            'id' => $objeto->id,
            'codigo' => $objeto->codigo,
            'nombre' => $objeto->nombre,
            'modelo' => $objeto->modelo,
            'serie' => $objeto->serie,
            'descripcion' => $objeto->descripcion,
            'foto' => $objeto->foto,
            'disponible' => $objeto->disponible,
            'marca' => $objeto->marca?->nombre,
            'categoria' => $objeto->categoria?->nombre,
        ]);
    }

    /**
     * Busca un usuario activo por su DNI (8 dígitos).
     *
     * @param  string  $dni  DNI exacto del usuario.
     * @return JsonResponse Usuario encontrado con los campos públicos, o `404` con un mensaje.
     */
    public function searchUser(string $dni): JsonResponse
    {
        $user = User::where('dni', $dni)->first();

        if (! $user) {
            return response()->json(['message' => 'Persona no encontrada.'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'dni' => $user->dni,
            'nombres' => $user->nombres,
            'apellidos' => $user->apellidos,
            'name' => $user->name,
            'whatsapp_number' => $user->whatsapp_number,
        ]);
    }
}
