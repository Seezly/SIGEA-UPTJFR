<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\People;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'second_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'id_prefix' => 'required|string|max:1',
            'id_number' => 'required|string|max:9|unique:' . People::class,
            'address' => 'required|string|min:10',
            'birth_date' => 'required|date_format:d/m/Y|before:today',
            'gender' => 'required|string|max:1',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone_number' => 'required|string|lowercase|max:11|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $people = People::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'id_prefix' => $request->id_prefix,
            'id_number' => $request->id_number,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
        ]);

        if ($people->id) {
            $user = User::create([
                'people_id' => $people->id,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
