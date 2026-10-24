<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Restaurant::class);

        $term = $this->term($request);

        $restaurants = Restaurant::query()
            ->withCount(['products', 'menus'])
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'ilike', "%{$term}%")
                        ->orWhere('city', 'ilike', "%{$term}%")
                        ->orWhere('cuisine', 'ilike', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('restaurants.index', compact('restaurants'));
    }

    public function create(): View
    {
        $this->authorize('create', Restaurant::class);

        return view('restaurants.form', [
            'restaurant' => new Restaurant(['is_active' => true]),
            'admin' => new User,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Restaurant::class);

        $data = $this->validateRestaurant($request);
        $active = $request->boolean('is_active');

        $restaurant = DB::transaction(function () use ($data, $active) {
            $restaurant = Restaurant::query()->create([
                'name' => $data['name'],
                'slug' => Restaurant::uniqueSlug($data['name']),
                'cuisine' => $data['cuisine'] ?? null,
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'is_active' => $active,
            ]);

            $restaurant->users()->create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => $data['admin_password'],
                'role' => UserRole::Admin,
            ]);

            return $restaurant;
        });

        return redirect()
            ->route('restaurants.show', $restaurant)
            ->with('status', 'Restaurante creado. Ya puede entrar con su correo.');
    }

    public function show(Restaurant $restaurant): View
    {
        $this->authorize('view', $restaurant);

        $restaurant->loadCount(['products', 'menus']);
        $restaurant->load([
            'admin',
            'menus' => fn ($query) => $query->withCount('products')->latest(),
            'products' => fn ($query) => $query->latest()->limit(8),
        ]);

        return view('restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant): View
    {
        $this->authorize('update', $restaurant);

        return view('restaurants.form', [
            'restaurant' => $restaurant,
            'admin' => $restaurant->admin ?: new User,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('update', $restaurant);

        $admin = $restaurant->admin;
        $data = $this->validateRestaurant($request, $admin);
        $active = $request->boolean('is_active');

        DB::transaction(function () use ($restaurant, $admin, $data, $active) {
            $restaurant->update([
                'name' => $data['name'],
                'slug' => Restaurant::uniqueSlug($data['name'], $restaurant->id),
                'cuisine' => $data['cuisine'] ?? null,
                'description' => $data['description'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'is_active' => $active,
            ]);

            $account = [
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'role' => UserRole::Admin,
            ];

            if (! empty($data['admin_password'])) {
                $account['password'] = $data['admin_password'];
            }

            if ($admin) {
                $admin->update($account);
            } else {
                $account['password'] = $data['admin_password'];
                $restaurant->users()->create($account);
            }
        });

        return redirect()
            ->route('restaurants.show', $restaurant)
            ->with('status', 'Restaurante actualizado.');
    }

    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $this->authorize('delete', $restaurant);

        $restaurant->delete();

        return redirect()
            ->route('restaurants.index')
            ->with('status', 'Restaurante eliminado.');
    }

    private function validateRestaurant(Request $request, ?User $admin = null): array
    {
        $creating = ! $request->route('restaurant');

        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'cuisine' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:80'],
            'is_active' => ['nullable', 'boolean'],
            'admin_name' => ['required', 'string', 'max:120'],
            'admin_email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($admin?->id)],
            'admin_password' => [$creating || ! $admin ? 'required' : 'nullable', 'string', 'min:8'],
        ]);
    }

    private function term(Request $request): string
    {
        return str_replace(['%', '_'], '', (string) $request->string('q')->trim());
    }
}
