<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $product = $data['product'];
    $price = $product['sale_price'] ?? $product['price'];
    $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
    
    // Get images
    $productImages = !empty($product['images']) ? json_decode($product['images'], true) : [];
    $firstImage = !empty($productImages) ? $productImages[0] : ($product['image'] ?? null);
    $inStock = $product['stock'] > 0;
    $lowStock = $product['stock'] > 0 && $product['stock'] <= 5;
    
    // Parse specifications from description or create from short_description
    $specs = [];
    if (!empty($product['description'])) {
        // Try to extract specs from description (lines with :)
        $lines = explode("\n", $product['description']);
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false && strlen($line) < 200) {
                [$key, $value] = explode(':', $line, 2);
                $specs[trim($key)] = trim($value);
            }
        }
    }
    
    // Default specs if none found
    if (empty($specs)) {
        $specs = [
            'Marca' => 'Genérica',
            'Garantía' => '1 año',
            'Condición' => 'Nuevo',
        ];
    }
?>

<div class="container" style="padding: 3rem 0;">
    <nav class="breadcrumb" aria-label="Navegación">
        <a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <a href="<?php echo AppConfig::url('/productos'); ?>"><i class="bi bi-grid"></i> Productos</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <span aria-current="page"><?php echo htmlspecialchars($product['name']); ?></span>
    </nav>
    
    <div class="product-detail" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
        <!-- Gallery -->
        <div class="product-gallery">
            <div class="main-image" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; aspect-ratio: 1;">
                <?php if ($firstImage): ?>
                    <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="main-product-image" style="width: 100%; height: 100%; object-fit: contain; padding: 2rem; cursor: zoom-in;">
                <?php else: ?>
                    <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem; color: rgba(255,255,255,0.3);">
                        <i class="bi bi-cpu"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($productImages) > 1): ?>
                <div class="product-thumbs" style="display: flex; gap: 0.75rem; margin-top: 1rem; overflow-x: auto; padding-bottom: 0.5rem;" role="group" aria-label="Imágenes del producto">
                    <?php foreach ($productImages as $index => $image): ?>
                        <button class="thumb" data-image="<?php echo AppConfig::asset('images/products/' . basename($image)); ?>" aria-label="Ver imagen <?php echo $index + 1; ?>" aria-pressed="<?php echo $index === 0 ? 'true' : 'false'; ?>" style="flex: 0 0 80px; height: 80px; border: 2px solid <?php echo $index === 0 ? 'var(--primary)' : 'var(--border)'; ?>; border-radius: var(--radius); background: var(--bg-white); overflow: hidden; cursor: pointer; transition: var(--transition-fast);">
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($image)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> - Vista <?php echo $index + 1; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Info -->
        <div class="product-info-detail">
            <span class="product-category" style="display: inline-block; font-size: 0.75rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;"><?php echo htmlspecialchars($product['short_description'] ?? 'Producto'); ?></span>
            <h1 style="font-size: clamp(1.75rem, 3vw, 2.25rem); font-weight: 700; margin-bottom: 1rem; color: var(--text-primary);"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div class="product-meta" style="display: flex; gap: 1.5rem; margin-bottom: 1rem; color: var(--text-secondary); font-size: 0.875rem; flex-wrap: wrap;">
                <span><i class="bi bi-barcode"></i> SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></span>
                <span><i class="bi bi-tag"></i> Categoría: <?php echo htmlspecialchars($product['category_name'] ?? 'Sin categoría'); ?></span>
            </div>
            
            <div class="product-price-detail" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <span class="current" style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($price, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                <?php if ($originalPrice): ?>
                    <span class="original" style="font-size: 1.25rem; color: var(--text-muted); text-decoration: line-through;"><?php echo number_format($originalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                    <span class="discount-badge" style="background: linear-gradient(135deg, var(--error), var(--secondary)); color: white; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600;">
                        -<?php echo round(100 - ($price / $originalPrice * 100)); ?>%
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="stock-info <?php echo $inStock ? ($lowStock ? 'stock-low' : 'stock-in') : 'stock-out'; ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-weight: 500; <?php echo $inStock ? ($lowStock ? 'background: #FFFBEB; color: var(--warning);' : 'background: #ECFDF5; color: var(--success);') : 'background: #FEF2F2; color: var(--error);'; ?>">
                <i class="bi <?php echo $inStock ? ($lowStock ? 'bi-exclamation-triangle' : 'bi-check-circle') : 'bi-x-circle'; ?>"></i>
                <?php 
                    if ($inStock) {
                        echo $lowStock ? 'Solo quedan ' . $product['stock'] . ' unidades disponibles' : 'En stock - Envío inmediato';
                    } else {
                        echo 'Agotado - Notificarme cuando esté disponible';
                    }
                ?>
            </div>
            
            <div class="product-description" style="margin-bottom: 1.5rem; line-height: 1.8; color: var(--text-secondary);">
                <?php echo nl2br(htmlspecialchars($product['description'] ?? $product['short_description'] ?? 'Sin descripción disponible')); ?>
            </div>
            
            <?php if ($inStock): ?>
                <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/agregar'); ?>" method="POST" style="margin-bottom: 1.5rem;">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <div class="quantity-selector" style="display: flex; align-items: center; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden;">
                            <button type="button" class="qty-btn" data-action="decrease" aria-label="Disminuir cantidad" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: var(--bg-white); border: none; color: var(--text-secondary); font-size: 1.25rem;">-</button>
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo $product['stock']; ?>" style="width: 70px; text-align: center; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); padding: 0.75rem; font-size: 1rem; font-weight: 600;">
                            <button type="button" class="qty-btn" data-action="increase" aria-label="Aumentar cantidad" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: var(--bg-white); border: none; color: var(--text-secondary); font-size: 1.25rem;">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg" style="flex: 1; min-width: 200px;">
                            <i class="bi bi-bag-plus"></i> Añadir al Carrito
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <button class="btn btn-primary btn-lg" disabled style="width: 100%; margin-bottom: 1.5rem;">
                    <i class="bi bi-x-circle"></i> Producto Agotado
                </button>
            <?php endif; ?>
            
            <div class="product-share" style="display: flex; align-items: center; gap: 1rem; padding-top: 1.5rem; border-top: 1px solid var(--border); flex-wrap: wrap;">
                <span style="font-weight: 500; color: var(--text-secondary);">Compartir:</span>
                <a href="https://wa.me/?text=<?php echo urlencode($product['name'] . ' - ' . AppConfig::url('/producto/' . $product['slug'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en WhatsApp" class="share-btn" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #25D366; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(AppConfig::url('/producto/' . $product['slug'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en Facebook" class="share-btn" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #1877F2; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($product['name']); ?>&url=<?php echo urlencode(AppConfig::url('/producto/' . $product['slug'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en Twitter" class="share-btn" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #000000; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                <a href="mailto:?subject=<?php echo urlencode($product['name']); ?>&body=<?php echo urlencode(AppConfig::url('/producto/' . $product['slug'])); ?>" aria-label="Compartir por Email" class="share-btn" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: var(--primary); color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                
                <!-- Compare Button -->
                <button type="button" class="btn btn-outline compare-btn" data-id="<?php echo $product['id']; ?>" style="margin-left: auto; padding: 0.625rem 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="bi bi-diagram-3"></i> <span>Comparar</span>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Specifications Tabs -->
    <div class="product-tabs" style="margin-top: 3rem; background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
        <div class="tabs-nav" style="display: flex; border-bottom: 1px solid var(--border); background: var(--bg-light);">
            <button class="tab-btn active" data-tab="description" style="flex: 1; padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 600; color: var(--primary); cursor: pointer; transition: var(--transition-fast);">Descripción</button>
            <button class="tab-btn" data-tab="specs" style="flex: 1; padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition-fast);">Especificaciones</button>
            <button class="tab-btn" data-tab="shipping" style="flex: 1; padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition-fast);">Envío y Garantía</button>
        </div>
        
        <div class="tabs-content" style="padding: 2rem;">
            <div class="tab-panel active" id="tab-description" style="line-height: 1.8; color: var(--text-secondary);">
                <?php echo nl2br(htmlspecialchars($product['description'] ?? $product['short_description'] ?? 'Sin descripción disponible')); ?>
            </div>
            
            <div class="tab-panel" id="tab-specs" style="display: none;">
                <table class="specs-table" style="width: 100%; border-collapse: collapse;">
                    <?php foreach ($specs as $key => $value): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--text-primary); width: 30%;"><?php echo htmlspecialchars($key); ?></th>
                            <td style="padding: 1rem; color: var(--text-secondary);"><?php echo htmlspecialchars($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <div class="tab-panel" id="tab-shipping" style="display: none;">
                <div class="shipping-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="shipping-item" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.5rem; background: var(--bg-light); border-radius: var(--radius);">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #DBEAFE; border-radius: var(--radius); color: var(--primary); font-size: 1.5rem;"><i class="bi bi-truck"></i></div>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Envío Gratis</h4>
                            <p style="color: var(--text-secondary); margin: 0;">En pedidos mayores a <?php echo $data['settings']['free_shipping_threshold'] ?? 50; ?> <?php echo $data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$'); ?></p>
                        </div>
                    </div>
                    <div class="shipping-item" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.5rem; background: var(--bg-light); border-radius: var(--radius);">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #D1FAE5; border-radius: var(--radius); color: var(--success); font-size: 1.5rem;"><i class="bi bi-shield-check"></i></div>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Garantía Oficial</h4>
                            <p style="color: var(--text-secondary); margin: 0;">1 año de garantía del fabricante en todos los productos</p>
                        </div>
                    </div>
                    <div class="shipping-item" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.5rem; background: var(--bg-light); border-radius: var(--radius);">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #FFEDD5; border-radius: var(--radius); color: var(--warning); font-size: 1.5rem;"><i class="bi bi-arrow-return-left"></i></div>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Devoluciones Fáciles</h4>
                            <p style="color: var(--text-secondary); margin: 0;">30 días para devoluciones sin preguntas</p>
                        </div>
                    </div>
                    <div class="shipping-item" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1.5rem; background: var(--bg-light); border-radius: var(--radius);">
                        <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: #E0E7FF; border-radius: var(--radius); color: var(--info); font-size: 1.5rem;"><i class="bi bi-headset"></i></div>
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Soporte Técnico</h4>
                            <p style="color: var(--text-secondary); margin: 0;">Asesoría experta antes y después de tu compra</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($data['relatedProducts'])): ?>
    <section class="related-products" aria-labelledby="related-title" style="margin-top: 4rem;">
        <header class="fade-in-up" style="margin-bottom: 2rem;">
            <h2 id="related-title" class="section-title">Productos Relacionados</h2>
            <p class="section-subtitle">Otros productos que podrían interesarte</p>
        </header>
        
        <div class="product-grid">
            <?php foreach ($data['relatedProducts'] as $index => $related): ?>
                <?php 
                    $rPrice = $related['sale_price'] ?? $related['price'];
                    $rOriginalPrice = $related['sale_price'] && $related['sale_price'] < $related['price'] ? $related['price'] : null;
                    $relatedImages = !empty($related['images']) ? json_decode($related['images'], true) : [];
                    $relatedFirstImage = !empty($relatedImages) ? $relatedImages[0] : ($related['image'] ?? null);
                    $rInStock = $related['stock'] > 0;
                ?>
                <article class="product-card fade-in-up stagger-<?php echo min(($index % 6) + 1, 6); ?>">
                    <div class="product-image">
                        <?php if ($relatedFirstImage): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($relatedFirstImage)); ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);">
                                <i class="bi bi-box"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($rOriginalPrice): ?>
                            <div class="product-badges">
                                <span class="product-badge sale"><i class="bi bi-tag"></i> Oferta</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="<?php echo AppConfig::url('/producto/' . $related['slug']); ?>">
                                <?php echo htmlspecialchars($related['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo number_format($rPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            <?php if ($rOriginalPrice): ?>
                                <span class="original-price"><?php echo number_format($rOriginalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="<?php echo AppConfig::url('/producto/' . $related['slug']); ?>" class="btn btn-primary" style="width: 100%;" <?php echo !$rInStock ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Recently Viewed -->
    <?php if (!empty($data['recentlyViewed'])): ?>
    <section class="recently-viewed" aria-labelledby="recent-title" style="margin-top: 4rem;">
        <header class="fade-in-up" style="margin-bottom: 2rem;">
            <h2 id="recent-title" class="section-title">Vistos Recientemente</h2>
        </header>
        
        <div class="product-grid">
            <?php foreach ($data['recentlyViewed'] as $index => $recent): ?>
                <?php 
                    $rPrice = $recent['sale_price'] ?? $recent['price'];
                    $rOriginalPrice = $recent['sale_price'] && $recent['sale_price'] < $recent['price'] ? $recent['price'] : null;
                    $recentImages = !empty($recent['images']) ? json_decode($recent['images'], true) : [];
                    $recentFirstImage = !empty($recentImages) ? $recentImages[0] : ($recent['image'] ?? null);
                    $rInStock = $recent['stock'] > 0;
                ?>
                <article class="product-card fade-in-up stagger-<?php echo min(($index % 6) + 1, 6); ?>">
                    <div class="product-image">
                        <?php if ($recentFirstImage): ?>
                            <img src="<?php echo AppConfig::asset('images/products/' . basename($recentFirstImage)); ?>" alt="<?php echo htmlspecialchars($recent['name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="placeholder-image" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);">
                                <i class="bi bi-box"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($rOriginalPrice): ?>
                            <div class="product-badges">
                                <span class="product-badge sale"><i class="bi bi-tag"></i> Oferta</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">
                            <a href="<?php echo AppConfig::url('/producto/' . $recent['slug']); ?>">
                                <?php echo htmlspecialchars($recent['name']); ?>
                            </a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price"><?php echo number_format($rPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            <?php if ($rOriginalPrice): ?>
                                <span class="original-price"><?php echo number_format($rOriginalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-actions">
                            <a href="<?php echo AppConfig::url('/producto/' . $recent['slug']); ?>" class="btn btn-primary" style="width: 100%;" <?php echo !$rInStock ? 'aria-disabled="true" tabindex="-1"' : ''; ?>>
                                <i class="bi bi-eye"></i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
// Image gallery
document.querySelectorAll('.thumb').forEach(thumb => {
    thumb.addEventListener('click', function() {
        const mainImage = document.getElementById('main-product-image');
        if (mainImage && this.dataset.image) {
            mainImage.src = this.dataset.image;
            document.querySelectorAll('.thumb').forEach(t => t.style.borderColor = 'var(--border)');
            this.style.borderColor = 'var(--primary)';
        }
    });
});

// Quantity selector
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = document.getElementById('quantity');
        if (!input) return;
        const action = this.dataset.action;
        const max = parseInt(input.max) || 999;
        const min = parseInt(input.min) || 1;
        let value = parseInt(input.value) || 1;
        
        if (action === 'increase' && value < max) {
            input.value = value + 1;
        } else if (action === 'decrease' && value > min) {
            input.value = value - 1;
        }
    });
});

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tab = this.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
            b.style.color = 'var(--text-secondary)';
            b.style.fontWeight = '500';
        });
        this.classList.add('active');
        this.style.color = 'var(--primary)';
        this.style.fontWeight = '600';
        
        document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
        const panel = document.getElementById('tab-' + tab);
        if (panel) panel.style.display = 'block';
    });
});

// Share buttons hover effect
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
this.style.transform = 'translateY(-2px) scale(1.1)';
    });
    btn.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Compare button
document.querySelectorAll('.compare-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const originalHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Añadiendo...</span>';
        
        fetch('<?php echo AppConfig::url('/comparar/añadir/'); ?>' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo htmlspecialchars($csrf_token); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.innerHTML = '<i class="bi bi-check"></i> <span>Añadido</span>';
                this.classList.add('btn-success');
                this.classList.remove('btn-outline');
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline');
                    this.disabled = false;
                }, 2000);
            } else {
                alert(data.message);
                this.innerHTML = originalHtml;
                this.disabled = false;
            }
        })
        .catch(() => {
            alert('Error al añadir a comparación');
            this.innerHTML = originalHtml;
            this.disabled = false;
        });
    });
});
</script>