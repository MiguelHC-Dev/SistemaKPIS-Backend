<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('activo', true)->get();

        return response()->json([
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'nombre' => $user->nombre_completo,
                    'email' => $user->email,
                    'rol' => $user->rol,
                    'activo' => $user->activo
                ];
            })
        ]);
    }


    public function store(Request $request)
    {
        try {
            // Verificar primero si el correo ya existe
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json([
                    'message' => 'El correo electrónico ya está registrado',
                    'errors' => [
                        'email' => ['Este correo electrónico ya está en uso']
                    ]
                ], 422);
            }

            $validated = $request->validate([
                'nombre_completo' => 'required|string|max:50|regex:/^[\pL\s]+$/u',
                'email' => 'required|email|unique:users',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()]).+$/'
                ],
                'rol' => 'required|in:Administrador,Supervisor',
            ], [
                'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial',
                'nombre_completo.regex' => 'El nombre solo puede contener letras y espacios',
                'email.unique' => 'Este correo electrónico ya está registrado'
            ]);

            $validated['password_hash'] = Hash::make($validated['password']);
            $validated['activo'] = true;
            unset($validated['password']);

            $user = User::create($validated);

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'data' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user)
    {
        return response()->json([
            'data' => [
                'id' => $user->id,
                'nombre' => $user->nombre_completo,
                'email' => $user->email,
                'rol' => $user->rol,
                'activo' => $user->activo,
                'creado' => $user->created_at
            ]
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8',
            'rol' => 'sometimes|in:Administrador,Supervisor',
            'activo' => 'sometimes|boolean'
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario actualizado',
            'data' => $user
        ]);
    }



    public function updateSelf(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nombre_completo' => 'sometimes|string|max:50|regex:/^[\pL\s]+$/u',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()]).+$/'
            ],
        ], [
            'nombre_completo.regex' => 'El nombre solo puede contener letras y espacios',
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, un número y un carácter especial',
        ]);

        if (isset($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $user->update($validated);
        return response()->json([
            'message' => 'Tus datos han sido actualizados correctamente',
            'data' => [
                'id' => $user->id,
                'nombre' => $user->nombre_completo,
                'email' => $user->email,
            ]
        ], 200); // <- asegúrate que regresas un 200 y no un 204

    }



    /**
     * Muestra los datos del usuario autenticado.
     */
    public function me(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'nombre' => $user->nombre_completo,
                'email' => $user->email,
                'rol' => $user->rol,
                'activo' => $user->activo,
            ]
        ]);
    }



    public function destroy(User $user)
    {
        if ($user->rol === 'Administrador' && User::where('rol', 'Administrador')->count() <= 1) {
            return response()->json([
                'message' => 'No se puede eliminar el último administrador'
            ], 422);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
