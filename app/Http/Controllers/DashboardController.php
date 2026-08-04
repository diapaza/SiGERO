<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\Objeto;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
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
