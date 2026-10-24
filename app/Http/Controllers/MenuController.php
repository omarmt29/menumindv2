<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Product;
use App\Models\Restaurant;
use App\Support\MenuQr;
use App\Support\Panel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Menu::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $term = str_replace(['%', '_'], '', (string) $request->string('q')->trim());
        $context = $this->context($request);

        $menus = Menu::query()
            ->with('restaurant')
            ->withCount('products')
            ->forUser($request->user())
            ->when($context, fn ($query) => $query->where('restaurant_id', $context->id))
            ->when($term !== '', fn ($query) => $query->where('name', 'ilike', "%{$term}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('menus.index', [
            'menus' => $menus,
            'context' => $context,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', Menu::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $menu = new Menu([
            'is_published' => true,
            'restaurant_id' => $this->context($request)?->id,
        ]);

        return view('menus.form', $this->formData($request, $menu));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Menu::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $data = $this->validateMenu($request);
        $restaurantId = $this->context($request)->id;

        $this->assertProductsBelong($restaurantId, $data['items'] ?? []);

        $menu = Menu::query()->create([
            'restaurant_id' => $restaurantId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        $this->syncItems($menu, $data['items'] ?? []);

        return redirect()
            ->to(Panel::menuShow($menu))
            ->with('status', 'Menú creado. Este QR no va a cambiar.');
    }

    public function show(Request $request, Restaurant $restaurant, Menu $menu): View|RedirectResponse
    {
        $this->authorize('view', $menu);

        if ($redirect = $this->enterRestaurant($request, $menu->restaurant, 'restaurants.menus.show', [$menu])) {
            return $redirect;
        }

        $menu->load(['restaurant', 'products']);

        return view('menus.show', [
            'menu' => $menu,
            'qr' => MenuQr::svg($menu->publicUrl()),
        ]);
    }

    public function edit(Request $request, Restaurant $restaurant, Menu $menu): View|RedirectResponse
    {
        $this->authorize('update', $menu);

        if ($redirect = $this->enterRestaurant($request, $menu->restaurant, 'restaurants.menus.edit', [$menu])) {
            return $redirect;
        }

        $menu->load('products');

        return view('menus.form', $this->formData($request, $menu));
    }

    public function update(Request $request, Restaurant $restaurant, Menu $menu): RedirectResponse
    {
        $this->authorize('update', $menu);

        $data = $this->validateMenu($request, $menu);
        $this->assertProductsBelong($menu->restaurant_id, $data['items'] ?? []);

        $menu->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        $this->syncItems($menu, $data['items'] ?? []);

        return redirect()
            ->to(Panel::menuShow($menu))
            ->with('status', 'Menú actualizado. El QR sigue siendo el mismo.');
    }

    public function destroy(Restaurant $restaurant, Menu $menu): RedirectResponse
    {
        $this->authorize('delete', $menu);

        $restaurant = $menu->restaurant;
        $menu->delete();

        return redirect()->to(Panel::menusIndex($restaurant))->with('status', 'Menú eliminado.');
    }

    public function qr(Request $request, Restaurant $restaurant, Menu $menu): Response|RedirectResponse
    {
        $this->authorize('view', $menu);

        if ($redirect = $this->enterRestaurant($request, $menu->restaurant, 'restaurants.menus.qr', [$menu])) {
            return $redirect;
        }

        $svg = MenuQr::svg($menu->publicUrl());

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="menu-'.$menu->qr_code.'.svg"',
        ]);
    }

    private function formData(Request $request, Menu $menu): array
    {
        $products = Product::query()
            ->forUser($request->user())
            ->when($menu->restaurant_id, fn ($query) => $query->where('restaurant_id', $menu->restaurant_id))
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return [
            'menu' => $menu,
            'products' => $products,
            'context' => $menu->restaurant ?? $this->context($request),
        ];
    }

    private function validateMenu(Request $request, ?Menu $menu = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
            'items' => ['nullable', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.section' => ['nullable', 'string', 'max:60'],
        ];

        return $request->validate($rules, [
            'items.*.product_id.exists' => 'Hay un producto que ya no está disponible.',
        ]);
    }

    private function assertProductsBelong(int $restaurantId, array $items): void
    {
        $ids = collect($items)->pluck('product_id')->filter()->map(fn ($id) => (int) $id)->unique()->values();

        if ($ids->isEmpty()) {
            return;
        }

        $count = Product::query()
            ->where('restaurant_id', $restaurantId)
            ->whereIn('id', $ids)
            ->count();

        if ($count !== $ids->count()) {
            throw ValidationException::withMessages([
                'items' => 'Hay productos que no pertenecen a este restaurante.',
            ]);
        }
    }

    private function syncItems(Menu $menu, array $items): void
    {
        $sync = [];
        $order = 0;

        foreach ($items as $item) {
            $id = (int) ($item['product_id'] ?? 0);

            if ($id === 0 || isset($sync[$id])) {
                continue;
            }

            $section = trim((string) ($item['section'] ?? ''));

            $sync[$id] = [
                'section' => $section !== '' ? $section : 'Carta',
                'sort_order' => $order,
            ];

            $order++;
        }

        $menu->products()->sync($sync);
    }

    private function context(Request $request): ?Restaurant
    {
        $restaurant = $request->route('restaurant');

        if ($restaurant instanceof Restaurant) {
            return $restaurant;
        }

        return $request->user()->isAdmin() ? $request->user()->restaurant : null;
    }

    private function guardSuperAdmin(Request $request): ?RedirectResponse
    {
        if ($request->user()->isSuperAdmin() && ! $request->route('restaurant') instanceof Restaurant) {
            return redirect()->route('restaurants.index');
        }

        return null;
    }

    private function enterRestaurant(Request $request, Restaurant $restaurant, string $route, array $params = []): ?RedirectResponse
    {
        if ($request->user()->isSuperAdmin() && ! $request->route('restaurant') instanceof Restaurant) {
            return redirect()->route($route, [$restaurant, ...$params]);
        }

        return null;
    }
}
