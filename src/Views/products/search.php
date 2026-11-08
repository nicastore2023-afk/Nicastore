<?php
/** @var array $data */
/** @var string $app_name */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $title = $data['title']; ?>
<?php $query = $data['query']; ?>

<?php
function getFirstImage($product) {
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        if (!empty($images)) return $images[0];
    }
    return $product['image'] ?? null;
}

$filters = $data['filters'] ?? [];
$priceRange = $data['priceRange'] ?? ['min' => 0, 'max' => 0];
?>

<div class="container" style="padding: 3rem 0;">
    <nav class="breadcrumb" aria-label="Navegación">
        <a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <span aria-current="page"><?php echo htmlspecialchars($title); ?></span>
    </nav>
    
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;"><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($query): ?>
            <p style="margin-top: 0.75rem; color: var(--text-secondary);">
                <?php 
                    $count = $data['pagination']['total'] ?? 0;
                    echo $count === 1 ? 'Se encontró 1 resultado' : "Se encontraron {$count} resultados";
                ?>
            </p>
        <?php endif; ?>
    </header>
    
    <!-- Search Bar -->
    <form method="GET" action="<?php echo AppConfig::url('/buscar'); ?>" class="search-form" style="max-width: 100%; margin-bottom: 2rem;" role="search">
        <i class="bi bi-search search-icon" aria-hidden="true"></i>
        <input type="search" name="q" class="search-input" placeholder="Buscar productos, marcas, categorías..." value="<?php echo htmlspecialchars($query); ?>" autocomplete="off">
        <button type="submit" class="search-btn" aria-label="Buscar"><i class="bi bi-search"></i></button>
    </form>
    
    <div class="products-layout" style="display: grid; grid-template-columns: 260px 1fr; gap: 2rem;">
        <!-- Sidebar Filters -->
        <aside class="filters-sidebar fade-in-up" style="height: fit-content; position: sticky; top: calc(var(--header-height) + var(--header-top-height) + 1.5rem);">
            <form method="GET" action="<?php echo AppConfig::url('/buscar'); ?>">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($query); ?>">
                <input type="hidden" name="page" value="1">
                
                <!-- Price Range Filter -->
                <div class="filter-section" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Precio</h3>
                    <div class="price-inputs" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                        <div>
                            <label for="min_price" class="form-label" style="font-size: 0.8125rem;">Mín</label>
                            <input type="number" id="min_price" name="min_price" class="form-control" placeholder="0" min="0" step="0.01" value="<?php echo $filters['min_price'] ?? ''; ?>" style="padding: 0.625rem 0.75rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label for="max_price" class="form-label" style="font-size: 0.8125rem;">Máx</label>
                            <input type="number" id="max_price" name="max_price" class="form-control" placeholder="<?php echo number_format($priceRange['max'], 0); ?>" min="0" step="0.01" value="<?php echo $filters['max_price'] ?? ''; ?>" style="padding: 0.625rem 0.75rem; font-size: 0.875rem;">
                        </div>
                    </div>
                    <div class="price-slider" style="margin-top: 1rem;">
                        <input type="range" id="price_range" min="0" max="<?php echo ceil($priceRange['max']); ?>" value="<?php echo $filters['max_price'] ?? $priceRange['max']; ?>" step="1" oninput="document.getElementById('max_price').value = this.value; this.form.submit()">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                            <span>0</span>
                            <span><?php echo number_format($priceRange['max'], 0); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Stock & Sale Filters -->
                <div class="filter-section" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Disponibilidad</h3>
                    <label class="filter-checkbox" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; cursor: pointer;">
                        <input type="checkbox" name="in_stock" value="1" <?php echo !empty($filters['in_stock']) ? 'checked' : ''; ?> onchange="this.form.submit()" style="accent-color: var(--primary); width: 1.125rem; height: 1.125rem;">
                        <span>Solo en stock</span>
                    </label>
                    <label class="filter-checkbox" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0; cursor: pointer;">
                        <input type="checkbox" name="on_sale" value="1" <?php echo !empty($filters['on_sale']) ? 'checked' : ''; ?> onchange="this.form.submit()" style="accent-color: var(--primary); width: 1.125rem; height: 1.125rem;">
                        <span>Solo ofertas</span>
                    </label>
                </div>
                
                <!-- Sort -->
                <div class="filter-section" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Ordenar por</h3>
                    <select name="sort" class="form-control select-wrapper" onchange="this.form.submit()" style="padding: 0.75rem 1rem; font-size: 0.875rem;">
                        <option value="">Más recientes</option>
                        <option value="popular" <?php echo ($filters['sort'] ?? '') === 'COALESCE(sale_price, price) DESC, is_featured DESC, id DESC' ? 'selected' : ''; ?>>Más populares</option>
                        <option value="price_asc" <?php echo ($filters['sort'] ?? '') === 'COALESCE(sale_price, price) ASC' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                        <option value="price_desc" <?php echo ($filters['sort'] ?? '') === 'COALESCE(sale_price, price) DESC' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                        <option value="name_asc" <?php echo ($filters['sort'] ?? '') === 'name ASC' ? 'selected' : ''; ?>>Nombre: A-Z</option>
                        <option value="name_desc" <?php echo ($filters['sort'] ?? '') === 'name DESC' ? 'selected' : ''; ?>>Nombre: Z-A</option>
                    </select>
                </div>
                
                <!-- Clear Filters -->
                <?php if (!empty($filters)): ?>
                    <a href="<?php echo AppConfig::url('/buscar?q=' . urlencode($query)); ?>" class="btn btn-outline btn-block" style="margin-top: 1.5rem; padding: 0.75rem;">
                        <i class="bi bi-x-circle"></i> Limpiar filtros
                    </a>
                <?php endif; ?>
            </form>
        </aside>
        
        <!-- Products -->
        <main class="products-main">
            <div class="products-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div class="products-count" style="color: var(--text-secondary); font-size: 0.9375rem;">
                    <?php 
                        $total = $data['pagination']['total'] ?? 0;
                        $from = $data['pagination']['from'] ?? 0;
                        $to = $data['pagination']['to'] ?? 0;
                        echo "Mostrando {$from}-{$to} de {$total} resultados";
                    ?>
                </div>
            </div>
            
            <?php if (empty($data['products'])): ?>
                <div class="empty-state fade-in-up">
                    <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                    <h3><?php echo $query ? 'No se encontraron resultados para "' . htmlspecialchars($query) . '"' : 'No se encontraron productos'; ?></h3>
                    <p><?php echo $query ? 'Intenta con otros términos de búsqueda' : 'No hay productos disponibles en este momento'; ?></p>
                    <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary" style="margin-top: 1.5rem;">Ver Todos los Productos</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($data['products'] as $index => $product): ?>
                        <?php 
                            $price = $product['sale_price'] ?? $product['price'];
                            $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                            $firstImage = getFirstImage($product);
                            $inStock = $product['stock'] > 0;
                            $lowStock = $product['stock'] > 0 && $product['stock'] <= 5;
                        ?>
                        <article class="product-card fade-in-up stagger-<?php echo min(($index % 6) + 1, 6); ?>">
                            <div class="product-image">
                                <?php if ($firstImage): ?>
                                    <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);">
                                        <i class="bi bi-box"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-badges">
                                    <?php if ($originalPrice): ?>
                                        <span class="product-badge sale"><i class="bi bi-tag"></i> Oferta</span>
                                    <?php endif; ?>
                                    <?php if ($product['is_featured']): ?>
                                        <span class="product-badge featured"><i class="bi bi-star-fill"></i> Destacado</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?php echo htmlspecialchars($product['short_description'] ?? 'Producto'); ?></span>
                                <h3 class="product-title">
                                    <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                <div class="product-price">
                                    <span class="current-price"><?php echo number_format($price, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                    <?php if ($originalPrice): ?>
                                        <span class="original-price"><?php echo number_format($originalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-stock <?php echo $inStock ? ($lowStock ? 'low-stock' : 'in-stock') : 'out-of-stock'; ?>">
                                    <i class="bi <?php echo $inStock ? ($lowStock ? 'bi-exclamation-triangle' : 'bi-check-circle') : 'bi-x-circle'; ?>"></i>
                                    <?php 
                                        if ($inStock) {
                                            echo $lowStock ? 'Quedan ' . $product['stock'] . ' unidades' : 'En stock';
                                        } else {
                                            echo 'Agotado';
                                        }
                                    ?>
                                </div>
                                <div class="product-actions">
                                    <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/agregar'); ?>" method="POST">
                                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary" <?php echo !$inStock ? 'disabled' : ''; ?>>
                                            <i class="bi bi-bag-plus"></i> Añadir
                                        </button>
                                    </form>
                                    <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>" class="btn btn-secondary" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if (isset($data['pagination']) && $data['pagination']['last_page'] > 1): ?>
                    <?php
                        $currentPage = $data['pagination']['current_page'];
                        $lastPage = $data['pagination']['last_page'];
                        $baseUrl = AppConfig::url('/buscar');
                        $queryParams = $_GET;
                        unset($queryParams['page']);
                        $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
                    ?>
                    <nav class="pagination" aria-label="Paginación de resultados" style="margin-top: 3rem;">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo $baseUrl . $queryString . '&page=' . ($currentPage - 1); ?>" aria-label="Página anterior"><i class="bi bi-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $baseUrl . $queryString . '&page=1">1</a>';
                                if ($start > 2) echo '<span class="disabled">...</span>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++):
                        ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="active" aria-current="page"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $baseUrl . $queryString . '&page=' . $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($end < $lastPage): ?>
                            <?php if ($end < $lastPage - 1) echo '<span class="disabled">...</span>'; ?>
                            <a href="<?php echo $baseUrl . $queryString . '&page=' . $lastPage; ?>"><?php echo $lastPage; ?></a>
                        <?php endif; ?>
                        
                        <?php if ($currentPage < $lastPage): ?>
                            <a href="<?php echo $baseUrl . $queryString . '&page=' . ($currentPage + 1); ?>" aria-label="Página siguiente"><i class="bi bi-chevron-right"></i></a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
// Auto-submit form when checkbox changes
document.querySelectorAll('.filters-sidebar input[type="checkbox"]').forEach(input => {
    input.addEventListener('change', function() {
        this.closest('form').submit();
    });
});

// Price slider sync
const priceRange = document.getElementById('price_range');
const maxPriceInput = document.getElementById('max_price');
if (priceRange && maxPriceInput) {
    priceRange.addEventListener('input', function() {
        maxPriceInput.value = this.value;
    });
}
</script>