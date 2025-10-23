<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// Clases necesarias para sobrescribir 'register'
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    */

    use RegistersUsers;

    /**
     * Donde redirigir a los usuarios después del registro (solo si el método register no se sobrescribe).
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // IMPORTANTE: Se ELIMINA el middleware 'guest' para permitir que el
        // Super Administrador (ya logeado) pueda acceder a la ruta.
        // La protección de acceso la maneja el 'role:Super Administrador' en routes/web.php.
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration, y le asigna el rol por defecto.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // ASIGNAR EL ROL POR DEFECTO
        // Asumimos que todos los usuarios creados aquí serán Solicitantes.
        $user->assignRole('Solicitante');

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * SOBREESCRITO: Se elimina la línea $this->guard()->login($user); 
     * para evitar que el Super Administrador sea deslogueado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // El método create() ahora se encarga de asignar el rol 'Solicitante'.
        event(new Registered($user = $this->create($request->all())));

        // LÍNEA ELIMINADA: $this->guard()->login($user); 

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // Redirige al Super Administrador de vuelta a la vista de registro con un mensaje de éxito

        return redirect()->route('register')->with('success', '✅ Usuario ' . $user->name . ' creado como Solicitante.');
    }
}