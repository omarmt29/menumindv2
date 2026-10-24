<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'user' => $request->user(),
            'restaurant' => $request->user()->restaurant,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $restaurant = $user->restaurant;

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($user->isAdmin() && $restaurant) {
            $rules = array_merge($rules, [
                'restaurant_name' => ['required', 'string', 'max:120'],
                'cuisine' => ['nullable', 'string', 'max:80'],
                'description' => ['nullable', 'string', 'max:1000'],
                'phone' => ['nullable', 'string', 'max:40'],
                'contact_email' => ['nullable', 'email', 'max:120'],
                'address' => ['nullable', 'string', 'max:180'],
                'city' => ['nullable', 'string', 'max:80'],
                'is_active' => ['nullable', 'boolean'],
            ]);
        }

        $data = $request->validate($rules);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        if ($user->isAdmin() && $restaurant) {
            $restaurant->update([
                'name' => $data['restaurant_name'],
                'slug' => \App\Models\Restaurant::uniqueSlug($data['restaurant_name'], $restaurant->id),
                'cuisine' => $data['cuisine'] ?? null,
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['contact_email'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);
        }

        return back()->with('status', 'Configuración guardada.');
    }
}
