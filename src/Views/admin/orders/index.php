<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $orders = $data['orders']; ?>
<?php $pagination = $data['pagination']; ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Gestión de Pedidos</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Administra y procesa los pedidos de tu tienda</p>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div id="bulk-actions-bar" style="display: none; background: var(--primary); color: white; padding: 1rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span id="selected-count" style="font-weight: 600;">0</span> pedidos seleccionados
        <button type="button" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);" id="clear-selection">Limpiar</button>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-sm bulk-action" data-action="processing" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-gear"></i> Procesando
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="shipped" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-truck"></i> Enviados
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="delivered" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-check-circle"></i> Entregados
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="cancelled" style="background: rgba(239,68,68,0.3); border: 1px solid rgba(239,68,68,0.5);">
            <i class="bi bi-x-circle"></i> Cancelados
        </button>
    </div>
</div>

<!-- Filters -->
<div class="data-table" style="padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius-lg) var(--radius-lg) 0 0; margin-bottom: 0;">
    <form method="GET" action="<?php echo AppConfig::url('/admin/pedidos'); ?>" id="orders-filter-form" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
        <div style="flex: 1; min-width: 250px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Buscar</label>
            <div class="input-group" style="display: flex;">
                <input type="search" name="q" class="form-control" placeholder="Cliente, email, ID..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <button type="submit" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0.875rem 1.25rem;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div style="min-width: 180px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Estado</label>
            <select name="status" class="form-control select-wrapper">
                <option value="">Todos los estados</option>
                <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pendientes</option>
                <option value="processing" <?php echo ($_GET['status'] ?? '') === 'processing' ? 'selected' : ''; ?>>Procesando</option>
                <option value="shipped" <?php echo ($_GET['status'] ?? '') === 'shipped' ? 'selected' : ''; ?>>Enviados</option>
                <option value="delivered" <?php echo ($_GET['status'] ?? '') === 'delivered' ? 'selected' : ''; ?>>Entregados</option>
                <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelados</option>
            </select>
        </div>
        <div style="min-width: 180px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Fecha desde</label>
            <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
        </div>
        <div style="min-width: 180px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Fecha hasta</label>
            <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($_GET['date_to'] ?? ''); ?>">
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Ordenar</label>
            <select name="sort" class="form-control select-wrapper">
                <option value="newest" <?php echo ($_GET['sort'] ?? '') === 'newest' ? 'selected' : ''; ?>>Más recientes</option>
                <option value="oldest" <?php echo ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : ''; ?>>Más antiguos</option>
                <option value="total_desc" <?php echo ($_GET['sort'] ?? '') === 'total_desc' ? 'selected' : ''; ?>>Total: Mayor a Menor</option>
                <option value="total_asc" <?php echo ($_GET['sort'] ?? '') === 'total_asc' ? 'selected' : ''; ?>>Total: Menor a Mayor</option>
            </select>
        </div>
        <a href="<?php echo AppConfig::url('/admin/pedidos'); ?>" class="btn btn-outline" style="height: fit-content; padding: 0.875rem 1.5rem;">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </form>
</div>

<!-- Orders Table -->
<div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: 0 0 var(--radius-lg) var(--radius-lg); overflow: hidden;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/pedidos/bulk'); ?>" id="bulk-form">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" id="bulk-action-input" value="">
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                        <th scope="col" style="width: 50px; padding: 1rem; text-align: center;">
                            <input type="checkbox" id="select-all" aria-label="Seleccionar todos">
                        </th>
                        <th scope="col" style="width: 80px; padding: 1rem;">ID</th>
                        <th scope="col" style="padding: 1rem;">Cliente</th>
                        <th scope="col" style="width: 130px; padding: 1rem;">Total</th>
                        <th scope="col" style="width: 130px; padding: 1rem;">Estado</th>
                        <th scope="col" style="width: 180px; padding: 1rem;">Fecha</th>
                        <th scope="col" style="width: 100px; padding: 1rem;"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr data-id="<?php echo $order['id']; ?>" style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);">
                            <td style="padding: 1rem; text-align: center;">
                                <input type="checkbox" class="row-checkbox" name="ids[]" value="<?php echo $order['id']; ?>" aria-label="Seleccionar pedido #<?php echo $order['id']; ?>">
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary);">#<?php echo $order['id']; ?></td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 500;"><?php echo htmlspecialchars($order['user_name']); ?></div>
                                <div style="font-size: 0.8125rem; color: var(--text-muted);"><?php echo htmlspecialchars($order['user_email']); ?></div>
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-primary);"><?php echo number_format($order['total'], 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></td>
                            <td style="padding: 1rem;">
                                <span class="status-badge status-<?php echo $order['status']; ?>" style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border-radius: var(--radius-full); text-transform: capitalize; <?php 
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
                
                <?php if (empty($orders)): ?>
                    <tbody>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class="bi bi-bag" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                                No se encontraron pedidos
                            </td>
                        </tr>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </form>
</div>

