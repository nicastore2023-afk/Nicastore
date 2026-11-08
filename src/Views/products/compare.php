<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
$products = $data['products'];
$compareIds = $data['compareIds'];

function getFirstImage($product) {
    if (!empty($product['images'])) {
        $images = json_decode($product['images'], true);
        if (!empty($images)) return $images[0];
    }
    return $product['image'] ?? null;
}
?>

<div class="container" style="padding: 3rem 0;">
    <nav class="breadcrumb" aria-label="Navegación">
        <a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <span aria-current="page">Comparar Productos</span>
    </nav>
    
    <header class="fade-in-up" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="section-title" style="text-align: left; margin: 0;">Comparar Productos</h1>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Selecciona hasta 3 productos para comparar sus características</p>
        </div>
        <?php if (!empty($products)): ?>
            <a href="<?php echo AppConfig::url('/comparar/limpiar'); ?>" class="btn btn-outline" id="clear-compare">
                <i class="bi bi-trash"></i> Limpiar comparación
            </a>
        <?php endif; ?>
    </header>
    
    <?php if (empty($products)): ?>
        <div class="empty-state fade-in-up" style="text-align: center; padding: 5rem 2rem;">
            <div class="empty-state-icon" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.5;"><i class="bi bi-diagram-3"></i></div>
            <h3>No hay productos para comparar</h3>
            <p style="margin: 1rem 0 2rem; color: var(--text-secondary);">Añade productos a la comparación desde la página del producto</p>
            <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary">Explorar Productos</a>
        </div>
    <?php else: ?>
        <div class="compare-table-wrapper" style="overflow-x: auto; background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg);">
            <table class="compare-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                        <th style="padding: 1.5rem; text-align: left; font-weight: 600; color: var(--text-primary); width: 200px; position: sticky; left: 0; z-index: 1; background: var(--bg-light);">Característica</th>
                        <?php foreach ($products as $index => $product): ?>
                            <?php 
                                $price = $product['sale_price'] ?? $product['price'];
                                $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                                $firstImage = getFirstImage($product);
                                $inStock = $product['stock'] > 0;
                            ?>
                            <th style="padding: 1.5rem; text-align: center; font-weight: 600; color: var(--text-primary); position: sticky; left: calc(200px + <?php echo $index * 250; ?>px); z-index: 1; background: var(--bg-light); min-width: 250px;">
                                <?php if ($firstImage): ?>
                                    <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height: 120px; margin-bottom: 1rem;">
                                <?php else: ?>
                                    <div style="height: 120px; display: flex; align-items: center; justify-content: center; background: var(--bg-lighter); margin-bottom: 1rem; font-size: 3rem; color: rgba(0,0,0,0.1);"><i class="bi bi-box"></i></div>
                                <?php endif; ?>
                                <div style="font-size: 1rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div class="product-price" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <span class="current-price" style="font-size: 1.25rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($price, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                    <?php if ($originalPrice): ?>
                                        <span class="original-price" style="font-size: 1rem; color: var(--text-muted); text-decoration: line-through;"><?php echo number_format($originalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-stock <?php echo $inStock ? 'stock-in' : 'stock-out'; ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; font-size: 0.8125rem; font-weight: 500; padding: 0.25rem 0.5rem; border-radius: var(--radius-full); <?php echo $inStock ? 'background: #ECFDF5; color: var(--success);' : 'background: #FEF2F2; color: var(--error);'; ?>">
                                    <i class="bi <?php echo $inStock ? 'bi-check-circle' : 'bi-x-circle'; ?>"></i>
                                    <?php echo $inStock ? 'En stock' : 'Agotado'; ?>
                                </div>
                                <button type="button" class="btn btn-ghost btn-sm remove-compare" data-id="<?php echo $product['id']; ?>" style="margin-top: 0.75rem; padding: 0.5rem 1rem;">
                                    <i class="bi bi-x"></i> Quitar
                                </button>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1.5rem; font-weight: 600; color: var(--text-primary); background: var(--bg-light); position: sticky; left: 0;">Categoría</td>
                        <?php foreach ($products as $product): ?>
                            <td style="padding: 1.5rem; text-align: center; color: var(--text-secondary);"><?php echo htmlspecialchars($product['short_description'] ?? 'Producto'); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1.5rem; font-weight: 600; color: var(--text-primary); background: var(--bg-light); position: sticky; left: 0;">SKU</td>
                        <?php foreach ($products as $product): ?>
                            <td style="padding: 1.5rem; text-align: center; color: var(--text-secondary); font-family: monospace; font-size: 0.875rem;"><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1.5rem; font-weight: 600; color: var(--text-primary); background: var(--bg-light); position: sticky; left: 0;">Precio</td>
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $price = $product['sale_price'] ?? $product['price'];
                                $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                            ?>
                            <td style="padding: 1.5rem; text-align: center;">
                                <span style="font-size: 1.25rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($price, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                <?php if ($originalPrice): ?>
                                    <div style="margin-top: 0.25rem;">
                                        <span style="font-size: 0.875rem; color: var(--text-muted); text-decoration: line-through;"><?php echo number_format($originalPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                        <span class="discount-badge" style="background: linear-gradient(135deg, var(--error), var(--secondary)); color: white; padding: 0.125rem 0.5rem; border-radius: var(--radius-full); font-size: 0.6875rem; font-weight: 600; margin-left: 0.5rem;">-<?php echo round(100 - ($price / $originalPrice * 100)); ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1.5rem; font-weight: 600; color: var(--text-primary); background: var(--bg-light); position: sticky; left: 0;">Stock</td>
                        <?php foreach ($products as $product): ?>
                            <?php 
                                $inStock = $product['stock'] > 0;
                                $lowStock = $product['stock'] > 0 && $product['stock'] <= 5;
                            ?>
                            <td style="padding: 1.5rem; text-align: center;">
                                <div class="product-stock <?php echo $inStock ? ($lowStock ? 'stock-low' : 'stock-in') : 'stock-out'; ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; font-size: 0.875rem; font-weight: 500; padding: 0.375rem 0.75rem; border-radius: var(--radius-full); <?php echo $inStock ? ($lowStock ? 'background: #FFFBEB; color: var(--warning);' : 'background: #ECFDF5; color: var(--success);') : 'background: #FEF2F2; color: var(--error);'; ?>">
                                    <i class="bi <?php echo $inStock ? ($lowStock ? 'bi-exclamation-triangle' : 'bi-check-circle') : 'bi-x-circle'; ?>"></i>
                                    <?php 
                                        if ($inStock) {
                                            echo $lowStock ? 'Solo ' . $product['stock'] . ' uds.' : 'Disponible';
                                        } else {
                                            echo 'Agotado';
                                        }
                                    ?>
                                </div>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 1.5rem; font-weight: 600; color: var(--text-primary); background: var(--bg-light); position: sticky; left: 0;">Descripción</td>
                        <?php foreach ($products as $product): ?>
                            <td style="padding: 1.5rem; text-align: center; color: var(--text-secondary); font-size: 0.875rem;"><?php echo htmlspecialchars(mb_strimwidth($product['description'] ?? $product['short_description'] ?? 'Sin descripción', 0, 200, '...')); ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="compare-actions" style="display: flex; gap: 1rem; margin-top: 2rem; justify-content: center; flex-wrap: wrap;">
            <?php foreach ($products as $product): ?>
                <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>" class="btn btn-primary">
                    <i class="bi bi-eye"></i> Ver <?php echo htmlspecialchars($product['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Remove from compare
document.querySelectorAll('.remove-compare').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo AppConfig::url('/comparar/eliminar/'); ?>' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo htmlspecialchars($csrf_token); ?>'
            }
        }).then(() => location.reload());
    });
});

// Clear compare
document.getElementById('clear-compare')?.addEventListener('click', function(e) {
    e.preventDefault();
    fetch('<?php echo AppConfig::url('/comparar/limpiar'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?php echo htmlspecialchars($csrf_token); ?>'
        }
    }).then(() => location.reload());
});
</script>