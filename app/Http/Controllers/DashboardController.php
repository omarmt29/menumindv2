<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return view('dashboard', [
                'stats' => [
                    ['label' => 'Restaurantes', 'value' => Restaurant::query()->count()],
                    ['label' => 'Productos', 'value' => Product::query()->count()],
                    ['label' => 'Menús', 'value' => Menu::query()->count()],
                    ['label' => 'Publicados', 'value' => Menu::query()->where('is_published', true)->count()],
                ],
                'restaurants' => Restaurant::query()->withCount(['products', 'menus'])->latest()->take(6)->get(),
                'menus' => Menu::query()->with('restaurant')->withCount('products')->latest()->take(5)->get(),
            ]);
        }

        $restaurant = $user->restaurant;

        return view('dashboard', [
            'stats' => [
                ['label' => 'Productos', 'value' => $restaurant?->products()->count() ?? 0],
                ['label' => 'Menús', 'value' => $restaurant?->menus()->count() ?? 0],
                ['label' => 'Publicados', 'value' => $restaurant?->menus()->where('is_published', true)->count() ?? 0],
                ['label' => 'Agotados', 'value' => $restaurant?->products()->where('is_available', false)->count() ?? 0],
            ],
            'restaurants' => collect(),
            'menus' => $restaurant
                ? $restaurant->menus()->withCount('products')->latest()->take(6)->get()
                : collect(),
        ]);
    }
}
