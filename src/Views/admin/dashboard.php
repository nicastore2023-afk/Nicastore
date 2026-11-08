<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php $stats = $data['stats']; ?>
<?php $recentOrders = $data['recentOrders']; ?>
<?php $lowStockProducts = $data['lowStockProducts'] ?? []; ?>
<?php $salesChart = $data['salesChart'] ?? []; ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Panel de Administración</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Bienvenido de nuevo. Aquí tienes un resumen de tu tienda.</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <a href="<?php echo AppConfig::url('/'); ?>" class="btn btn-secondary" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> Ver Tienda
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card primary" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden;">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #DBEAFE; color: var(--primary);"><i class="bi bi-people"></i></div>
            <span class="stat-trend" style="font-size: 0.75rem; font-weight: 600; color: var(--success);">+12%</span>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo number_format($stats['users']); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Usuarios Registrados</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);">vs mes anterior</div>
    </div>
    
    <div class="stat-card secondary" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden;">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #FFEDD5; color: var(--secondary);"><i class="bi bi-box"></i></div>
            <span class="stat-trend" style="font-size: 0.75rem; font-weight: 600; color: var(--success);">+5%</span>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo number_format($stats['products']); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Productos Totales</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);"><?php echo count($lowStockProducts); ?> con stock bajo</div>
    </div>
    
    <div class="stat-card success" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden;">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #D1FAE5; color: var(--success);"><i class="bi bi-bag-check"></i></div>
            <span class="stat-trend" style="font-size: 0.75rem; font-weight: 600; color: var(--warning);">-3%</span>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo number_format($stats['orders']); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Pedidos Totales</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);"><?php echo $stats['pending_orders']; ?> pendientes</div>
    </div>
    
    <div class="stat-card warning" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden;">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #FEF3C7; color: var(--warning);"><i class="bi bi-cash"></i></div>
            <span class="stat-trend" style="font-size: 0.75rem; font-weight: 600; color: var(--success);">+18%</span>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo number_format($stats['revenue'], 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Ingresos Totales</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);">Este mes: <?php echo number_format($stats['monthly_revenue'] ?? 0, 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></div>
    </div>
    
    <div class="stat-card info" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden;">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #DBEAFE; color: var(--info);"><i class="bi bi-clock"></i></div>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo number_format($stats['pending_orders']); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Pedidos Pendientes</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);">Requieren atención</div>
    </div>
    
    <div class="stat-card" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); transition: var(--transition); position: relative; overflow: hidden; border-left: 4px solid var(--error);">
        <div class="stat-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <div class="stat-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); font-size: 1.5rem; background: #FEF2F2; color: var(--error);"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
        <div class="stat-value" style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1.2;"><?php echo count($lowStockProducts); ?></div>
        <div class="stat-label" style="color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">Productos Stock Bajo</div>
        <div style="margin-top: 0.75rem; font-size: 0.75rem; color: var(--text-muted);">Requieren reposición</div>
    </div>
</div>

<!-- Charts & Recent Activity -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Sales Chart -->
    <div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; color: var(--primary);">Ventas (Últimos 30 días)</h3>
            <select id="chart-period" class="form-control select-wrapper" style="width: auto; padding: 0.5rem 2rem 0.5rem 1rem; font-size: 0.8125rem;">
                <option value="30" selected>30 días</option>
                <option value="7">7 días</option>
                <option value="90">90 días</option>
            </select>
        </div>
        <div style="padding: 1.5rem; height: 300px;">
            <canvas id="salesChart" style="width: 100%; height: 100%;"></canvas>
        </div>
    </div>
    
    <!-- Low Stock & Recent Orders -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Low Stock Products -->
        <div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid var(--border);">
                <h3 style="margin: 0; color: var(--primary);"><i class="bi bi-exclamation-triangle"></i> Stock Bajo (<?php echo count($lowStockProducts); ?>)</h3>
                <a href="<?php echo AppConfig::url('/admin/productos?stock=low'); ?>" class="btn btn-sm btn-outline">Ver Todos</a>
            </div>
            
            <?php if (empty($lowStockProducts)): ?>
                <div class="empty-state" style="padding: 3rem; margin: 0; text-align: center;">
                    <div class="empty-state-icon" style="color: var(--success);"><i class="bi bi-check-circle"></i></div>
                    <h4>¡Todo en orden!</h4>
                    <p style="color: var(--text-muted);">Todos los productos tienen stock suficiente</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--bg-light); border-bottom: 1px solid var(--border);">
                                <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Producto</th>
                                <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Stock</th>
                                <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($lowStockProducts, 0, 5) as $product): ?>
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td style="padding: 1rem;">
                                        <div style="font-weight: 500;"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div style="font-size: 0.8125rem; color: var(--text-muted);">SKU: <?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span class="badge" style="background: <?php echo $product['stock'] == 0 ? '#FEF2F2' : '#FFFBEB'; ?>; color: <?php echo $product['stock'] == 0 ? 'var(--error)' : 'var(--warning)'; ?>; padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; border-radius: var(--radius-full);">
                                            <?php echo $product['stock'] == 0 ? 'Agotado' : $product['stock'] . ' unidades'; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <a href="<?php echo AppConfig::url('/admin/productos/editar/' . $product['id']); ?>" class="btn btn-sm btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                            <i class="bi bi-pencil"></i> Reponer
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border);">
                <h3 style="margin: 0; color: var(--primary);">Acciones Rápidas</h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; padding: 1.5rem;">
                <a href="<?php echo AppConfig::url('/admin/productos/nuevo'); ?>" class="quick-action" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text-primary); transition: var(--transition);">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--primary); color: white; border-radius: var(--radius); font-size: 1.5rem;"><i class="bi bi-plus-lg"></i></div>
                    <span style="font-weight: 500;">Nuevo Producto</span>
                </a>
                <a href="<?php echo AppConfig::url('/admin/categorias'); ?>" class="quick-action" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text-primary); transition: var(--transition);">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--secondary); color: white; border-radius: var(--radius); font-size: 1.5rem;"><i class="bi bi-tags"></i></div>
                    <span style="font-weight: 500;">Categorías</span>
                </a>
                <a href="<?php echo AppConfig::url('/admin/pedidos?status=pending'); ?>" class="quick-action" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text-primary); transition: var(--transition);">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--warning); color: white; border-radius: var(--radius); font-size: 1.5rem;"><i class="bi bi-clock"></i></div>
                    <span style="font-weight: 500;">Pedidos Pendientes</span>
                </a>
                <a href="<?php echo AppConfig::url('/admin/configuracion'); ?>" class="quick-action" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text-primary); transition: var(--transition);">
                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--info); color: white; border-radius: var(--radius); font-size: 1.5rem;"><i class="bi bi-gear"></i></div>
                    <span style="font-weight: 500;">Configuración</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders (Full Width) -->
