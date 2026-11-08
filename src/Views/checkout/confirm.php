<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $shipping = $data['shipping'];
    $tax = $data['tax'];
    $discount = $data['discount'];
    $total = $data['total'];
    $shippingData = $data['shippingData'];
    $paymentData = $data['paymentData'];
    $coupon = $data['coupon'] ?? null;
?>

<div class="container checkout-page" style="padding: 3rem 0;">
    <nav class="breadcrumb" aria-label="Navegación" style="margin-bottom: 2rem;">
        <a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <a href="<?php echo AppConfig::url('/carrito'); ?>"><i class="bi bi-bag"></i> Carrito</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <span aria-current="page">Confirmar Pedido</span>
    </nav>
    
    <!-- Progress indicator -->
    <div class="checkout-progress" style="margin-bottom: 2rem;">
        <div class="progress-steps" style="display: flex; justify-content: space-between; position: relative;">
            <div class="progress-line" style="position: absolute; top: 20px; left: 10%; right: 10%; height: 3px; background: var(--border); z-index: 0;">
                <div class="progress-fill" style="height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); width: 100%; transition: width 0.3s ease;"></div>
            </div>
            <div class="step completed" data-step="1" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;"><i class="bi bi-check"></i></div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: var(--primary);">Envío</span>
            </div>
            <div class="step completed" data-step="2" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;"><i class="bi bi-check"></i></div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: var(--primary);">Pago</span>
            </div>
            <div class="step active" data-step="3" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;">3</div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: var(--primary);">Confirmar</span>
            </div>
        </div>
    </div>
    
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;">Confirmar Pedido</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Revisa toda la información antes de confirmar tu pedido</p>
    </header>
    
    <form method="POST" action="<?php echo AppConfig::url('/checkout/confirmar'); ?>" id="checkout-confirm-form">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="checkout-grid">
            <!-- Confirmation Details -->
            <div class="fade-in-up">
                <!-- Shipping Address -->
                <div class="checkout-form" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-truck"></i> Dirección de Envío</h3>
                    
                    <address style="font-style: normal; line-height: 1.8; color: var(--text-secondary);">
                        <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($shippingData['shipping_name']); ?></strong><br>
                        <?php echo htmlspecialchars($shippingData['shipping_address']); ?><br>
                        <?php echo htmlspecialchars($shippingData['shipping_city'] . ', ' . $shippingData['shipping_state']); ?><br>
                        <?php echo htmlspecialchars($shippingData['shipping_zip'] . ', ' . $shippingData['shipping_country']); ?><br>
                        Tel: <?php echo htmlspecialchars($shippingData['shipping_phone']); ?><br>
                        Email: <?php echo htmlspecialchars($shippingData['shipping_email']); ?>
                        <?php if (!empty($shippingData['notes'])): ?>
                            <br>Notas: <?php echo htmlspecialchars($shippingData['notes']); ?>
                        <?php endif; ?>
                    </address>
                </div>
                
                <!-- Payment Method -->
                <div class="checkout-form" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-credit-card"></i> Método de Pago</h3>
                    
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: var(--bg-light); border-radius: var(--radius); border: 1px solid var(--border);">
                        <?php 
                            $methodIcons = [
                                'card' => 'bi-credit-card',
                                'cash' => 'bi-cash',
                                'transfer' => 'bi-bank'
                            ];
                            $methodNames = [
                                'card' => 'Tarjeta de Crédito/Débito',
                                'cash' => 'Efectivo contra Entrega',
                                'transfer' => 'Transferencia Bancaria'
                            ];
                            $method = $paymentData['method'] ?? 'cash';
                        ?>
                        <i class="bi <?php echo $methodIcons[$method] ?? 'bi-credit-card'; ?>" style="font-size: 2rem; color: var(--primary);"></span>
                        <div>
                            <strong style="color: var(--text-primary);"><?php echo $methodNames[$method] ?? 'Efectivo contra Entrega'; ?></span>
                            <br><small style="color: var(--text-muted);">Método de pago seleccionado</small>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="checkout-form" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-bag-check"></i> Productos</h3>
                    
                    <div class="order-items" style="margin-bottom: 1.5rem;">
                        <?php foreach ($items as $item): ?>
                            <?php
                                $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                            ?>
                            <div class="order-item" style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                                <div class="order-item-image" style="width: 50px; height: 50px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-box" style="font-size: 1.5rem; color: var(--text-muted); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-info" style="flex: 1; min-width: 0;">
                                    <div class="order-item-name" style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="order-item-qty" style="font-size: 0.8125rem; color: var(--text-muted);">Cant: <?php echo $item['quantity']; ?> <?php if ($item['on_sale']): ?><span class="sale-badge" style="margin-left: 0.5rem; font-size: 0.6875rem; background: var(--error); color: white; padding: 0.125rem 0.375rem; border-radius: var(--radius-full);">OFERTA</span><?php endif; ?></div>
                                </div>
                                <div class="order-item-price" style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem; white-space: nowrap;"><?php echo number_format($itemPrice * $item['quantity'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-top: 2rem;">
                    <a href="<?php echo AppConfig::url('/checkout/paso2'); ?>" class="btn btn-secondary" style="padding: 1rem 2rem;">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" style="padding: 1rem 2rem;">
                        <i class="bi bi-check-circle"></i> Confirmar y Pagar
                    </button>
                </div>
            </div>
            
            <!-- Order Summary -->
            <aside class="fade-in-up" style="animation-delay: 200ms;">
                <div class="checkout-summary" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; height: fit-content; position: sticky; top: calc(var(--header-height) + var(--header-top-height) + 1.5rem); box-shadow: var(--shadow);">
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);"><i class="bi bi-receipt"></i> Resumen Final</h3>
                    
                    <div class="order-items" style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 0.5rem;">
                        <?php foreach ($items as $item): ?>
                            <?php
                                $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                            ?>
                            <div class="order-item" style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                                <div class="order-item-image" style="width: 50px; height: 50px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-box" style="font-size: 1.5rem; color: var(--text-muted); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-info" style="flex: 1; min-width: 0;">
                                    <div class="order-item-name" style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="order-item-qty" style="font-size: 0.8125rem; color: var(--text-muted);">Cant: <?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="order-item-price" style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem; white-space: nowrap;"><?php echo number_format($item['on_sale'] ? $item['sale_price'] : $item['price'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="checkout-totals">
                        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                            <span class="label">Subtotal</span>
                            <span class="value"><?php echo number_format($subtotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                        </div>
                        
                        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                            <span class="label">Envío</span>
                            <span class="value"><?php echo $shipping == 0 ? '<span style="color: var(--success); font-weight: 600;">Gratis</span>' : number_format($shipping, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                        </div>
                        
                        <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                            <span class="label">Impuestos (<?php echo $data['settings']['tax_rate'] ?? 15; ?>%)</span>
                            <span class="value"><?php echo number_format($tax, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                        </div>
                        
                        <?php if ($discount > 0): ?>
                            <div class="summary-row summary-discount" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--success);">
                                <span class="label">Descuento</span>
                                <span class="value">-<?php echo number_format($discount, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="summary-row total" style="display: flex; justify-content: space-between; padding: 0.75rem 0 0; margin-top: 0.5rem; border-top: 2px solid var(--border); font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                            <span class="label">Total Final</span>
                            <span class="value" style="color: var(--secondary);"><?php echo number_format($total, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-confirm-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Procesando...';
            }
        });
    });
});
</script>