<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restaurant;
use App\Support\Panel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $this->authorize('viewAny', Product::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $term = str_replace(['%', '_'], '', (string) $request->string('q')->trim());
        $context = $this->context($request);

        $products = Product::query()
            ->with('restaurant')
            ->forUser($request->user())
            ->when($context, fn ($query) => $query->where('restaurant_id', $context->id))
            ->when($term !== '', fn ($query) => $query->where('name', 'ilike', "%{$term}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'context' => $context,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $this->authorize('create', Product::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $context = $this->context($request);

        return view('products.form', [
            'product' => new Product([
                'is_available' => true,
                'category' => 'Platos',
                'restaurant_id' => $context?->id,
            ]),
            'context' => $context,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        if ($redirect = $this->guardSuperAdmin($request)) {
            return $redirect;
        }

        $data = $this->validateProduct($request);
        $data['restaurant_id'] = $this->context($request)->id;
        $data['is_available'] = $request->boolean('is_available');

        Product::query()->create($data);

        return redirect()
            ->to(Panel::productsIndex($this->context($request)))
            ->with('status', 'Producto creado.');
    }

    public function edit(Request $request, Restaurant $restaurant, Product $product): View|RedirectResponse
    {
        $this->authorize('update', $product);

        if ($redirect = $this->enterRestaurant($request, $product->restaurant, 'restaurants.products.edit', [$product])) {
            return $redirect;
        }

        return view('products.form', [
            'product' => $product,
            'context' => $product->restaurant,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $data = $this->validateProduct($request, $product);
        $data['is_available'] = $request->boolean('is_available');
        unset($data['restaurant_id']);

        $product->update($data);

        return redirect()
            ->to(Panel::productsIndex($product->restaurant))
            ->with('status', 'Producto actualizado.');
    }

    public function destroy(Request $request, Restaurant $restaurant, Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $restaurant = $product->restaurant;
        $product->delete();

        return redirect()
            ->to(Panel::productsIndex($restaurant))
            ->with('status', 'Producto eliminado.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:140'],
            'detail' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'category' => ['required', 'string', 'max:60'],
            'is_available' => ['nullable', 'boolean'],
        ];

        return $request->validate($rules);
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
