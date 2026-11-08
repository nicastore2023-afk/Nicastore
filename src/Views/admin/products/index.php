<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $products = $data['products']; ?>
<?php $pagination = $data['pagination']; ?>
<?php $categories = (new \Nicastore\Models\Category())->getActive(); ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Gestión de Productos</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Administra tu catálogo de productos</p>
    </div>
    <a href="<?php echo AppConfig::url('/admin/productos/nuevo'); ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo Producto
    </a>
</div>

<!-- Bulk Actions Bar (hidden by default) -->
<div id="bulk-actions-bar" style="display: none; background: var(--primary); color: white; padding: 1rem 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <span id="selected-count" style="font-weight: 600;">0</span> productos seleccionados
        <button type="button" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);" id="clear-selection">Limpiar</button>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button type="button" class="btn btn-sm bulk-action" data-action="activate" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-check-circle"></i> Activar
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="deactivate" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-x-circle"></i> Desactivar
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="feature" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-star"></i> Destacar
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="unfeature" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
            <i class="bi bi-star"></i> Quitar Destacado
        </button>
        <button type="button" class="btn btn-sm bulk-action" data-action="delete" style="background: rgba(239,68,68,0.3); border: 1px solid rgba(239,68,68,0.5);">
            <i class="bi bi-trash"></i> Eliminar
        </button>
    </div>
</div>

<!-- Filters -->
<div class="data-table" style="padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius-lg) var(--radius-lg) 0 0; margin-bottom: 0;">
    <form method="GET" action="<?php echo AppConfig::url('/admin/productos'); ?>" id="products-filter-form" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
        <div style="flex: 1; min-width: 250px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Buscar</label>
            <div class="input-group" style="display: flex;">
                <input type="search" name="q" class="form-control" placeholder="Nombre, SKU, descripción..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <button type="submit" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0.875rem 1.25rem;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div style="min-width: 180px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Categoría</label>
            <select name="category" class="form-control select-wrapper">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Estado</label>
            <select name="status" class="form-control select-wrapper">
                <option value="">Todos</option>
                <option value="1" <?php echo ($_GET['status'] ?? '') === '1' ? 'selected' : ''; ?>>Activos</option>
                <option value="0" <?php echo ($_GET['status'] ?? '') === '0' ? 'selected' : ''; ?>>Inactivos</option>
            </select>
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Stock</label>
            <select name="stock" class="form-control select-wrapper">
                <option value="">Todos</option>
                <option value="out" <?php echo ($_GET['stock'] ?? '') === 'out' ? 'selected' : ''; ?>>Agotados</option>
                <option value="low" <?php echo ($_GET['stock'] ?? '') === 'low' ? 'selected' : ''; ?>>Stock Bajo (≤10)</option>
                <option value="ok" <?php echo ($_GET['stock'] ?? '') === 'ok' ? 'selected' : ''; ?>>Con Stock (>10)</option>
            </select>
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Destacado</label>
            <select name="featured" class="form-control select-wrapper">
                <option value="">Todos</option>
                <option value="1" <?php echo ($_GET['featured'] ?? '') === '1' ? 'selected' : ''; ?>>Sí</option>
                <option value="0" <?php echo ($_GET['featured'] ?? '') === '0' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>
        <div style="min-width: 120px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Ordenar</label>
            <select name="sort" class="form-control select-wrapper">
                <option value="newest" <?php echo ($_GET['sort'] ?? '') === 'newest' ? 'selected' : ''; ?>>Más recientes</option>
                <option value="oldest" <?php echo ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : ''; ?>>Más antiguos</option>
                <option value="name_asc" <?php echo ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : ''; ?>>Nombre A-Z</option>
                <option value="name_desc" <?php echo ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : ''; ?>>Nombre Z-A</option>
                <option value="price_asc" <?php echo ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : ''; ?>>Precio: Menor a Mayor</option>
                <option value="price_desc" <?php echo ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : ''; ?>>Precio: Mayor a Menor</option>
                <option value="stock_asc" <?php echo ($_GET['sort'] ?? '') === 'stock_asc' ? 'selected' : ''; ?>>Stock: Menor a Mayor</option>
                <option value="stock_desc" <?php echo ($_GET['sort'] ?? '') === 'stock_desc' ? 'selected' : ''; ?>>Stock: Mayor a Menor</option>
            </select>
        </div>
        <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-outline" style="height: fit-content; padding: 0.875rem 1.5rem;">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </form>
