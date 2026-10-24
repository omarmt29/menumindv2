<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MenuMindSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Super admin',
            'email' => 'super@menumind.test',
            'password' => 'MenuMind2026!',
            'role' => UserRole::SuperAdmin,
            'email_verified_at' => now(),
        ]);

        $this->seedRestaurant(
            [
                'name' => 'La Brasa de Gazcue',
                'cuisine' => 'Cocina criolla',
                'description' => 'Fuego lento, sazón de casa y una sala corta en Gazcue. La carta cambia con el mercado, el QR no.',
                'phone' => '809-555-0142',
                'email' => 'hola@labrasa.test',
                'address' => 'Calle Santiago 48',
                'city' => 'Santo Domingo',
            ],
            ['Laura Méndez', 'laura@labrasa.test'],
            [
                ['Yuca al mojo', 'Yuca dorada, mojo de ajo y cilantro.', 320, 'Entradas', true],
                ['Ensalada de aguacate', 'Aguacate, cebolla morada y vinagre de chinola.', 380, 'Entradas', true],
                ['Pollo guisado', 'Pollo de corral, aceitunas y arroz blanco.', 640, 'Platos', true],
                ['Chivo liniero', 'Chivo al carbón, salsa agria y tostones.', 890, 'Platos', true],
                ['Mofongo de chicharrón', 'Plátano verde majado con chicharrón crocante.', 720, 'Platos', false],
                ['Flan de coco', 'Flan suave, leche de coco y azúcar quemada.', 280, 'Postres', true],
                ['Morir soñando', 'Jugo de naranja y leche evaporada, bien frío.', 180, 'Bebidas', true],
            ],
            [
                ['Almuerzo', 'La carta corta del mediodía.', ['Yuca al mojo', 'Ensalada de aguacate', 'Pollo guisado', 'Chivo liniero', 'Flan de coco', 'Morir soñando']],
                ['Noche', 'Brasas, un postre y algo para brindar.', ['Ensalada de aguacate', 'Chivo liniero', 'Mofongo de chicharrón', 'Flan de coco', 'Morir soñando']],
            ],
        );

        $this->seedRestaurant(
            [
                'name' => 'Marea Norte',
                'cuisine' => 'Mariscos',
                'description' => 'Pescado del día y una barra corta frente al malecón. Todo sale a la plancha o al ajillo.',
                'phone' => '809-555-0198',
                'email' => 'hola@marea.test',
                'address' => 'Av. Malecón 12',
                'city' => 'Puerto Plata',
            ],
            ['Andrés Vidal', 'andres@marea.test'],
            [
                ['Ceviche de chillo', 'Chillo, limón agrio, ají y cilantro.', 540, 'Entradas', true],
                ['Camarones al ajillo', 'Camarones salteados, ajo y pan tostado.', 780, 'Del mar', true],
                ['Pescado a la plancha', 'Filete del día, mantequilla de hierbas y limón.', 860, 'Del mar', true],
                ['Arroz con mariscos', 'Arroz caldoso, calamar, camarón y azafrán.', 920, 'Del mar', true],
                ['Tostones', 'Plátano verde frito, dos veces.', 220, 'Acompañantes', true],
                ['Flan de la casa', 'Flan clásico, vainilla y caramelo.', 260, 'Postres', true],
                ['Sangría de chinola', 'Chinola, vino blanco y naranja.', 340, 'Bebidas', true],
            ],
            [
                ['Carta del mar', 'Lo que llegó esta mañana.', ['Ceviche de chillo', 'Camarones al ajillo', 'Pescado a la plancha', 'Arroz con mariscos', 'Tostones', 'Flan de la casa', 'Sangría de chinola']],
                ['Almuerzo ligero', 'Una entrada, un plato y algo frío.', ['Ceviche de chillo', 'Pescado a la plancha', 'Tostones', 'Flan de la casa']],
            ],
        );

        $this->seedRestaurant(
            [
                'name' => 'Taller del Pan',
                'cuisine' => 'Panadería y café',
                'description' => 'Masa madre, mantequilla y café de altura. Abre temprano y cierra cuando se acaba la vitrina.',
                'phone' => '809-555-0114',
                'email' => 'hola@taller.test',
                'address' => 'Calle del Sol 203',
                'city' => 'Santiago',
            ],
            ['Sofía Herrera', 'sofia@taller.test'],
            [
                ['Croissant de mantequilla', 'Hojaldre laminado durante la noche.', 180, 'Para llevar', true],
                ['Canelé', 'Costra de caramelo y centro tierno de vainilla.', 160, 'Postres', true],
                ['Pan de masa madre', 'Hogaza del día, corteza gruesa.', 240, 'Para llevar', true],
                ['Huevos en sartén', 'Dos huevos, pan tostado y mantequilla.', 420, 'Platos', true],
                ['Tostada de ricotta', 'Pan del día, ricotta, miel y pistacho.', 390, 'Platos', true],
                ['Jugo de naranja', 'Naranjas exprimidas al momento.', 160, 'Bebidas', true],
                ['Café filtrado', 'Extracción del día, taza o para llevar.', 140, 'Bebidas', true],
            ],
            [
                ['Desayuno', 'Hasta que se acabe el pan.', ['Huevos en sartén', 'Tostada de ricotta', 'Croissant de mantequilla', 'Jugo de naranja', 'Café filtrado']],
                ['Vitrina', 'Para llevar, sin mesa.', ['Croissant de mantequilla', 'Canelé', 'Pan de masa madre', 'Café filtrado']],
            ],
        );
    }

    private function seedRestaurant(array $profile, array $admin, array $products, array $menus): void
    {
        $restaurant = Restaurant::query()->create([
            ...$profile,
            'slug' => Restaurant::uniqueSlug($profile['name']),
            'is_active' => true,
        ]);

        $restaurant->users()->create([
            'name' => $admin[0],
            'email' => $admin[1],
            'password' => 'MenuMind2026!',
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $ids = [];

        foreach ($products as [$name, $detail, $price, $category, $available]) {
            $product = $restaurant->products()->create([
                'name' => $name,
                'detail' => $detail,
                'price' => $price,
                'category' => $category,
                'is_available' => $available,
            ]);
            $ids[$name] = [$product->id, $category];
        }

        foreach ($menus as [$name, $description, $names]) {
            $menu = $restaurant->menus()->create([
                'name' => $name,
                'description' => $description,
                'is_published' => true,
            ]);

            $sync = [];

            foreach (array_values($names) as $index => $productName) {
                [$id, $category] = $ids[$productName];
                $sync[$id] = ['section' => $category, 'sort_order' => $index];
            }

            $menu->products()->sync($sync);
        }
    }
}
