<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleAccess
{
    /**
     * Maneja una petición entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario no está autenticado, continuar (el middleware 'auth' se encargará si es necesario)
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $roleId = $user->role_id;
        $routeName = $request->route()?->getName();

        // Si la ruta no tiene un nombre asignado, permitir el acceso
        if (!$routeName) {
            return $next($request);
        }

        // Definición de las rutas permitidas para cada rol (id del CatalogoRole)
        // 1 = Administrador, 2 = Gerente, 3 = Gestion, 4 = Consulta, 5 = Auditor
        $roleRoutes = [
            1 => [
                '*', // Acceso absoluto y total sin restricciones
            ],
            2 => [
                'home',
                'dashboard',
                'dashboard.administrador',
                'dashboard.gerente',
                'dashboard.gestion',
                'dashboard.consulta',
                'dashboard.auditor',
                'users.index',
                'catalogo-roles.index',
                'inspeccion.tela',
                'settings.profile',
                'settings.password',
            ],
            3 => [
                'home',
                'dashboard',
                'dashboard.gestion',
                'settings.profile',
                'settings.password',
            ],
            4 => [
                'home',
                'dashboard',
                'dashboard.consulta',
                'settings.profile',
                'settings.password',
            ],
            5 => [
                'home',
                'dashboard',
                'dashboard.auditor',
                'inspeccion.tela',
                'settings.profile',
                'settings.password',
            ],
        ];

        // Rutas protegidas que queremos validar estrictamente por rol
        $protectedRoutes = [
            'dashboard.administrador',
            'dashboard.gerente',
            'dashboard.gestion',
            'dashboard.consulta',
            'dashboard.auditor',
            'users.index',
            'catalogo-roles.index',
            'inspeccion.tela',
            'settings.profile',
            'settings.password',
        ];

        // Validar si la ruta actual está en la lista de rutas protegidas
        if (in_array($routeName, $protectedRoutes)) {
            $allowed = $roleRoutes[$roleId] ?? [];

            // Si el rol del usuario no tiene acceso absoluto (*) y tampoco la ruta específica permitida
            if (!in_array('*', $allowed) && !in_array($routeName, $allowed)) {
                // Redirigir al dashboard general (que a su vez redirigirá al dashboard correcto de su rol)
                return redirect()->route('dashboard')->with('notify', [
                    'type' => 'error',
                    'message' => 'No tienes permiso para acceder a esta sección.',
                ]);
            }
        }

        return $next($request);
    }
}
