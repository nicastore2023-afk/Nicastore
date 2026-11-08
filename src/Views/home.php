<?php
/** @var array $data */
/** @var string $app_name */
/** @var string $csrf_token */
/** @var array $settings */
/** @var array $featuredProducts */
/** @var array $newProducts */
/** @var array $categories */
/** @var \Nicastore\Config\AppConfig $appConfig */
use Nicastore\Config\AppConfig;
?>

<?php $title = $data['title']; ?>

<?php
function getFirstImage(array $product): ?string {
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        if (!empty($images)) return $images[0];
    }
    return $product['image'] ?? null;
}
?>

<!-- Hero Section -->
<section class="hero" aria-labelledby="hero-title">
    <div class="container">
        <div class="fade-in">
            <div class="hero-badge">
                <i class="bi bi-shield-check"></i>
                Envío gratis en pedidos > <?php echo $settings['free_shipping_threshold'] ?? 50; ?> <?php echo $settings['currency'] === 'USD' ? '$' : ($settings['currency'] ?? '$'); ?>
            </div>
            <h1 id="hero-title">Tecnología de Vanguardia para <?php echo $settings['store_address'] ? 'Nicaragua' : 'tu país'; ?></h1>
            <p>Descubre laptops, smartphones, componentes y accesorios de las mejores marcas. Precios competitivos, garantía real y envío a todo el país.</p>
            <div class="hero-actions">
                <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-grid"></i> Ver Catálogo
                </a>
                <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-secondary btn-lg">
                    <i class="bi bi-tags"></i> Categorías
                </a>
            </div>
        </div>
        
        <div class="hero-stats stagger-1">
            <div class="stat-item">
                <div class="stat-value">500+</div>
                <div class="stat-label">Productos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">50+</div>
                <div class="stat-label">Marcas</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">15</div>
                <div class="stat-label">Departamentos</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">24/7</div>
                <div class="stat-label">Soporte</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section categories-section" aria-labelledby="categories-title">
    <div class="container">
        <header class="fade-in-up">
            <h2 id="categories-title" class="section-title">Categorías Populares</h2>
            <p class="section-subtitle">Encuentra lo que necesitas por categoría</p>
        </header>
        
        <div class="category-grid">
            <?php foreach ($data['categories'] as $index => $category): ?>
                <?php
                    $icons = [
                        'laptops' => 'bi-laptop',
                        'smartphones' => 'bi-phone',
                        'componentes' => 'bi-cpu',
                        'perifericos' => 'bi-mouse',
                        'audio' => 'bi-headphones',
                        'gaming' => 'bi-controller',
                        'almacenamiento' => 'bi-hdd',
                        'redes' => 'bi-wifi',
                        'accesorios' => 'bi-bag',
                        'monitores' => 'bi-display',
                    ];
                    $icon = $icons[$category['slug']] ?? 'bi-box';
                ?>
                <a href="<?php echo AppConfig::url('/categoria/' . $category['slug']); ?>" class="category-card stagger-<?php echo min($index + 2, 6); ?>">
                    <div class="category-icon">
                        <i class="bi <?php echo $icon; ?>"></i>
                    </div>
                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                    <div class="category-count"><?php echo $category['product_count']; ?> productos</div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section" style="background: var(--bg-light);" aria-labelledby="featured-title">
    <div class="container">
        <header class="fade-in-up">
            <h2 id="featured-title" class="section-title">Productos Destacados</h2>
            <p class="section-subtitle">Los más vendidos y mejor valorados por nuestros clientes</p>
        </header>
        
        <div class="product-grid">
            <?php foreach ($data['featuredProducts'] as $index => $product): ?>
                <?php 
                    $price = $product['sale_price'] ?? $product['price'];
                    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                    $firstImage = getFirstImage($product);
                    $inStock = $product['stock'] > 0;
                    $lowStock = $product['stock'] > 0 && $product['stock'] <= 5;
                ?>
                <article class="product-card fade-in-up stagger-<?php echo min($index + 1, 6); ?>">
                    <div class="product-image">
                        <?php if ($firstImage): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);">
                                <i class="bi bi-cpu"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-badges">
                            <?php if ($product['is_featured']): ?>
                                <span class="product-badge featured"><i class="bi bi-star-fill"></i> Destacado</span>
                            <?php endif; ?>
                            <?php if ($originalPrice): ?>
                                <span class="product-badge sale"><i class="bi bi-tag"></i> Oferta</span>
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
                            <span class="current-price"><?php echo number_format($price, 2, ',', '.'); ?> $</span>
                            <?php if ($originalPrice): ?>
                                <span class="original-price"><?php echo number_format($originalPrice, 2, ',', '.'); ?> $</span>
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
    </div>
</section>

<!-- New Products -->
<section class="section" aria-labelledby="new-title">
    <div class="container">
        <header class="fade-in-up">
            <h2 id="new-title" class="section-title">Novedades</h2>
            <p class="section-subtitle">Lo último en tecnología, recién llegado a nuestro almacén</p>
        </header>
        
        <div class="product-grid">
            <?php foreach ($data['newProducts'] as $index => $product): ?>
                <?php 
                    $price = $product['sale_price'] ?? $product['price'];
                    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                    $firstImage = getFirstImage($product);
                    $inStock = $product['stock'] > 0;
                    $lowStock = $product['stock'] > 0 && $product['stock'] <= 5;
                ?>
                <article class="product-card fade-in-up stagger-<?php echo min($index + 1, 6); ?>">
                    <div class="product-image">
                        <?php if ($firstImage): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);">
                                <i class="bi bi-box-seam"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-badges">
                            <span class="product-badge new"><i class="bi bi-sparkles"></i> Nuevo</span>
                            <?php if ($originalPrice): ?>
                                <span class="product-badge sale"><i class="bi bi-tag"></i> Oferta</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category">Nuevo</span>
                        <h3 class="product-title">
                            <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo number_format($price, 2, ',', '.'); ?> $</span>
                            <?php if ($originalPrice): ?>
                                <span class="original-price"><?php echo number_format($originalPrice, 2, ',', '.'); ?> $</span>
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
    </div>
</section>

<!-- CTA Section -->
<section class="section" style="background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: var(--text-light);" aria-labelledby="cta-title">
    <div class="container" style="text-align: center;">
        <h2 id="cta-title" class="section-title" style="color: var(--text-light);">¿No encuentras lo que buscas?</h2>
        <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Contamos con un catálogo extendido bajo pedido. Contáctanos y te ayudamos.</p>
        <div style="margin-top: 2rem;">
            <a href="#" class="btn btn-secondary btn-lg" style="background: transparent; border-color: var(--text-light); color: var(--text-light);">
                <i class="bi bi-whatsapp"></i> Consultar por WhatsApp
            </a>
        </div>
    </div>
</section>