<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php
$user = $data['user'];
$orders = $data['orders'];
$pagination = $data['pagination'] ?? null;
?>

<div class="container account-page" style="padding: 3rem 0;">
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;">Mis Pedidos</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Historial completo de tus compras</p>
    </header>
    
    <div class="account-layout">
        <!-- Sidebar -->
        <aside class="account-sidebar fade-in-up">
            <div class="account-sidebar-header">
                <div class="account-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <nav class="account-nav" aria-label="Navegación de cuenta">
                <a href="<?php echo AppConfig::url('/cuenta'); ?>">
                    <i class="bi bi-speedometer2"></i> Panel
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="active">
                    <i class="bi bi-bag-check"></i> Mis Pedidos
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>">
                    <i class="bi bi-person-gear"></i> Mi Perfil
                </a>
                <hr style="margin: 1rem; border-color: var(--border);">
                <form action="<?php echo AppConfig::url('/logout'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: left; background: none; border: none; color: var(--error);">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- Content -->
        <main class="account-content fade-in-up" style="animation-delay: 100ms;">
            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-bag"></i></div>
                    <h3>No tienes pedidos</h3>
                    <p>Cuando realices tu primera compra aparecerá aquí</p>
                    <a href="<?php echo AppConfig::url('/productos'); ?>" class="btn btn-primary" style="margin-top: 1.5rem;">Ir a la Tienda</a>
                </div>
            <?php else: ?>
                <div class="data-table">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th scope="col">Pedido</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Productos</th>
                                <th scope="col">Total</th>
                                <th scope="col">Estado</th>
                                <th scope="col"><span class="visually-hidden">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr data-href="<?php echo AppConfig::url('/cuenta/pedido/' . $order['id']); ?>">
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <span class="badge badge-info"><?php echo $order['items_count'] ?? 0; ?> items</span>
                                    </td>
                                    <td><strong><?php echo number_format($order['total'], 2, ',', '.'); ?> $</strong></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo AppConfig::url('/cuenta/pedido/' . $order['id']); ?>" class="btn btn-view">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($pagination && $pagination['last_page'] > 1): ?>
                    <?php
                        $currentPage = $pagination['current_page'];
                        $lastPage = $pagination['last_page'];
                        $baseUrl = AppConfig::url('/cuenta/pedidos');
                    ?>
                    <nav class="pagination" aria-label="Paginación de pedidos">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo $baseUrl . '?page=' . ($currentPage - 1); ?>" aria-label="Página anterior"><i class="bi bi-chevron-left"></i></a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
                        <?php endif; ?>
                        
                        <?php
                            $start = max(1, $currentPage - 2);
                            $end = min($lastPage, $currentPage + 2);
                            
                            if ($start > 1) {
                                echo '<a href="' . $baseUrl . '?page=1">1</a>';
                                if ($start > 2) echo '<span class="disabled">...</span>';
                            }
                            
                            for ($i = $start; $i <= $end; $i++):
                        ?>
                            <?php if ($i == $currentPage): ?>
                                <span class="active" aria-current="page"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo $baseUrl . '?page=' . $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($end < $lastPage): ?>
                            <?php if ($end < $lastPage - 1) echo '<span class="disabled">...</span>'; ?>
                            <a href="<?php echo $baseUrl . '?page=' . $lastPage; ?>"><?php echo $lastPage; ?></a>
                        <?php endif; ?>
                        
                        <?php if ($currentPage < $lastPage): ?>
                            <a href="<?php echo $baseUrl . '?page=' . ($currentPage + 1); ?>" aria-label="Página siguiente"><i class="bi bi-chevron-right"></i></a>
                        <?php else: ?>
                            <span class="disabled" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>