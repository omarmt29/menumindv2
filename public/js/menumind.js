(() => {
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.querySelector('.backdrop');
    const toggle = document.querySelector('[data-sidebar-toggle]');

    const setSidebar = (open) => {
        sidebar?.classList.toggle('is-open', open);
        backdrop?.classList.toggle('is-open', open);
    };

    toggle?.addEventListener('click', () => setSidebar(!sidebar.classList.contains('is-open')));
    backdrop?.addEventListener('click', () => setSidebar(false));

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const label = button.querySelector('[data-copy-label]');
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                if (label) label.textContent = 'Copiado';
                button.classList.add('is-copied');
                window.setTimeout(() => {
                    button.classList.remove('is-copied');
                    if (label) label.textContent = 'Copiar enlace';
                }, 1600);
            } catch {
                window.prompt('Copia el enlace', button.dataset.copy);
            }
        });
    });

    document.querySelectorAll('.alert-success').forEach((alert) => {
        window.setTimeout(() => {
            alert.classList.add('is-leaving');
            alert.addEventListener('transitionend', () => alert.remove(), { once: true });
        }, 4200);
    });

    const root = document.querySelector('[data-menu-builder]');
    const catalogNode = document.getElementById('product-catalog');

    if (!root || !catalogNode) {
        return;
    }

    const catalog = JSON.parse(catalogNode.textContent || '[]');
    const list = root.querySelector('[data-items]');
    const picker = root.querySelector('[data-picker]');
    const empty = root.querySelector('[data-empty]');
    const template = document.getElementById('builder-template');
    const restaurantSelect = document.querySelector('[data-restaurant-select]');

    const restaurantId = () => {
        if (restaurantSelect) {
            return Number(restaurantSelect.value || 0);
        }

        return Number(root.dataset.restaurant || 0);
    };

    const fillPicker = () => {
        const used = new Set([...list.querySelectorAll('[data-id]')].map((input) => Number(input.value)));
        const current = restaurantId();
        picker.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = restaurantSelect && !current ? 'Primero elige el restaurante' : 'Agregar un producto';
        picker.append(placeholder);

        if (restaurantSelect && !current) {
            return;
        }

        catalog
            .filter((item) => !current || Number(item.restaurant_id) === current)
            .filter((item) => !used.has(Number(item.id)))
            .forEach((item) => {
                const option = document.createElement('option');
                option.value = String(item.id);
                option.textContent = `${item.name} · ${item.price_label}`;
                picker.append(option);
            });
    };

    const reindex = () => {
        [...list.children].forEach((item, index) => {
            const id = item.querySelector('[data-id]');
            const section = item.querySelector('[data-section]');
            if (id) id.name = `items[${index}][product_id]`;
            if (section) section.name = `items[${index}][section]`;
        });
        if (empty) empty.hidden = list.children.length > 0;
        fillPicker();
    };

    root.querySelector('[data-add]')?.addEventListener('click', () => {
        const product = catalog.find((item) => Number(item.id) === Number(picker.value));
        if (!product || !template) return;
        const node = template.content.firstElementChild.cloneNode(true);
        node.querySelector('[data-label]').textContent = product.name;
        node.querySelector('[data-price]').textContent = product.price_label;
        node.querySelector('[data-section]').value = product.category || 'Carta';
        node.querySelector('[data-id]').value = String(product.id);
        list.append(node);
        reindex();
    });

    list.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        const item = button.closest('.builder-item');
        if (!item) return;
        if (button.hasAttribute('data-remove')) item.remove();
        if (button.hasAttribute('data-up') && item.previousElementSibling) {
            list.insertBefore(item, item.previousElementSibling);
        }
        if (button.hasAttribute('data-down') && item.nextElementSibling) {
            list.insertBefore(item.nextElementSibling, item);
        }
        reindex();
    });

    restaurantSelect?.addEventListener('change', () => {
        list.replaceChildren();
        reindex();
    });

    reindex();
})();
