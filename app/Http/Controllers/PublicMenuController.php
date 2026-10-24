<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Product;
use Illuminate\View\View;

class PublicMenuController extends Controller
{
    private const SECTION_ORDER = [
        'Entradas' => 1,
        'Platos' => 2,
        'Del mar' => 3,
        'Acompañantes' => 4,
        'Postres' => 5,
        'Bebidas' => 6,
        'Para llevar' => 7,
        'Carta' => 8,
    ];

    public function show(Menu $menu): View
    {
        $menu->load([
            'restaurant',
            'products' => fn ($query) => $query->orderByPivot('sort_order'),
        ]);

        $open = $menu->is_published && $menu->restaurant->is_active;

        $sections = collect();

        if ($open) {
            $sections = $menu->products
                ->groupBy(fn (Product $product) => $product->pivot->section ?: 'Carta')
                ->sortBy(fn ($items, string $name) => sprintf('%02d-%s', self::SECTION_ORDER[$name] ?? 40, $name))
                ->map(fn ($items) => $items->sortBy(fn (Product $product) => $product->pivot->sort_order)->values());
        }

        return view('public.menu', [
            'menu' => $menu,
            'restaurant' => $menu->restaurant,
            'open' => $open,
            'sections' => $sections,
        ]);
    }
}
