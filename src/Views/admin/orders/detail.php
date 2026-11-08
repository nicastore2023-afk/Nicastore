<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $order = $data['order'];
    $items = $data['items'];
?>

<div class="admin-header">
    <h1>Pedido #<?php echo $order['id']; ?></h1>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?php echo AppConfig::url('/admin/pedidos'); ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                <i class="bi bi-pencil"></i> Cambiar Estado
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="checkout-grid">
    <div>
        <!-- Order Info -->
        <div class="data-table" style="padding: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="margin: 0; color: var(--primary);">Información del Pedido</h3>
                <span class="status-badge status-<?php echo $order['status']; ?>" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">Cliente</p>
                    <p style="font-weight: 600;"><?php echo htmlspecialchars($order['user_name']); ?></p>
                    <p style="color: var(--text-muted); font-size: 0.875rem;"><?php echo htmlspecialchars($order['user_email']); ?></p>
                </div>
                <div>
                    <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">Fecha</p>
                    <p style="font-weight: 600;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                </div>
                <div>
                    <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">Total</p>
                    <p style="font-weight: 700; color: var(--secondary); font-size: 1.25rem;"><?php echo number_format($order['total'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></p>
                </div>
                <div>
                    <p style="color: var(--text-secondary); margin-bottom: 0.25rem;">Subtotal</p>
                    <p style="font-weight: 600;"><?php echo number_format($order['subtotal'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Shipping -->
        <div class="data-table" style="padding: 1.5rem;">
            <h3 style="margin-bottom: 1rem; color: var(--primary);">Dirección de Envío</h3>
            <address style="font-style: normal; color: var(--text-secondary); line-height: 1.8;">
                <strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong><br>
                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                <?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>, <?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?><br>
                <?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?> - <?php echo htmlspecialchars($order['shipping_country'] ?? 'Nicaragua'); ?><br>
                Email: <?php echo htmlspecialchars($order['shipping_email']); ?><br>
                <?php if ($order['shipping_phone']): ?>
                    Tel: <?php echo htmlspecialchars($order['shipping_phone']); ?>
                <?php endif; ?>
            </address>
            <?php if ($order['notes']): ?>
                <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-light); border-radius: var(--radius);">
                    <strong style="color: var(--text-primary);">Notas del cliente:</strong><br>
                    <span style="color: var(--text-secondary);"><?php echo htmlspecialchars($order['notes']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Items & Actions -->
    <div>
        <!-- Items -->
        <div class="data-table" style="padding: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 1rem; color: var(--primary);">Productos</h3>
            
            <?php foreach ($items as $item): ?>
                <?php
                    $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                    $itemTotal = $itemPrice * $item['quantity'];
                ?>
                <div class="order-item" style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border); align-items: center;">
                    <div class="order-item-image" style="width: 60px; height: 60px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
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
                    <div class="order-item-price" style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem; white-space: nowrap;"><?php echo number_format($itemTotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                </div>
            <?php endforeach; ?>
            
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid var(--border); display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div style="text-align: right;">
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Subtotal</div>
                    <div style="font-weight: 600;"><?php echo number_format($order['subtotal'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                </div>
                <div style="text-align: right;">
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Envío</div>
                    <div style="font-weight: 600;"><?php echo $order['shipping'] == 0 ? 'Gratis' : number_format($order['shipping'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                </div>
                <div style="text-align: right;">
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Impuestos</div>
                    <div style="font-weight: 600;"><?php echo number_format($order['tax'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                </div>
                <?php if ($order['discount'] > 0): ?>
                    <div style="text-align: right; color: var(--success);">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">Descuento</div>
                        <div style="font-weight: 600;">-<?php echo number_format($order['discount'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                    </div>
                <?php endif; ?>
                <div style="text-align: right; border-top: 2px solid var(--border); padding-top: 1rem; grid-column: 1 / -1;">
                    <div style="color: var(--text-secondary); font-size: 0.875rem;">Total</div>
                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($order['total'], 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Update Status Modal -->
        <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: var(--bg-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); border: none; overflow: hidden;">
                    <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                        <h5 id="statusModalLabel" style="margin: 0; color: var(--primary);">Cambiar Estado del Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer;">&times;</button>
                    </div>
                    <form method="POST" action="<?php echo AppConfig::url('/admin/pedidos/' . $order['id'] . '/estado'); ?>">
                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="modal-body" style="padding: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label" for="status">Nuevo Estado</label>
                                <select name="status" id="status" class="form-control select-wrapper" required>
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Procesando</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Entregado</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer" style="padding: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" style="position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;"></div>

<!-- Simple Modal Implementation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('statusModal');
    const openBtn = document.querySelector('[data-bs-target="#statusModal"]');
    const closeBtn = modal?.querySelector('.btn-close');
    const cancelBtn = modal?.querySelector('[data-bs-dismiss="modal"]');
    const statusForm = modal?.querySelector('form');
    
    if (!modal) return;
    
    const openModal = () => {
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        document.body.style.overflow = 'hidden';
        const focusable = modal.querySelectorAll('button, select, input, a');
        if (focusable.length) focusable[0].focus();
    };
    
    const closeModal = () => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };
    
    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });
    
    // Handle form submission via AJAX
    statusForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Actualizando...';
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast(result.message, 'success');
                closeModal();
                
                // Update the status badge in the UI
                const newStatus = document.getElementById('status').value;
                const statusBadge = document.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    statusBadge.className = 'status-badge status-' + newStatus;
                }
            } else {
                showToast(result.message || 'Error al actualizar el estado', 'error');
            }
        } catch (error) {
            showToast('Error de conexión. Intente nuevamente.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    // Toast notification system
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        if (!container) return;
        
        const toast = document.createElement('div');
        toast.className = `toast flash flash-${type}`;
        toast.style.cssText = `
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
            info: 'bi-info-circle-fill',
            warning: 'bi-exclamation-triangle-fill'
        };
        
        toast.innerHTML = `<i class="bi ${icons[type] || icons.info}"></i> <span>${message}</span>`;
        
        // Add close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cssText = 'background: none; border: none; color: inherit; font-size: 1.25rem; cursor: pointer; margin-left: 0.5rem; line-height: 1;';
        closeBtn.addEventListener('click', () => toast.remove());
        toast.appendChild(closeBtn);
        
        document.getElementById('toast-container').appendChild(toast);
        
        // Auto-remove after 4 seconds
        setTimeout(() => {
            toast.style.animation = 'fadeIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
});
</script>