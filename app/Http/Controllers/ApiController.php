<?php

namespace App\Http\Controllers;

use App\Models\Objeto;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
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
