<?php

namespace App\Models;

use App\Models\Concerns\BelongsToRestaurant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['restaurant_id', 'name', 'detail', 'price', 'category', 'is_available'])]
class Product extends Model
{
    use BelongsToRestaurant;

    public const CATEGORIES = [
        'Entradas',
        'Platos',
        'Del mar',
        'Acompañantes',
        'Postres',
        'Bebidas',
        'Para llevar',
    ];

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_product')
            ->withPivot(['section', 'sort_order'])
            ->withTimestamps();
    }

    public function priceLabel(): string
    {
        $amount = (float) $this->price;
        $formatted = fmod($amount, 1.0) === 0.0
            ? number_format($amount, 0, '.', ',')
            : number_format($amount, 2, '.', ',');

        return 'RD$'.$formatted;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }
}
