<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador del Dashboard (`/`).
 *
 * Compone las estadísticas generales del sistema, los datos de los gráficos
 * (solo para usuarios con permiso `ver reportes`) y la lista de los 10
 * objetos actualmente prestados. La vista `Dashboard` se refresca por polling
 * cada 30 segundos desde el frontend.
 */
class DashboardController extends Controller
{
    /**
     * Renderiza la página principal del dashboard.
     *
     * Props que recibe la vista `Dashboard`:
     * - `estadisticas`: totales (total, disponibles, prestados, eliminados).
     * - `usuariosTotal`: cantidad de usuarios activos.
     * - `movimientosPorMes`: movimientos agrupados por año/mes y tipo (solo `ver reportes`).
     * - `objetosPorCategoria`: conteo de objetos por categoría (solo `ver reportes`).
     * - `objetosPrestados`: hasta 10 objetos con `disponible = false` y su movimiento activo.
     *
     * @return Response Vista Inertia `Dashboard`.
     */
    public function index(Request $request): Response
    {
        $estadisticas = Objeto::estadisticas();
        $usuariosTotal = User::count();

        $tieneReportes = $request->user()->hasPermissionTo('ver reportes');

        if ($tieneReportes) {
            $movimientosPorMes = Movimiento::selectRaw('
                    YEAR(fecha_hora) as anio,
                    MONTH(fecha_hora) as mes,
                    tipo_movimiento,
                    COUNT(*) as total
                ')
                ->where('fecha_hora', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('anio', 'mes', 'tipo_movimiento')
                ->get()
                ->map(fn ($item) => [
                    'anio' => (int) $item->anio,
                    'mes' => (int) $item->mes,
                    'tipo_movimiento' => $item->tipo_movimiento,
                    'total' => (int) $item->total,
                ]);

            $objetosPorCategoria = Categoria::withCount('objetos')
                ->get()
                ->map(fn ($cat) => [
                    'nombre' => $cat->nombre,
                    'total' => $cat->objetos_count,
                ]);
        } else {
            $movimientosPorMes = collect();
            $objetosPorCategoria = collect();
        }

        $objetosPrestados = Objeto::with(['movimientoActivo.user', 'movimientoActivo.registradoPor'])
            ->where('disponible', false)
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return Inertia::render('Dashboard', [
            'estadisticas' => $estadisticas,
            'usuariosTotal' => $usuariosTotal,
            'movimientosPorMes' => $movimientosPorMes,
            'objetosPorCategoria' => $objetosPorCategoria,
            'objetosPrestados' => $objetosPrestados,
        ]);
    }
}