<div class="data-table" style="margin-top: 1.5rem; background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm);">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 1rem;">
        <h3 style="margin: 0; color: var(--primary);">Pedidos Recientes</h3>
        <a href="<?php echo AppConfig::url('/admin/pedidos'); ?>" class="btn btn-sm btn-secondary">Ver Todos</a>
    </div>
    
    <?php if (empty($recentOrders)): ?>
        <div class="empty-state" style="padding: 3rem; margin: 0; text-align: center;">
            <div class="empty-state-icon"><i class="bi bi-bag"></i></div>
            <h4>No hay pedidos recientes</h4>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-light); border-bottom: 1px solid var(--border);">
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); width: 80px;">ID</th>
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Cliente</th>
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); width: 150px;">Total</th>
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); width: 130px;">Estado</th>
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); width: 180px;">Fecha</th>
                        <th scope="col" style="padding: 1rem; text-align: left; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); width: 100px;"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);" data-href="<?php echo AppConfig::url('/admin/pedidos/' . $order['id']); ?>">
                            <td style="padding: 1rem;"><strong>#<?php echo $order['id']; ?></strong></td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 500;"><?php echo htmlspecialchars($order['user_name']); ?></div>
                                <div style="font-size: 0.8125rem; color: var(--text-muted);"><?php echo htmlspecialchars($order['user_email']); ?></div>
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-primary);"><?php echo number_format($order['total'], 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></td>
                            <td style="padding: 1rem;">
                                <span class="status-badge status-<?php echo $order['status']; ?>" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 600; border-radius: var(--radius-full); text-transform: capitalize; <?php 
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
                            </td>
                            <td style="padding: 1rem;"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td style="padding: 1rem;">
                                <a href="<?php echo AppConfig::url('/admin/pedidos/' . $order['id']); ?>" class="btn btn-sm btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    const salesData = <?php echo json_encode($salesChart); ?>;
    
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels || [],
            datasets: [{
                label: 'Ingresos',
                data: salesData.data || [],
                borderColor: '#1E3A8A',
                backgroundColor: 'rgba(30, 58, 138, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#1E3A8A',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Ingresos: $' + context.parsed.y.toLocaleString('es-NI', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748B', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    ticks: { 
                        color: '#64748B', 
                        font: { size: 11 },
                        callback: function(value) { return '$' + value.toLocaleString(); }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Period selector
    document.getElementById('chart-period')?.addEventListener('change', function() {
        const period = this.value;
        // In a real app, this would fetch new data via AJAX
        console.log('Change period to:', period);
    });
    
    // Hover effects for stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = 'var(--shadow-md)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'var(--shadow-sm)';
        });
    });
    
    // Quick actions hover
    document.querySelectorAll('.quick-action').forEach(action => {
        action.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = 'var(--shadow-md)';
            this.style.borderColor = 'var(--primary)';
        });
        action.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
            this.style.borderColor = 'var(--border)';
        });
    });
});
</script>

<style>
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-card.primary::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--primary); }
.stat-card.secondary::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--secondary); }
.stat-card.success::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--success); }
.stat-card.warning::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--warning); }
.stat-card.info::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--info); }

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .quick-action {
        min-width: 140px !important;
    }
}
</style>