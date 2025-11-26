<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string"],
        ]);

        $remember = $request->boolean("remember", false);

        if (!Auth::guard("web")->attempt($credentials, $remember)) {

            // Si es JSON → respuesta JSON para Nuxt
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas.'
                ], 422);
            }

            // Si viene del formulario Blade → volver al login
            return back()
                ->withErrors(["email" => "Las credenciales no son válidas."])
                ->onlyInput("email");
        }

        $request->session()->regenerate();

        // Si la petición espera JSON → respuesta para Nuxt
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login correcto',
            ]);
        }

        // Si viene del formulario Laravel → redirige al dashboard
        return redirect()->to('/admin/dashboard');
    }

    /**
     * Registro básico de usuarios (opcional para tu SPA).
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                "unique:users",
            ],
            "password" => ["required", "string", "min:8", "confirmed"],
        ]);

        $user = User::create([
            "name" => $data["name"],
            "email" => $data["email"],
            "password" => Hash::make($data["password"]),
        ]);

        return response()->json(
            [
                "message" => "User registered successfully.",
                "user" => $user,
            ],
            201,
        );
    }

    /**
     * Login para SPA (Nuxt + Sanctum en modo cookie).
     */
    public function loginSpa(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string"],
        ]);

        $remember = $request->boolean("remember", false);

        // IMPORTANTE: usar guard('web')
        if (!Auth::guard("web")->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                "email" => ["The provided credentials are incorrect."],
            ]);
        }

        // Regenerar sesión para evitar fixation
        $request->session()->regenerate();

        /** @var User $user */
        $user = $request->user();

        return response()->json([
            "user" => $this->buildUserPayload($user),
        ]);
    }

    /**
     * Logout para SPA (cierra sesión + invalida sesión + regenera token CSRF).
     */
    public function logoutSpa(Request $request): JsonResponse|RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Si viene de Nuxt (fetch/axios, API) → JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out.',
            ]);
        }

        // Si viene de un form HTML (Blade) → redirige al login
        return redirect()->to('/admin/login');
    }

    /**
     * Endpoint /user para Nuxt (devuelve usuario + roles/permisos).
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(null, 204);
        }

        return response()->json($this->buildUserPayload($user));
    }

    /**
     * Login para API (token personal de Sanctum).
     * Ideal para integraciones externas o clientes móviles.
     */
    public function loginApi(Request $request): JsonResponse
    {
        $data = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "string"],
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where("email", $data["email"])->first();

        if (!$user || !Hash::check($data["password"], $user->password)) {
            throw ValidationException::withMessages([
                "email" => ["The provided credentials are incorrect."],
            ]);
        }

        $token = $user->createToken("api-token")->plainTextToken;

        return response()->json([
            "message" => "Login successful.",
            "token" => $token,
        ]);
    }

    /**
     * Logout para API token (revoca el token actual).
     */
    public function logoutApi(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            "message" => "Logged out successfully.",
        ]);
    }

    /**
     * Arma el payload estándar de usuario (incluye Spatie roles/permissions).
     */
    protected function buildUserPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // Roles asignados al usuario (Spatie\Permission)
            'roles' => $user->getRoleNames()->values()->all(),
            // Permisos directos + heredados por roles
            'permissions' => $user->getAllPermissions()
                ->pluck('name')
                ->values()
                ->all(),
        ];
    }
}
