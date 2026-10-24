<?php

namespace App\Models;

use App\Models\Concerns\BelongsToRestaurant;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['restaurant_id', 'name', 'description', 'is_published'])]
class Menu extends Model
{
    use BelongsToRestaurant;

    protected static function booted(): void
    {
        static::creating(function (Menu $menu): void {
            if (! $menu->qr_code) {
                $menu->qr_code = static::newQrCode();
            }
        });

        static::updating(function (Menu $menu): void {
            if ($menu->isDirty('qr_code')) {
                $menu->qr_code = $menu->getOriginal('qr_code');
            }
        });
    }

    public static function newQrCode(): string
    {
        do {
            $code = strtolower(Str::random(12));
        } while (static::query()->where('qr_code', $code)->exists());

        return $code;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'menu_product')
            ->withPivot(['section', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function publicUrl(): string
    {
        return route('menus.public', ['menu' => $this->qr_code]);
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
