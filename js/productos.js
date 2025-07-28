// File: js/productos.js
// Products page functionality (filtros)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize product filters
    initializeFilters();

    // NOTA: initializeWishlist() y initializeQuickView() se han movido a main.js
    // para centralizar la lógica de UI común y usar delegación de eventos.
    // Si tienes alguna lógica de inicialización específica para estos aquí,
    // asegúrate de que no duplique o entre en conflicto con main.js.
});

function initializeFilters() {
    const colorFilter = document.getElementById('colorFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (colorFilter) colorFilter.addEventListener('change', filterProducts);
    if (priceFilter) priceFilter.addEventListener('change', filterProducts);
    if (sortFilter) sortFilter.addEventListener('change', filterProducts);

    // Añadir listener para el botón de limpiar filtros
    const clearFiltersBtn = document.querySelector('.btn-clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }
}

function filterProducts() {
    const colorFilter = document.getElementById('colorFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const sortFilter = document.getElementById('sortFilter').value;

    const productsGrid = document.getElementById('productsGrid');
    const products = Array.from(productsGrid.querySelectorAll('.product-card')); // Obtener todas las tarjetas de productos
    const noProductsMessage = document.getElementById('no-products-message'); // Mensaje si no hay productos

    // Add loading state
    productsGrid.classList.add('loading');

    setTimeout(() => { // Simulate a small delay for filtering/sorting
        let filteredProducts = products.filter(product => {
            const productColors = product.dataset.colors.split(',').map(c => c.trim());
            // Si el filtro de color está vacío o el producto tiene el color seleccionado
            const matchesColor = colorFilter === '' || productColors.includes(colorFilter);
            return matchesColor;
        });

        // Sorting logic
        filteredProducts.sort((a, b) => {
            const nameA = a.dataset.name.toLowerCase();
            const nameB = b.dataset.name.toLowerCase();
            const priceA = parseFloat(a.dataset.price);
            const priceB = parseFloat(b.dataset.price);

            if (sortFilter === 'name') {
                return nameA.localeCompare(nameB);
            } else if (sortFilter === 'price') {
                return priceFilter === 'asc' ? priceA - priceB : priceB - priceA;
            }
            return 0; // No sorting
        });

        // Clear current products and append filtered/sorted ones
        productsGrid.innerHTML = '';
        if (filteredProducts.length > 0) {
            filteredProducts.forEach((product, index) => {
                // Pequeño delay para animar individualmente si es necesario
                setTimeout(() => {
                    product.style.display = 'block'; // Asegura que estén visibles
                    productsGrid.appendChild(product); // Añade el producto al grid
                    product.classList.add('fade-in'); // Opcional: animación de aparición
                }, index * 50); // Ajusta el delay si es necesario
            });
            if (noProductsMessage) noProductsMessage.style.display = 'none';
        } else {
            if (noProductsMessage) noProductsMessage.style.display = 'block';
        }

        // Remove loading state
        productsGrid.classList.remove('loading');
    }, 300); // Small timeout to show loading state
}

function clearFilters() {
    const colorFilter = document.getElementById('colorFilter');
    const priceFilter = document.getElementById('priceFilter');
    const sortFilter = document.getElementById('sortFilter');

    if (colorFilter) colorFilter.value = '';
    if (priceFilter) priceFilter.value = '';
    if (sortFilter) sortFilter.value = 'name'; // O tu valor predeterminado

    // Vuelve a aplicar los filtros para mostrar todos los productos
    filterProducts();
}


// NOTA: La función createProductCardHtml ya no es usada directamente aquí
// porque el HTML de los productos se renderiza en playeras.php.
// Sin embargo, si en el futuro cargas productos dinámicamente con JS,
// esta función podría ser útil.
// function createProductCardHtml(product) { ... }