</div>

<!-- Products Table -->
<div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: 0 0 var(--radius-lg) var(--radius-lg); overflow: hidden;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/productos/bulk'); ?>" id="bulk-form">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <input type="hidden" name="action" id="bulk-action-input" value="">
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                        <th scope="col" style="width: 50px; padding: 1rem; text-align: center;">
                            <input type="checkbox" id="select-all" aria-label="Seleccionar todos">
                        </th>
                        <th scope="col" style="width: 60px; padding: 1rem;">ID</th>
                        <th scope="col" style="width: 70px; padding: 1rem;">Imagen</th>
                        <th scope="col" style="padding: 1rem;">Producto</th>
                        <th scope="col" style="width: 130px; padding: 1rem;">Precio</th>
                        <th scope="col" style="width: 100px; padding: 1rem;">Stock</th>
                        <th scope="col" style="width: 100px; padding: 1rem;">Estado</th>
                        <th scope="col" style="width: 90px; padding: 1rem;">Destacado</th>
                        <th scope="col" style="width: 160px; padding: 1rem;"><span class="visually-hidden">Acciones</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <?php 
                            $price = $product['sale_price'] ?? $product['price'];
                            $originalPrice = $product['sale_price'] && $product['sale_price'] < $product['price'] ? $product['price'] : null;
                            $inStock = $product['stock'] > 0;
                            $lowStock = $product['stock'] > 0 && $product['stock'] <= 10;
                            $stockClass = $product['stock'] == 0 ? 'danger' : ($lowStock ? 'warning' : 'success');
                        ?>
                        <tr data-id="<?php echo $product['id']; ?>" style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);">
                            <td style="padding: 1rem; text-align: center;">
                                <input type="checkbox" class="row-checkbox" name="ids[]" value="<?php echo $product['id']; ?>" aria-label="Seleccionar producto <?php echo htmlspecialchars($product['name']); ?>">
                            </td>
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary);">#<?php echo $product['id']; ?></td>
                            <td style="padding: 1rem; text-align: center;">
                                <div style="width: 50px; height: 50px; border-radius: var(--radius); overflow: hidden; background: var(--bg-light); margin: 0 auto;">
                                    <?php if ($product['image']): ?>
                                        <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted); display: flex; width: 100%; height: 100%; align-items: center; justify-content: center;"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: var(--text-primary); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($product['name']); ?></div>
                                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
                                    SKU: <code style="background: var(--bg-light); padding: 0.125rem 0.375rem; border-radius: var(--radius-sm);"><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></code>
                                    <?php if ($product['category_name'] ?? false): ?>
                                        | <?php echo htmlspecialchars($product['category_name']); ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 1rem;">
                                <strong><?php echo number_format($price, 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></strong>
                                <?php if ($originalPrice): ?>
                                    <div style="font-size: 0.8125rem; color: var(--text-muted); text-decoration: line-through;">
                                        <?php echo number_format($originalPrice, 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?>
                                    </div>
                                    <span class="badge badge-danger" style="font-size: 0.6875rem; margin-left: 0.5rem;">-<?php echo round(100 - ($price / $originalPrice * 100)); ?>%</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge badge-<?php echo $stockClass; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem; display: inline-flex; align-items: center; gap: 0.375rem;">
                                    <i class="bi <?php echo $product['stock'] == 0 ? 'bi-x-circle' : ($lowStock ? 'bi-exclamation-triangle' : 'bi-check-circle'); ?>"></i>
                                    <?php echo $product['stock'] == 0 ? 'Agotado' : ($lowStock ? 'Bajo: ' . $product['stock'] : $product['stock'] . ' u.'); ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge badge-<?php echo $product['is_active'] ? 'success' : 'danger'; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <?php echo $product['is_active'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <span class="badge badge-<?php echo $product['is_featured'] ? 'primary' : 'secondary'; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <?php echo $product['is_featured'] ? 'Sí' : 'No'; ?>
                                </span>
                            </td>
                            <td style="padding: 1rem;">
                                <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                    <a href="<?php echo AppConfig::url('/admin/productos/editar/' . $product['id']); ?>" class="btn btn-sm btn-primary" title="Editar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>" title="Eliminar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                
                <?php if (empty($products)): ?>
                    <tbody>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class="bi bi-box" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                                No se encontraron productos
                                <a href="<?php echo AppConfig::url('/admin/productos/nuevo'); ?>" class="btn btn-primary" style="margin-top: 1rem;"><i class="bi bi-plus-lg"></i> Crear el primero</a>
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
        $baseUrl = AppConfig::url('/admin/productos');
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = $queryParams ? '?' . http_build_query($queryParams) : '';
    ?>
    <nav class="pagination" aria-label="Paginación de productos" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="color: var(--text-secondary); font-size: 0.875rem;">
            Mostrando <?php echo $pagination['from']; ?>-<?php echo $pagination['to']; ?> de <?php echo $pagination['total']; ?> productos
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
    const deleteBtns = document.querySelectorAll('.delete-btn');
    
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
                alert('Selecciona al menos un producto');
                return;
            }
            
            let confirmMsg = '';
            switch(action) {
                case 'activate': confirmMsg = '¿Activar ' + checked.length + ' producto(s)?'; break;
                case 'deactivate': confirmMsg = '¿Desactivar ' + checked.length + ' producto(s)?'; break;
                case 'feature': confirmMsg = '¿Marcar como destacados ' + checked.length + ' producto(s)?'; break;
                case 'unfeature': confirmMsg = '¿Quitar destacado de ' + checked.length + ' producto(s)?'; break;
                case 'delete': confirmMsg = '¿ELIMINAR ' + checked.length + ' producto(s)? Esta acción NO se puede deshacer.'; break;
            }
            
            if (!confirm(confirmMsg)) return;
            
            bulkActionInput.value = action;
            bulkForm.action = '<?php echo AppConfig::url('/admin/productos/bulk'); ?>';
            bulkForm.submit();
        });
    });
    
    // Individual delete buttons
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            if (!confirm('¿Eliminar "' + name + '"? Esta acción no se puede deshacer.')) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo AppConfig::url('/admin/productos/eliminar/'); ?>' + id;
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '<?php echo htmlspecialchars($csrf_token); ?>';
            form.appendChild(token);
            
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Row click to edit (but not on checkbox or buttons)
    document.querySelectorAll('tbody tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' || e.target.closest('button, a')) return;
            window.location.href = '<?php echo AppConfig::url('/admin/productos/editar/'); ?>' + this.dataset.id;
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

.data-table tr.selected {
    background: #DBEAFE;
}

.row-checkbox:checked + .row-checkbox-label,
.row-checkbox:checked ~ td {
    background: #DBEAFE;
}

#bulk-actions-bar {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.375rem 0.75rem;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success { background: #D1FAE5; color: #065F46; }
.badge-warning { background: #FEF3C7; color: #92400E; }
.badge-danger { background: #FEF2F2; color: #991B1B; }
.badge-primary { background: #DBEAFE; color: #1E40AF; }
.badge-secondary { background: #F1F5F9; color: #475569; }
.badge-info { background: #DBEAFE; color: #1E40AF; }

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