<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php $user = $data['user']; ?>

<div class="container account-page" style="padding: 3rem 0;">
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;">Mi Cuenta</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Bienvenido, <?php echo htmlspecialchars($user['name']); ?></p>
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
                <?php if ($user['role'] === 'admin'): ?>
                    <span style="display: inline-block; margin-top: 0.5rem; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.2); border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600;">
                        <i class="bi bi-shield-check"></i> Administrador
                    </span>
                <?php endif; ?>
            </div>
            <nav class="account-nav" aria-label="Navegación de cuenta">
                <a href="<?php echo AppConfig::url('/cuenta'); ?>" class="<?php echo basename($_SERVER['REQUEST_URI']) === 'cuenta' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Panel
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'], '/cuenta/pedidos') ? 'active' : ''; ?>">
                    <i class="bi bi-bag-check"></i> Mis Pedidos
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>" class="<?php echo str_contains($_SERVER['REQUEST_URI'], '/cuenta/perfil') ? 'active' : ''; ?>">
                    <i class="bi bi-person-gear"></i> Mi Perfil
                </a>
                <?php if ($user['role'] === 'admin'): ?>
                    <hr style="margin: 1rem 1rem; border-color: var(--border);">
                    <a href="<?php echo AppConfig::url('/admin'); ?>">
                        <i class="bi bi-gear"></i> Panel Admin
                    </a>
                <?php endif; ?>
                <hr style="margin: 1rem 1rem; border-color: var(--border);">
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
            <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem;">Panel de Control</h2>
            
            <div class="account-stats">
                <div class="stat-card primary">
                    <div class="stat-value"><?php echo count($data['orders']); ?></div>
                    <div class="stat-label">Pedidos Totales</div>
                </div>
                <div class="stat-card secondary">
                    <div class="stat-value">
                        <?php
                            $cart = new \Nicastore\Models\Cart();
                            echo $cart->getCount();
                        ?>
                    </div>
                    <div class="stat-label">En el Carrito</div>
                </div>
                <div class="stat-card success">
                    <div class="stat-value">
                        <?php
                            $totalSpent = 0;
                            foreach ($data['orders'] as $order) {
                                if ($order['status'] !== 'cancelled') {
                                    $totalSpent += $order['total'];
                                }
                            }
                            echo number_format($totalSpent, 0);
                        ?>
                    </div>
                    <div class="stat-label">Gastado ($)</div>
                </div>
            </div>
            
            <h3 style="margin: 2rem 0 1rem;">Pedidos Recientes</h3>
            
            <?php if (empty($data['orders'])): ?>
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="bi bi-bag"></i></div>
                    <h3>No tienes pedidos aún</h3>
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
                                <th scope="col">Total</th>
                                <th scope="col">Estado</th>
                                <th scope="col"><span class="visually-hidden">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['orders'] as $order): ?>
                                <tr data-href="<?php echo AppConfig::url('/cuenta/pedido/' . $order['id']); ?>">
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
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
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="btn btn-secondary">
                        Ver Todos los Pedidos <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>