<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\Product;
use App\Models\Restaurant;

class Panel
{
    public static function restaurant(): ?Restaurant
    {
        $bound = request()->route('restaurant');

        if ($bound instanceof Restaurant) {
            return $bound;
        }

        $user = auth()->user();

        return $user && $user->isAdmin() ? $user->restaurant : null;
    }

    public static function productsIndex(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.products.index', $restaurant ?? self::restaurant());
        }

        return route('products.index');
    }

    public static function productsCreate(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.products.create', $restaurant ?? self::restaurant());
        }

        return route('products.create');
    }

    public static function productsStore(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.products.store', $restaurant ?? self::restaurant());
        }

        return route('products.store');
    }

    public static function productsEdit(Product $product): string
    {
        if (self::super()) {
            return route('restaurants.products.edit', [$product->restaurant_id, $product]);
        }

        return route('products.edit', $product);
    }

    public static function productsUpdate(Product $product): string
    {
        if (self::super()) {
            return route('restaurants.products.update', [$product->restaurant_id, $product]);
        }

        return route('products.update', $product);
    }

    public static function productsDestroy(Product $product): string
    {
        if (self::super()) {
            return route('restaurants.products.destroy', [$product->restaurant_id, $product]);
        }

        return route('products.destroy', $product);
    }

    public static function menusIndex(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.menus.index', $restaurant ?? self::restaurant());
        }

        return route('menus.index');
    }

    public static function menusCreate(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.menus.create', $restaurant ?? self::restaurant());
        }

        return route('menus.create');
    }

    public static function menusStore(?Restaurant $restaurant = null): string
    {
        if (self::super()) {
            return route('restaurants.menus.store', $restaurant ?? self::restaurant());
        }

        return route('menus.store');
    }

    public static function menuShow(Menu $menu): string
    {
        if (self::super()) {
            return route('restaurants.menus.show', [$menu->restaurant_id, $menu]);
        }

        return route('menus.show', $menu);
    }

    public static function menusEdit(Menu $menu): string
    {
        if (self::super()) {
            return route('restaurants.menus.edit', [$menu->restaurant_id, $menu]);
        }

        return route('menus.edit', $menu);
    }

    public static function menusUpdate(Menu $menu): string
    {
        if (self::super()) {
            return route('restaurants.menus.update', [$menu->restaurant_id, $menu]);
        }

        return route('menus.update', $menu);
    }

    public static function menusDestroy(Menu $menu): string
    {
        if (self::super()) {
            return route('restaurants.menus.destroy', [$menu->restaurant_id, $menu]);
        }

        return route('menus.destroy', $menu);
    }

    public static function menusQr(Menu $menu): string
    {
        if (self::super()) {
            return route('restaurants.menus.qr', [$menu->restaurant_id, $menu]);
        }

        return route('menus.qr', $menu);
    }

    private static function super(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}
