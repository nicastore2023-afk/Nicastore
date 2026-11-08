<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
use Nicastore\Core\Session;
?>

<?php
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $shipping = $data['shipping'];
    $tax = $data['tax'];
    $total = $data['total'];
    $coupon = Session::get('applied_coupon');
    $discount = $coupon['discount'] ?? 0;
    
    // Helper function to get first image
    function getFirstImage($item) {
        if (!empty($item['images'])) {
            $images = json_decode($item['images'], true);
            if (!empty($images)) return $images[0];
        }
        return $item['image'] ?? null;
    }
    
    // Nicaraguan departments for autocomplete
    $departments = [
        'Managua', 'León', 'Granada', 'Estelí', 'Matagalpa', 'Jinotega',
        'Chinandega', 'Masaya', 'Carazo', 'Rivas', 'Boaco', 'Chontales',
        'Río San Juan', 'Madriz', 'Nueva Segovia', 'Autonoma Atlántico Norte',
        'Autonoma Atlántico Sur'
    ];
?>

<div class="container cart-page" style="padding: 3rem 0;">
    <header class="cart-header fade-in-up" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="cart-title" style="margin: 0;">Carrito de Compras</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0;">Revisa tu pedido antes de finalizar la compra</p>
        </div>
        <span class="cart-count-badge" id="cart-count-badge" style="font-size: 0.875rem; padding: 0.5rem 1rem;"><?php echo $data['count']; ?> <?php echo $data['count'] === 1 ? 'producto' : 'productos'; ?></span>
    </header>
    
    <?php if (empty($items)): ?>
        <div class="empty-state fade-in-up" style="text-align: center; padding: 5rem 2rem;">
            <div class="empty-state-icon" style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.5;"><i class="bi bi-bag"></i></div>
            <h3>Tu carrito está vacío</h3>
            <p style="margin: 1rem 0 2rem; color: var(--text-secondary);">Parece que aún no has añadido ningún producto. ¡Explora nuestro catálogo y encuentra lo que necesitas!</p>
            <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-grid"></i> Ir a la Tienda
            </a>
            
            <!-- Recently viewed on empty cart -->
            <?php if (!empty($_COOKIE['recently_viewed'])): ?>
                <div style="margin-top: 4rem; text-align: left;">
                    <h4 style="margin-bottom: 1rem; color: var(--text-secondary);">Vistos Recientemente</h4>
                    <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                        <?php 
                        $recentIds = array_filter(explode(',', $_COOKIE['recently_viewed']), fn($id) => is_numeric($id));
                        $recentIds = array_slice($recentIds, 0, 4);
                        if (!empty($recentIds)): 
                            require_once __DIR__ . '/../../Core/Database.php';
                            $pdo = \Nicastore\Config\DatabaseConfig::getConnection();
                            $placeholders = str_repeat('?,', count($recentIds) - 1) . '?';
                            $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
                            $stmt->execute($recentIds);
                            $recentProducts = $stmt->fetchAll();
                            foreach ($recentProducts as $index => $rp): 
                                $rpPrice = $rp['sale_price'] ?? $rp['price'];
                                $rpImage = !empty($rp['images']) ? (json_decode($rp['images'], true)[0] ?? null) : ($rp['image'] ?? null);
                        ?>
                            <article class="product-card">
                                <div class="product-image">
                                    <?php if ($rpImage): ?>
                                        <img src="<?php echo AppConfig::asset('images/products/' . basename($rpImage)); ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                        <div style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); aspect-ratio: 1; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.3);"><i class="bi bi-box"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title"><a href="<?php echo AppConfig::url('/producto/' . $rp['slug']); ?>"><?php echo htmlspecialchars($rp['name']); ?></a></h3>
                                    <div class="product-price"><span class="current-price"><?php echo number_format($rpPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span></div>
                                    <form class="add-to-cart-form" action="<?php echo AppConfig::url('/carrito/agregar'); ?>" method="POST" style="width: 100%;">
                                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="product_id" value="<?php echo $rp['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="bi bi-bag-plus"></i> Añadir</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="checkout-grid" style="grid-template-columns: 1fr 400px; gap: 2rem; align-items: start;">
            <!-- Cart Items -->
            <div class="fade-in-up">
                <div class="cart-table-wrapper" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
                    <table class="cart-table" role="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                                <th scope="col" style="padding: 1.25rem 1rem; text-align: left; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Producto</th>
                                <th scope="col" style="padding: 1.25rem 1rem; text-align: left; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Precio</th>
                                <th scope="col" style="padding: 1.25rem 1rem; text-align: left; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Cantidad</th>
                                <th scope="col" style="padding: 1.25rem 1rem; text-align: left; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Subtotal</th>
                                <th scope="col" style="padding: 1.25rem 1rem; text-align: left; font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);"><span class="visually-hidden">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <?php
                                    $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                                    $itemTotal = $itemPrice * $item['quantity'];
                                    $firstImage = getFirstImage($item);
                                    $maxQty = $item['stock'];
                                ?>
                                <tr data-item-id="<?php echo $item['id']; ?>" style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);">
                                    <td data-label="Producto" style="padding: 1.25rem 1rem;">
                                        <div class="cart-item" style="display: flex; align-items: center; gap: 1rem;">
                                            <div class="cart-item-image" style="width: 80px; height: 80px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
                                                <?php if ($firstImage): ?>
                                                    <img src="<?php echo AppConfig::asset('images/products/' . basename($firstImage)); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="bi bi-box" style="font-size: 2rem; color: var(--text-muted); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cart-item-info" style="min-width: 0;">
                                                <h3 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 0.25rem;">
                                                    <a href="<?php echo AppConfig::url('/producto/' . $item['slug']); ?>" style="color: var(--text-primary);"><?php echo htmlspecialchars($item['name']); ?></a>
                                                </h3>
                                                <span class="cart-item-price" style="color: var(--secondary); font-weight: 600; font-size: 0.875rem;"><?php echo number_format($itemPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                                <?php if ($item['on_sale']): ?>
                                                    <span class="cart-item-meta" style="font-size: 0.8125rem; color: var(--text-muted); text-decoration: line-through; margin-left: 0.5rem;"><?php echo number_format($item['price'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                                <?php endif; ?>
                                                <?php if ($maxQty <= 5): ?>
                                                    <span class="stock-alert" style="display: inline-block; margin-top: 0.375rem; font-size: 0.75rem; color: var(--warning);"><i class="bi bi-exclamation-triangle"></i> Solo <?php echo $maxQty; ?> unidades disponibles</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Precio" style="padding: 1.25rem 1rem; font-weight: 500; color: var(--text-primary);"><?php echo number_format($itemPrice, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></td>
                                    <td data-label="Cantidad" style="padding: 1.25rem 1rem;">
                                        <form class="quantity-form update-cart-form" data-item-id="<?php echo $item['id']; ?>" style="display: flex; align-items: center; gap: 0.5rem;">
                                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <button type="button" class="qty-btn" data-action="decrease" aria-label="Disminuir cantidad" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-secondary); background: var(--bg-white); transition: var(--transition-fast);" <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $maxQty; ?>" aria-label="Cantidad" style="width: 60px; text-align: center; border: 1px solid var(--border); border-radius: var(--radius); padding: 0.5rem; font-weight: 500;">
                                            <button type="button" class="qty-btn" data-action="increase" aria-label="Aumentar cantidad" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-secondary); background: var(--bg-white); transition: var(--transition-fast);" <?php echo $item['quantity'] >= $maxQty ? 'disabled' : ''; ?>>
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td data-label="Subtotal" style="padding: 1.25rem 1rem;"><span class="item-subtotal" style="font-weight: 600; color: var(--text-primary); font-size: 1rem;"><?php echo number_format($itemTotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span></td>
                                    <td data-label="Acciones" style="padding: 1.25rem 1rem;">
                                        <form class="remove-cart-form" data-item-id="<?php echo $item['id']; ?>" style="display: inline;">
                                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <button type="submit" class="remove-btn" aria-label="Eliminar <?php echo htmlspecialchars($item['name']); ?>" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); color: var(--text-muted); background: transparent; border: 1px solid transparent; transition: var(--transition-fast);">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Promo codes suggestion -->
                <div style="margin-top: 1.5rem; padding: 1.5rem; background: var(--bg-light); border-radius: var(--radius-lg); border: 1px solid var(--border);">
                    <h4 style="margin: 0 0 1rem; font-size: 0.9375rem; color: var(--text-primary);"><i class="bi bi-gift"></i> Cupones disponibles</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <button type="button" class="apply-suggested-coupon btn btn-outline btn-sm" data-code="WELCOME10" style="padding: 0.5rem 1rem;">WELCOME10 (10% desc.)</button>
                        <button type="button" class="apply-suggested-coupon btn btn-outline btn-sm" data-code="NIKASTORE20" data-code="NIKASTORE20" style="padding: 0.5rem 1rem;">NIKASTORE20 (15% desc.)</button>
                        <button type="button" class="apply-suggested-coupon btn btn-outline btn-sm" data-code="FREESHIP" style="padding: 0.5rem 1rem;">FREESHIP (Envío gratis)</button>
                    </div>
                </div>
            </div>
            
            <!-- Summary -->
            <aside class="cart-summary fade-in-up" style="animation-delay: 200ms; position: sticky; top: calc(var(--header-height) + var(--header-top-height) + 1.5rem);">
                <div style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow);">
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);"><i class="bi bi-receipt"></i> Resumen del Pedido</h3>
                    
                    <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                        <span class="label">Subtotal</span>
                        <span class="value" id="cart-subtotal" style="font-weight: 600; color: var(--text-primary);"><?php echo number_format($subtotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                    </div>
                    
                    <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                        <span class="label">Envío</span>
                        <span class="value" id="cart-shipping" style="font-weight: 600; color: var(--text-primary);"><?php echo $shipping == 0 ? 'Gratis' : number_format($shipping, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                    </div>
                    
                    <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                        <span class="label">Impuestos (<?php echo $data['settings']['tax_rate'] ?? 15; ?>%)</span>
                        <span class="value" id="cart-tax" style="font-weight: 600; color: var(--text-primary);"><?php echo number_format($tax, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="summary-row summary-discount" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--success);">
                            <span class="label">Descuento (<?php echo htmlspecialchars($coupon['code']); ?>)</span>
                            <span class="value" style="font-weight: 600;">-<?php echo number_format($discount, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-row total" style="display: flex; justify-content: space-between; padding: 0.75rem 0 0; margin-top: 0.5rem; border-top: 2px solid var(--border); font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                        <span class="label">Total</span>
                        <span class="value" id="cart-total" style="color: var(--secondary);"><?php echo number_format($total, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                    </div>
                    
                    <!-- Free shipping progress -->
                    <?php 
                        $freeThreshold = (float)($data['settings']['free_shipping_threshold'] ?? 50);
                        $progress = min(100, ($subtotal / $freeThreshold) * 100);
                        $remaining = max(0, $freeThreshold - $subtotal);
                    ?>
                    <?php if ($subtotal < $freeThreshold): ?>
                        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                            <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.5rem;"><i class="bi bi-truck"></i> Añade <?php echo number_format($remaining, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?> más para envío gratis</p>
                            <div style="height: 6px; background: var(--bg-lighter); border-radius: 3px; overflow: hidden;">
                                <div style="width: <?php echo $progress; ?>%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 3px; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="margin-top: 1.5rem; padding: 1rem; background: #ECFDF5; border-radius: var(--radius); border: 1px solid #A7F3D0;">
                            <p style="margin: 0; color: var(--success); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-check-circle-fill"></i> ¡Envío gratis aplicado!</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="cart-actions" style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 1.5rem;">
                        <a href="<?php echo AppConfig::url('/checkout'); ?>" class="btn btn-primary btn-lg" style="width: 100%; text-align: center;">
                            <i class="bi bi-credit-card"></i> Finalizar Compra
                        </a>
                        <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-outline" style="width: 100%; text-align: center;">
                            <i class="bi bi-arrow-left"></i> Seguir Comprando
                        </a>
                    </div>
                    
                    <!-- Coupon Form -->
                    <form id="coupon-form" class="coupon-form" action="<?php echo AppConfig::url('/carrito/cupon'); ?>" method="POST" style="display: flex; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="text" name="coupon_code" class="form-control" placeholder="Código de cupón" aria-label="Código de cupón" style="flex: 1; padding: 0.75rem 1rem;">
                        <button type="submit" class="btn btn-secondary" style="white-space: nowrap;">Aplicar</button>
                    </form>
                    
                    <p style="margin-top: 1rem; font-size: 0.8125rem; color: var(--text-muted); text-align: center;">
                        <i class="bi bi-shield-lock"></i> Pago seguro y protegido
                    </p>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
// Nicaraguan departments for autocomplete
const nicaraguaDepartments = <?php echo json_encode($departments); ?>;

// Cart page specific interactions
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls with buttons
    document.querySelectorAll('.qty-btn[data-action]').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.qty-input');
            if (!input) return;
            
            const current = parseInt(input.value) || 1;
            const action = this.dataset.action;
            const max = parseInt(input.max) || 999;
            let newValue = current;
            
            if (action === 'increase') {
                newValue = Math.min(max, current + 1);
            } else if (action === 'decrease') {
                newValue = Math.max(1, current - 1);
            }
            
            if (newValue !== current) {
                input.value = newValue;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });
    
    // Suggested coupon buttons
    document.querySelectorAll('.apply-suggested-coupon').forEach(btn => {
        btn.addEventListener('click', function() {
            const code = this.dataset.code;
            const couponInput = document.querySelector('#coupon-form input[name="coupon_code"]');
            if (couponInput) {
                couponInput.value = code;
                document.getElementById('coupon-form').dispatchEvent(new Event('submit'));
            }
        });
    });
    
    // Quantity input change - AJAX update
    document.querySelectorAll('.qty-input').forEach(input => {
        let updateTimeout;
        input.addEventListener('change', function() {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                const form = this.closest('.update-cart-form, .quantity-form');
                if (!form) return;
                
                const itemId = form.dataset.itemId;
                const quantity = parseInt(this.value) || 1;
                
                if (!itemId) return;
                
                // Show loading state
                const row = form.closest('tr');
                if (row) row.style.opacity = '0.6';
                
                const formData = new FormData();
                formData.append('quantity', quantity);
                formData.append('_token', form.querySelector('input[name="_token"]')?.value || '');
                
                fetch(`/carrito/actualizar/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    if (row) row.style.opacity = '';
                    
                    if (result.success) {
                        // Update cart count in header
                        if (window.Nicastore) {
                            window.Nicastore.updateCartCount(result.count);
                        }
                        // Update summary
                        updateCartSummary(result);
                    } else {
                        alert(result.message);
                        this.value = this.dataset.previous || 1;
                    }
                })
                .catch(() => {
                    if (row) row.style.opacity = '';
                    alert('Error al actualizar el carrito');
                });
            }, 300);
        });
        
        input.addEventListener('focus', function() {
            this.dataset.previous = this.value;
        });
    });
    
    // Remove item with confirmation
    document.querySelectorAll('.remove-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!confirm('¿Eliminar este producto del carrito?')) return;
            
            const itemId = this.dataset.itemId;
            if (!itemId) return;
            
            const row = this.closest('tr');
            if (row) row.style.opacity = '0.5';
            
            try {
                const formData = new FormData();
                formData.append('_token', this.querySelector('input[name="_token"]')?.value || '');
                
                const response = await fetch(`/carrito/eliminar/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    if (window.Nicastore) {
                        window.Nicastore.updateCartCount(result.count);
                    }
                    updateCartSummary(result);
                    
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        row.style.transform = 'translateX(20px)';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            checkEmptyCart();
                        }, 300);
                    }
                } else {
                    if (row) row.style.opacity = '';
                    alert(result.message);
                }
            } catch (error) {
                if (row) row.style.opacity = '';
                alert('Error al eliminar el artículo');
            }
        });
    });
    
    // Coupon form AJAX
    const couponForm = document.getElementById('coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Aplicando...';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    updateCartSummary(result);
                    this.querySelector('input[name="coupon_code"]').value = '';
                    // Show success toast
                    showToast('Cupón aplicado correctamente', 'success');
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('Error al aplicar el cupón', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }
    
    function updateCartSummary(data) {
        const mappings = {
            'cart-subtotal': data.subtotal,
            'cart-shipping': data.shipping,
            'cart-tax': data.tax,
            'cart-total': data.total
        };
        
        Object.entries(mappings).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value !== undefined) {
                el.textContent = formatPrice(value);
            }
        });
        
        // Update shipping text if free
        const shippingEl = document.getElementById('cart-shipping');
        if (shippingEl && data.shipping !== undefined) {
            shippingEl.textContent = data.shipping == 0 ? 'Gratis' : formatPrice(data.shipping);
        }
        
        // Update free shipping progress bar
        updateFreeShippingProgress(data.subtotal);
        
        // Update coupon row
        updateCouponRow(data);
    }
    
    function updateFreeShippingProgress(subtotal) {
        const freeThreshold = <?php echo $freeThreshold; ?>;
        const progress = Math.min(100, (subtotal / freeThreshold) * 100);
        const remaining = Math.max(0, freeThreshold - subtotal);
        
        const progressContainer = document.querySelector('.cart-summary > div[style*="margin-top: 1.5rem"]');
        if (!progressContainer) return;
        
        if (subtotal < freeThreshold) {
            progressContainer.innerHTML = `
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0 0 0.5rem;"><i class="bi bi-truck"></i> Añade ${formatPrice(remaining)} más para envío gratis</p>
                <div style="height: 6px; background: var(--bg-lighter); border-radius: 3px; overflow: hidden;">
                    <div style="width: ${progress}%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); border-radius: 3px; transition: width 0.3s ease;"></div>
                </div>
            `;
        } else {
            progressContainer.innerHTML = `
                <div style="padding: 1rem; background: #ECFDF5; border-radius: var(--radius); border: 1px solid #A7F3D0;">
                    <p style="margin: 0; color: var(--success); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-check-circle-fill"></i> ¡Envío gratis aplicado!</p>
                </div>
            `;
        }
    }
    
    function updateCouponRow(data) {
        // This would need more complex DOM manipulation - for now just reload on coupon change
        if (data.discount !== undefined && data.discount > 0) {
            // Coupon was applied, reload to show discount row
            setTimeout(() => location.reload(), 500);
        }
    }
    
    function checkEmptyCart() {
        const rows = document.querySelectorAll('.cart-table tbody tr');
        if (rows.length === 0) {
            location.reload();
        }
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('es-NI', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(price);
    }
    
    function showToast(message, type = 'info') {
        // Remove existing toasts
        document.querySelectorAll('.toast').forEach(t => t.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast flash flash-${type}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideInRight 0.3s ease;
            max-width: 350px;
        `;
        
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            info: 'bi-info-circle-fill'
        };
        
        toast.innerHTML = `<i class="bi ${icons[type] || icons.info}"></i> <span>${message}</span>`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
</script>

<style>
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}
</style>