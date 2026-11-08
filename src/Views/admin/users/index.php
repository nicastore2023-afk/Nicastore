<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $users = $data['users']; ?>
<?php $pagination = $data['pagination']; ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Gestión de Usuarios</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Administra los usuarios registrados</p>
    </div>
</div>

<!-- Filters -->
<div class="data-table" style="padding: 1.5rem; background: var(--bg-light); border: 1px solid var(--border); border-radius: var(--radius-lg) var(--radius-lg) 0 0; margin-bottom: 0;">
    <form method="GET" action="<?php echo AppConfig::url('/admin/usuarios'); ?>" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
        <div style="flex: 1; min-width: 250px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Buscar</label>
            <div class="input-group" style="display: flex;">
                <input type="search" name="q" class="form-control" placeholder="Nombre, email..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <button type="submit" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0.875rem 1.25rem;">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Rol</label>
            <select name="role" class="form-control select-wrapper">
                <option value="">Todos</option>
                <option value="admin" <?php echo ($_GET['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                <option value="customer" <?php echo ($_GET['role'] ?? '') === 'customer' ? 'selected' : ''; ?>>Cliente</option>
            </select>
        </div>
        <div style="min-width: 150px;">
            <label class="form-label" style="margin-bottom: 0.375rem;">Estado</label>
            <select name="status" class="form-control select-wrapper">
                <option value="">Todos</option>
                <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Activos</option>
                <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactivos</option>
            </select>
        </div>
        <a href="<?php echo AppConfig::url('/admin/usuarios'); ?>" class="btn btn-outline" style="height: fit-content; padding: 0.875rem 1.5rem;">
            <i class="bi bi-x"></i> Limpiar
        </a>
    </form>
</div>

<!-- Users Table -->
<div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: 0 0 var(--radius-lg) var(--radius-lg); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                    <th scope="col" style="width: 50px; padding: 1rem; text-align: center;">
                        <input type="checkbox" id="select-all" aria-label="Seleccionar todos">
                    </th>
                    <th scope="col" style="width: 60px; padding: 1rem;">ID</th>
                    <th scope="col" style="padding: 1rem;">Usuario</th>
                    <th scope="col" style="width: 120px; padding: 1rem;">Rol</th>
                    <th scope="col" style="width: 100px; padding: 1rem;">Estado</th>
                    <th scope="col" style="width: 150px; padding: 1rem;">Pedidos</th>
                    <th scope="col" style="width: 150px; padding: 1rem;">Total Gastado</th>
                    <th scope="col" style="width: 130px; padding: 1rem;">Registrado</th>
                    <th scope="col" style="width: 130px; padding: 1rem;">Último Acceso</th>
                    <th scope="col" style="width: 130px; padding: 1rem;"><span class="visually-hidden">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr data-id="<?php echo $user['id']; ?>" style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);">
                        <td style="padding: 1rem; text-align: center;">
                            <input type="checkbox" class="row-checkbox" name="ids[]" value="<?php echo $user['id']; ?>" aria-label="Seleccionar usuario <?php echo htmlspecialchars($user['name']); ?>">
                        </td>
                        <td style="padding: 1rem; font-weight: 600; color: var(--text-secondary);">#<?php echo $user['id']; ?></td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1rem;">
                                    <?php echo strtoupper($user['name'][0] ?? '?'); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($user['name']); ?></div>
                                    <div style="font-size: 0.8125rem; color: var(--text-muted);"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'primary' : 'secondary'; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                <i class="bi bi-<?php echo $user['role'] === 'admin' ? 'person-badge' : 'person'; ?>"></i>
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                <?php echo $user['is_active'] ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="font-weight: 600; color: var(--text-primary);"><?php echo $user['orders_count'] ?? 0; ?></span>
                        </td>
                        <td style="padding: 1rem;">
                            <span style="font-weight: 600; color: var(--secondary);"><?php echo number_format($user['total_spent'] ?? 0, 2, ',', '.') . ' ' . ($data['settings']['currency'] === 'USD' ? '$' : ($data['settings']['currency'] ?? '$')); ?></span>
                        </td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: var(--text-secondary);"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                        <td style="padding: 1rem; font-size: 0.875rem; color: var(--text-secondary);">
                            <?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                <a href="<?php echo AppConfig::url('/admin/usuarios/' . $user['id']); ?>" class="btn btn-sm btn-primary" title="Ver Detalles" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo AppConfig::url('/admin/usuarios/editar/' . $user['id']); ?>" class="btn btn-sm btn-secondary" title="Editar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($user['id'] !== ($data['current_user_id'] ?? 0)): ?>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $user['id']; ?>" data-name="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>" data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>" title="Eliminar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            
            <?php if (empty($users)): ?>
                <tbody>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="bi bi-people" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                            No se encontraron usuarios
                        </td>
                    </tr>
                </tbody>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['last_page'] > 1): ?>
    <?php
        $currentPage = $pagination['current_page'];
        $lastPage = $pagination['last_page'];
        $baseUrl = AppConfig::url('/admin/usuarios');
        $queryParams = $_GET;
        unset($queryParams['page']);
    ?>
    <nav class="pagination" aria-label="Paginación de usuarios" style="padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="color: var(--text-secondary); font-size: 0.875rem;">
            Mostrando <?php echo $pagination['from']; ?>-<?php echo $pagination['to']; ?> de <?php echo $pagination['total']; ?> usuarios
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

