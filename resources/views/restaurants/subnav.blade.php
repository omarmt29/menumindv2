<nav class="subnav" aria-label="Secciones del restaurante">
    <a class="{{ request()->routeIs('restaurants.show', 'restaurants.edit') ? 'is-active' : '' }}" href="{{ route('restaurants.show', $restaurant) }}">Ficha</a>
    <a class="{{ request()->routeIs('restaurants.products.*') ? 'is-active' : '' }}" href="{{ route('restaurants.products.index', $restaurant) }}">Productos</a>
    <a class="{{ request()->routeIs('restaurants.menus.*') ? 'is-active' : '' }}" href="{{ route('restaurants.menus.index', $restaurant) }}">Menús</a>
</nav>