<!-- Pagination -->
<?php if ($pagination['last_page'] > 1): ?>
    <?php
        $currentPage = $pagination['current_page'];
        $lastPage = $pagination['last_page'];
        $baseUrl = AppConfig::url('/admin/pedidos');
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = $queryParams ? '?' . http_build_query($queryParams) : '';
    ?>
    <nav class="pagination" aria-label="Paginación de pedidos" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="color: var(--text-secondary); font-size: 0.875rem;">
            Mostrando <?php echo $pagination['from']; ?>-<?php echo $pagination['to']; ?> de <?php echo $pagination['total']; ?> pedidos
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <?php if ($currentPage > 1): ?>
                <?php $params = array_merge($queryParams, ['page' => $currentPage - 1]); ?>
                <a href="<?php echo $baseUrl . '?' . http_build_query($params); ?>" class="btn btn-sm btn-outline" aria-label="Página anterior"><i class="bi bi-chevron-left"></i></a>
            <?php else: ?>
                <span class="btn btn-sm btn-outline disabled" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            <?php endif; ?>
            
            <?php
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);
                
                if ($start > 1) {
                    $params = array_merge($queryParams, ['page' => 1]);
                    echo '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="btn btn-sm btn-outline">1</a>';
                    if ($start > 2) echo '<span class="btn btn-sm btn-outline disabled">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++):
            ?>
                <?php if ($i == $currentPage): ?>
                    <span class="btn btn-sm btn-primary" aria-current="page"><?php echo $i; ?></span>
                <?php else: ?>
                    <?php $params = array_merge($queryParams, ['page' => $i]); ?>
                    <a href="<?php echo $baseUrl . '?' . http_build_query($params); ?>" class="btn btn-sm btn-outline"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($end < $lastPage): ?>
                <?php if ($end < $lastPage - 1) echo '<span class="btn btn-sm btn-outline disabled">...</span>'; ?>
                <?php $params = array_merge($queryParams, ['page' => $lastPage]); ?>
                <a href="<?php echo $baseUrl . '?' . http_build_query($params); ?>" class="btn btn-sm btn-outline"><?php echo $lastPage; ?></a>
            <?php endif; ?>
            
            <?php if ($currentPage < $lastPage): ?>
                <?php $params = array_merge($queryParams, ['page' => $currentPage + 1]); ?>
                <a href="<?php echo $baseUrl . '?' . http_build_query($params); ?>" class="btn btn-sm btn-outline" aria-label="Página siguiente"><i class="bi bi-chevron-right"></i></a>
            <?php else: ?>
                <span class="btn btn-sm btn-outline disabled" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>

<script>
// Bulk actions and row selection
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCount = document.getElementById('selected-count');
    const clearSelection = document.getElementById('clear-selection');
    const bulkActionBtns = document.querySelectorAll('.bulk-action');
    const bulkForm = document.getElementById('bulk-form');
    const bulkActionInput = document.getElementById('bulk-action-input');
    
    // Toggle all checkboxes
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkUI();
        });
    }
    
    // Individual checkbox changes
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkUI);
    });
    
    function updateBulkUI() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;
        
        if (count > 0) {
            bulkActionsBar.style.display = 'flex';
            selectedCount.textContent = count;
            
            // Update select-all indeterminate state
            if (selectAll) {
                selectAll.checked = count === rowCheckboxes.length;
                selectAll.indeterminate = count > 0 && count < rowCheckboxes.length;
            }
        } else {
            bulkActionsBar.style.display = 'none';
            if (selectAll) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            }
        }
    }
    
    // Clear selection
    clearSelection?.addEventListener('click', function() {
        rowCheckboxes.forEach(cb => cb.checked = false);
        if (selectAll) selectAll.checked = false;
        updateBulkUI();
    });
    
    // Bulk actions
    bulkActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const checked = document.querySelectorAll('.row-checkbox:checked');
            
            if (checked.length === 0) {
                alert('Selecciona al menos un pedido');
                return;
            }
            
            let confirmMsg = '';
            const statusLabels = {
                'processing': 'Procesando',
                'shipped': 'Enviados',
                'delivered': 'Entregados',
                'cancelled': 'Cancelados'
            };
            confirmMsg = '¿Marcar ' + checked.length + ' pedido(s) como ' + statusLabels[action] + '?';
            
            if (!confirm(confirmMsg)) return;
            
            bulkActionInput.value = action;
            bulkForm.action = '<?php echo AppConfig::url('/admin/pedidos/bulk'); ?>';
            bulkForm.submit();
        });
    });
    
    // Row click to view (but not on checkbox or buttons)
    document.querySelectorAll('tbody tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' || e.target.closest('button, a')) return;
            window.location.href = '<?php echo AppConfig::url('/admin/pedidos/'); ?>' + this.dataset.id;
        });
        
        // Visual feedback
        row.style.cursor = 'pointer';
    });
});
</script>

<style>
.data-table input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    accent-color: var(--primary);
    cursor: pointer;
}

.data-table tr:hover {
    background: var(--bg-light);
}

#bulk-actions-bar {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .data-table {
        font-size: 0.875rem;
    }
    .data-table th, .data-table td {
        padding: 0.75rem 0.5rem;
    }
    .bulk-action { padding: 0.375rem 0.5rem; font-size: 0.8125rem; }
}
</style>