<!-- User Detail Modal -->
<div class="modal" id="userDetailModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; animation: fadeIn 0.2s ease;">
    <div class="modal-dialog" style="max-width: 800px; margin: auto; animation: slideUp 0.3s ease;">
        <div class="modal-content" style="background: var(--bg-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); border: none; overflow: hidden;">
            <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                <h5 id="userDetailLabel" style="margin: 0; color: var(--primary);">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer;">&times;</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem; max-height: 70vh; overflow-y: auto;">
                <div id="user-detail-content">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple Modal Implementation
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('userDetailModal');
    const closeBtn = modal?.querySelector('.btn-close');
    const cancelBtn = modal?.querySelector('[data-bs-dismiss="modal"]');
    
    const openModal = () => {
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        document.body.style.overflow = 'hidden';
    };
    
    const closeModal = () => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    };
    
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });
    
    // View user details
    document.querySelectorAll('a[href*="/admin/usuarios/"]').forEach(link => {
        // Skip edit links
        if (link.href.includes('/editar/')) return;
        
        link.addEventListener('click', async function(e) {
            e.preventDefault();
            const url = this.href;
            
            // Show loading
            document.getElementById('user-detail-content').innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; padding: 3rem;">
                    <div class="spinner" style="width: 32px; height: 32px; border: 3px solid var(--border); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                </div>
            `;
            
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                document.getElementById('user-detail-content').innerHTML = html;
                openModal();
            } catch (error) {
                document.getElementById('user-detail-content').innerHTML = '<div class="alert alert-error">Error al cargar detalles</div>';
            }
        });
    });
    
    // Delete user
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const name = this.dataset.name;
            const email = this.dataset.email;
            
            if (!confirm('¿Eliminar al usuario "' + name + '" (' + email + ')?\n\nEsta acción eliminará también sus pedidos y datos asociados. No se puede deshacer.')) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo AppConfig::url('/admin/usuarios/eliminar/'); ?>' + id;
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '<?php echo htmlspecialchars($csrf_token); ?>';
            form.appendChild(token);
            
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Bulk actions and row selection
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }
    
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.row-checkbox:checked').length === rowCheckboxes.length;
                selectAll.indeterminate = document.querySelectorAll('.row-checkbox:checked').length > 0 && document.querySelectorAll('.row-checkbox:checked').length < rowCheckboxes.length;
            }
        });
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    animation: fadeIn 0.2s ease;
}

.modal-dialog {
    max-width: 800px;
    margin: auto;
    animation: slideUp 0.3s ease;
}

.modal-content {
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--border);
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-muted);
    cursor: pointer;
    padding: 0.25rem;
    line-height: 1;
}

.btn-close:hover {
    color: var(--text-primary);
}

.data-table input[type="checkbox"] {
    width: 1.125rem;
    height: 1.125rem;
    accent-color: var(--primary);
    cursor: pointer;
}

.data-table tr:hover {
    background: var(--bg-light);
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
}
</style>