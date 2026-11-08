<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
use Nicastore\Models\Setting;
?>

<?php
    $order = $data['order'];
    $items = $data['items'];
    $settings = (new Setting())->getAll();
    $currency = $settings['currency'] ?? 'USD';
    $currencySymbol = $currency === 'USD' ? '$' : ($currency === 'EUR' ? '€' : ($currency === 'NIO' ? 'C$' : '$'));
    
    // Calculate estimated delivery
    $orderDate = new DateTime($order['created_at']);
    $estimatedDelivery = clone $orderDate;
    $estimatedDelivery->add(new DateInterval('P3D')); // 3 business days
    while (in_array($estimatedDelivery->format('N'), [6, 7])) {
        $estimatedDelivery->add(new DateInterval('P1D'));
    }
?>

<div class="container success-page">
    <div class="success-card" style="max-width: 800px;">
        <div class="success-icon" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; background: #ECFDF5; border-radius: 50%; font-size: 3rem; color: var(--success); margin: 0 auto 1.5rem;">
            <i class="bi bi-check-lg"></i>
        </div>
        
        <h2 style="font-size: 2rem; margin-bottom: 0.5rem;">¡Pedido Confirmado!</h2>
        <p style="color: var(--text-secondary); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto;">Gracias por tu compra en Nicastore. Tu pedido ha sido procesado correctamente y te enviaremos una confirmación por email.</p>
        
        <!-- Order details grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div class="order-info-card" style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; border: 1px solid var(--border);">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">NÚMERO DE PEDIDO</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary); font-family: monospace;">#<?php echo $order['id']; ?></div>
            </div>
            
            <div class="order-info-card" style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; border: 1px solid var(--border);">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">FECHA</div>
                <div style="font-size: 1rem; font-weight: 600; color: var(--text-primary);"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></div>
            </div>
            
            <div class="order-info-card" style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; border: 1px solid var(--border);">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">ESTADO</div>
                <span class="status-badge status-<?php echo $order['status']; ?>" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 1rem; font-size: 0.8125rem; font-weight: 600; border-radius: var(--radius-full); text-transform: capitalize; <?php 
                    $statusColors = [
                        'pending' => 'background: #FEF3C7; color: #92400E;',
                        'processing' => 'background: #DBEAFE; color: #1E40AF;',
                        'shipped' => 'background: #E0E7FF; color: #3730A3;',
                        'delivered' => 'background: #D1FAE5; color: #065F46;',
                        'cancelled' => 'background: #FEF2F2; color: #991B1B;',
                    ];
                    echo $statusColors[$order['status']] ?? '';
                ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
            
            <div class="order-info-card" style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; border: 1px solid var(--border);">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">ENTREGA ESTIMADA</div>
                <div style="font-size: 1rem; font-weight: 600; color: var(--text-primary);"><?php echo $estimatedDelivery->format('d/m/Y'); ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">3-5 días hábiles</div>
            </div>
            
            <div class="order-info-card" style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 1.5rem; text-align: center; border: 1px solid var(--border); grid-column: span 2;">
                <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">TOTAL PAGADO</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($order['total'], 2, ',', '.') . ' ' . $currencySymbol; ?></div>
            </div>
        </div>
        
        <!-- Products -->
        <div style="margin-bottom: 2rem;">
            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">Productos</h4>
            <div class="order-items-list" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
                <?php foreach ($items as $index => $item): ?>
                    <div class="order-item-row" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-bottom: <?php echo $index < count($items) - 1 ? '1px solid var(--border)' : 'none'; ?>">
                        <div style="width: 60px; height: 60px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
                            <?php if ($item['image']): ?>
                                <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-box" style="font-size: 1.5rem; color: var(--text-muted); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-weight: 500; color: var(--text-primary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Cantidad: <?php echo $item['quantity']; ?></div>
                        </div>
                        <div style="font-weight: 600; color: var(--text-primary); white-space: nowrap;"><?php echo number_format($item['total'], 2, ',', '.') . ' ' . $currencySymbol; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Shipping Address -->
        <div style="margin-bottom: 2rem;">
            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border);">Dirección de Envío</h4>
            <address style="font-style: normal; color: var(--text-secondary); line-height: 1.8; background: var(--bg-light); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border);">
                <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($order['shipping_name']); ?></strong><br>
                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                <?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>, <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?><br>
                <?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?> - <?php echo htmlspecialchars($order['shipping_country'] ?? 'Nicaragua'); ?><br>
                Email: <?php echo htmlspecialchars($order['shipping_email']); ?><br>
                <?php if ($order['shipping_phone']): ?>
                    Tel: <?php echo htmlspecialchars($order['shipping_phone']); ?>
                <?php endif; ?>
            </address>
        </div>
        
        <!-- Next Steps -->
        <div style="margin-bottom: 2rem; padding: 1.5rem; background: #DBEAFE; border-radius: var(--radius-lg); border: 1px solid #93C5FD;">
            <h4 style="margin: 0 0 1rem; color: #1E40AF; display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-info-circle"></i> ¿Qué sigue?</h4>
            <ul style="margin: 0; padding-left: 1.5rem; color: #1E40AF; line-height: 2;">
                <li>Recibirás un email de confirmación en los próximos minutos</li>
                <li>Prepararemos tu pedido y te notificaremos cuando sea enviado</li>
                <li>Podrás rastrear tu pedido desde <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" style="color: var(--primary); font-weight: 600;">Mi Cuenta > Mis Pedidos</a></li>
                <li>Entrega estimada: <strong><?php echo $estimatedDelivery->format('d/m/Y'); ?></strong></li>
            </ul>
        </div>
        
        <!-- Share & Actions -->
        <div class="success-actions" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin-top: 1rem;">
            <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="btn btn-primary btn-lg">
                <i class="bi bi-bag-check"></i> Ver Mis Pedidos
            </a>
            <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-secondary btn-lg">
                <i class="bi bi-grid"></i> Seguir Comprando
            </a>
        </div>
        
        <!-- Share buttons -->
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); text-align: center;">
            <p style="margin: 0 0 1rem; color: var(--text-secondary); font-size: 0.875rem;">Comparte tu compra</p>
            <div style="display: inline-flex; gap: 0.75rem;">
                <a href="https://wa.me/?text=<?php echo urlencode('¡Acabo de hacer mi pedido #' . $order['id'] . ' en Nicastore! ' . AppConfig::url('/pedido/' . $order['id'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en WhatsApp" class="share-btn" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #25D366; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(AppConfig::url('/pedido/' . $order['id'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en Facebook" class="share-btn" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #1877F2; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode('¡Acabo de hacer mi pedido #' . $order['id'] . ' en Nicastore!'); ?>&url=<?php echo urlencode(AppConfig::url('/pedido/' . $order['id'])); ?>" target="_blank" rel="noopener" aria-label="Compartir en Twitter" class="share-btn" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); background: #000000; color: white; font-size: 1.25rem; transition: var(--transition-fast);"></a>
            </div>
        </div>
    </div>
</div>

<style>
.share-btn:hover {
    transform: translateY(-2px) scale(1.1);
    filter: brightness(1.1);
}
</style>