<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controlador de autenticación de sesión.
 *
 * Gestiona el formulario de inicio de sesión (`/signin`), el proceso de
 * autenticación con credenciales `username`/`password` y el cierre de sesión.
 * El endpoint POST `/signin` está limitado por el rate limiter `login`
 * (5 intentos por minuto por usuario e IP) para mitigar fuerza bruta.
 */
class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return Response Vista Inertia `Auth/Signin`.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Signin');
    }

    /**
     * Autentica al usuario con sus credenciales.
     *
     * En caso de éxito regenera la sesión (evita session fixation) y redirige
     * a la página prevista (por defecto el dashboard). Si falla, devuelve al
     * formulario con un error de credenciales y conserva el username.
     *
     * @return RedirectResponse Redirección al dashboard o de vuelta al formulario.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => __('Las credenciales proporcionadas no coinciden con nuestros registros.'),
        ])->onlyInput('username');
    }

    /**
     * Cierra la sesión del usuario autenticado.
     *
     * Invalida la sesión y regenera el token CSRF, redirigiendo al formulario
     * de inicio de sesión.
     *
     * @return RedirectResponse Redirección a `signin`.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('signin');
    }
}
