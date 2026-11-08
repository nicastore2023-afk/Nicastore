<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php
$order = $data['order'];
$items = $data['items'];
$user = $data['user'];
?>

<div class="container account-page" style="padding: 3rem 0;">
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 class="section-title" style="text-align: left; margin: 0;">Pedido #<?php echo $order['id']; ?></h1>
                <p style="color: var(--text-secondary); margin-top: 0.5rem;">Realizado el <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            </div>
            <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </header>
    
    <div class="account-layout">
        <!-- Sidebar -->
        <aside class="account-sidebar fade-in-up">
            <div style="padding: 1.5rem;">
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Estado del Pedido</h4>
                <div class="status-timeline" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php
                        $statuses = ['pending' => 'Pendiente', 'processing' => 'Procesando', 'shipped' => 'Enviado', 'delivered' => 'Entregado', 'cancelled' => 'Cancelado'];
                        $currentStatus = $order['status'];
                        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($currentStatus, $statusOrder);
                        if ($currentIndex === false) $currentIndex = 0;
                    ?>
                    <?php foreach ($statusOrder as $index => $status): ?>
                        <div class="status-step <?php echo $index < $currentIndex ? 'completed' : ($index === $currentIndex ? 'active' : ''); ?>" style="display: flex; align-items: flex-start; gap: 1rem; position: relative;">
                            <div class="status-dot" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.75rem; font-weight: 600; background: var(--bg-light); color: var(--text-muted); border: 2px solid var(--border); z-index: 1;">
                                <?php echo $index + 1; ?>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: var(--text-primary);"><?php echo $statuses[$status]; ?></div>
                                <div style="font-size: 0.8125rem; color: var(--text-muted);">
                                    <?php if ($index < $currentIndex): ?>
                                        Completado
                                    <?php elseif ($index === $currentIndex): ?>
                                        En progreso
                                    <?php else: ?>
                                        Pendiente
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($index < count($statusOrder) - 1): ?>
                            <div style="position: absolute; left: 15px; top: 32px; bottom: -32px; width: 2px; background: var(--border); z-index: 0;"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>
        
        <!-- Content -->
        <main class="account-content fade-in-up" style="animation-delay: 100ms;">
            <!-- Order Info -->
            <div class="data-table" style="margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 1.5rem;">
                    <div>
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Información del Pedido</h4>
                        <dl style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1.5rem; font-size: 0.9375rem;">
                            <dt style="color: var(--text-secondary);">Número</dt>
                            <dd style="font-weight: 600; color: var(--text-primary);">#<?php echo $order['id']; ?></dd>
                            
                            <dt style="color: var(--text-secondary);">Fecha</dt>
                            <dd><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></dd>
                            
                            <dt style="color: var(--text-secondary);">Estado</dt>
                            <dd>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </dd>
                            
                            <dt style="color: var(--text-secondary);">Total</dt>
                            <dd style="font-weight: 700; color: var(--secondary); font-size: 1.125rem;"><?php echo number_format($order['total'], 2, ',', '.'); ?> $</dd>
                        </dl>
                    </div>
                    
                    <div>
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Dirección de Envío</h4>
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
                                <strong style="color: var(--text-primary);">Notas:</strong><br>
                                <span style="color: var(--text-secondary);"><?php echo htmlspecialchars($order['notes']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="data-table" style="margin-bottom: 2rem;">
                <table class="orders-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 60px;"></th>
                            <th scope="col">Producto</th>
                            <th scope="col" style="width: 100px;">Precio</th>
                            <th scope="col" style="width: 80px;">Cant.</th>
                            <th scope="col" style="width: 120px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $itemPrice = $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius);">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; border-radius: var(--radius); background: var(--bg-light); display: flex; align-items: center; justify-content: center;"><i class="bi bi-box"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo AppConfig::url('/producto/' . $item['slug']); ?>" style="font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($item['name']); ?></a>
                                </td>
                                <td><?php echo number_format($itemPrice, 2, ',', '.'); ?> $</td>
                                <td style="text-align: center;"><?php echo $item['quantity']; ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?php echo number_format($itemTotal, 2, ',', '.'); ?> $</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Totals -->
                <div style="padding: 1.5rem; border-top: 2px solid var(--border); display: flex; justify-content: flex-end; gap: 2rem;">
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">Subtotal</div>
                        <div style="font-weight: 600;"><?php echo number_format($order['subtotal'], 2, ',', '.'); ?> $</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">Envío</div>
                        <div style="font-weight: 600;"><?php echo $order['shipping'] == 0 ? 'Gratis' : number_format($order['shipping'], 2, ',', '.') . ' $'; ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">Impuestos</div>
                        <div style="font-weight: 600;"><?php echo number_format($order['tax'], 2, ',', '.'); ?> $</div>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                        <div style="text-align: right; color: var(--success);">
                            <div style="color: var(--text-secondary); font-size: 0.875rem;">Descuento</div>
                            <div style="font-weight: 600;">-<?php echo number_format($order['discount'], 2, ',', '.'); ?> $</div>
                        </div>
                    <?php endif; ?>
                    <div style="text-align: right; border-top: 2px solid var(--border); padding-top: 1rem; margin-top: 0.5rem;">
                        <div style="color: var(--text-secondary); font-size: 0.875rem;">Total</div>
                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--secondary);"><?php echo number_format($order['total'], 2, ',', '.'); ?> $</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>