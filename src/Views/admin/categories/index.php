<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $categories = $data['categories']; ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Gestión de Categorías</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Organiza tu catálogo por categorías</p>
    </div>
    <button type="button" class="btn btn-primary" id="new-category-btn">
        <i class="bi bi-plus-lg"></i> Nueva Categoría
    </button>
</div>

<!-- Categories Grid -->
<div class="data-table" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 2rem;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--bg-light); border-bottom: 2px solid var(--border);">
                    <th scope="col" style="width: 60px; padding: 1rem; text-align: center;">
                        <input type="checkbox" id="select-all" aria-label="Seleccionar todos">
                    </th>
                    <th scope="col" style="padding: 1rem;">Imagen</th>
                    <th scope="col" style="padding: 1rem;">Nombre</th>
                    <th scope="col" style="width: 150px; padding: 1rem;">Slug</th>
                    <th scope="col" style="padding: 1rem;">Descripción</th>
                    <th scope="col" style="width: 100px; padding: 1rem;">Estado</th>
                    <th scope="col" style="width: 100px; padding: 1rem;">Productos</th>
                    <th scope="col" style="width: 150px; padding: 1rem;"><span class="visually-hidden">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <?php
                        $productCount = (new \Nicastore\Models\Product())->query("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$category['id']])->fetch()['count'];
                        $image = $category['image'] ?? null;
                    ?>
                    <tr data-id="<?php echo $category['id']; ?>" style="border-bottom: 1px solid var(--border); transition: var(--transition-fast);">
                        <td style="padding: 1rem; text-align: center;">
                            <input type="checkbox" class="row-checkbox" name="ids[]" value="<?php echo $category['id']; ?>" aria-label="Seleccionar categoría <?php echo htmlspecialchars($category['name']); ?>">
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <?php if ($image): ?>
                                <img src="<?php echo AppConfig::asset('images/categories/' . basename($image)); ?>" alt="" style="width: 50px; height: 50px; border-radius: var(--radius); object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: var(--radius); background: var(--bg-light); display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--text-muted);">
                                    <i class="bi bi-tag" style="font-size: 1.5rem;"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($category['name']); ?></div>
                        </td>
                        <td style="padding: 1rem;"><code style="background: var(--bg-light); padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); font-size: 0.8125rem;"><?php echo htmlspecialchars($category['slug']); ?></code></td>
                        <td style="padding: 1rem; color: var(--text-secondary); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($category['description'] ?? 'Sin descripción'); ?>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-<?php echo $category['is_active'] ? 'success' : 'danger'; ?>" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                <?php echo $category['is_active'] ? 'Activa' : 'Inactiva'; ?>
                            </span>
                        </td>
                        <td style="padding: 1rem;">
                            <span class="badge badge-info" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;"><?php echo $productCount; ?></span>
                        </td>
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                <a href="<?php echo AppConfig::url('/admin/categorias/editar/' . $category['id']); ?>" class="btn btn-sm btn-primary" title="Editar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $category['id']; ?>" data-name="<?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>" data-count="<?php echo $productCount; ?>" title="Eliminar" style="padding: 0.375rem 0.75rem; font-size: 0.8125rem;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (empty($categories)): ?>
        <div style="padding: 3rem; text-align: center;">
            <i class="bi bi-tags" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem; display: block;"></i>
            <h3>No hay categorías</h3>
            <p style="color: var(--text-muted); margin: 1rem 0 2rem;">Comienza creando tu primera categoría</p>
            <button type="button" class="btn btn-primary" id="first-category-btn"><i class="bi bi-plus-lg"></i> Crear Categoría</button>
        </div>
    <?php endif; ?>
</div>

<!-- Category Form Modal -->
<div class="modal" id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; animation: fadeIn 0.2s ease;">
    <div class="modal-dialog" style="max-width: 600px; margin: auto; animation: slideUp 0.3s ease;">
        <div class="modal-content" style="background: var(--bg-white); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); border: none; overflow: hidden;">
            <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                <h5 id="categoryModalLabel" style="margin: 0; color: var(--primary);">Nueva Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer;">&times;</button>
            </div>
            <form method="POST" action="" id="category-form" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" id="category-id" value="">
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="name">Nombre <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required autocomplete="off" aria-describedby="name-error">
                        <div class="form-error" id="name-error" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="slug">Slug <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="slug" name="slug" class="form-control" required placeholder="ej: laptops-gaming" autocomplete="off" aria-describedby="slug-error">
                        <small class="form-hint">Solo letras, números, guiones. Se usa en la URL.</small>
                        <div class="form-error" id="slug-error" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="description">Descripción</label>
                        <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="image">Imagen (opcional)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <small class="form-hint">Formatos: JPG, PNG, WebP. Máx. 5MB.</small>
                        <div id="image-preview" style="margin-top: 0.75rem; display: none;">
                            <img id="preview-img" src="" alt="Vista previa" style="max-height: 100px; border-radius: var(--radius);">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500; margin: 0;">
                            <input type="checkbox" name="is_active" id="is_active" checked>
                            <span>Categoría Activa</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Simple Modal Implementation
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('category-form');
    const newCategoryBtn = document.getElementById('new-category-btn');
    const firstCategoryBtn = document.getElementById('first-category-btn');
    const closeBtn = modal?.querySelector('.btn-close');
    const cancelBtn = modal?.querySelector('[data-bs-dismiss="modal"]');
    
    let editingId = null;
    
    const openModal = (category = null) => {
        if (category) {
            editingId = category.id;
            document.getElementById('categoryModalLabel').textContent = 'Editar Categoría';
            document.getElementById('name').value = category.name;
            document.getElementById('slug').value = category.slug;
            document.getElementById('description').value = category.description || '';
            document.getElementById('is_active').checked = !!category.is_active;
            document.getElementById('category-id').value = category.id;
            form.action = '<?php echo AppConfig::url('/admin/categorias/actualizar/'); ?>' + category.id;
            
            // Image preview
            if (category.image) {
                document.getElementById('preview-img').src = '<?php echo AppConfig::asset('images/categories/'); ?>' + category.image;
                document.getElementById('image-preview').style.display = 'block';
            } else {
                document.getElementById('image-preview').style.display = 'none';
            }
        } else {
            editingId = null;
            document.getElementById('categoryModalLabel').textContent = 'Nueva Categoría';
            form.reset();
            document.getElementById('category-id').value = '';
            form.action = '<?php echo AppConfig::url('/admin/categorias/guardar'); ?>';
            document.getElementById('is_active').checked = true;
            document.getElementById('image-preview').style.display = 'none';
            // Clear validation
            document.querySelectorAll('.form-error').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            document.querySelectorAll('.form-control').forEach(el => {
                el.style.borderColor = '';
            });
        }
        
        modal.style.display = 'flex';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        document.body.style.overflow = 'hidden';
        
        // Focus trap
        const focusable = modal.querySelectorAll('button, select, input, textarea, a');
        if (focusable.length) focusable[0].focus();
    };
    
    const closeModal = () => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        editingId = null;
    };
    
    const validateForm = () => {
        let isValid = true;
        const name = document.getElementById('name');
        const slug = document.getElementById('slug');
        
        // Clear previous errors
        [name, slug].forEach(el => {
            el.style.borderColor = '';
            document.getElementById(el.id + '-error').style.display = 'none';
        });
        
        if (!name.value.trim()) {
            name.style.borderColor = 'var(--error)';
            document.getElementById('name-error').textContent = 'El nombre es requerido';
            document.getElementById('name-error').style.display = 'flex';
            isValid = false;
        }
        
        if (!slug.value.trim()) {
            slug.style.borderColor = 'var(--error)';
            document.getElementById('slug-error').textContent = 'El slug es requerido';
            document.getElementById('slug-error').style.display = 'flex';
            isValid = false;
        } else if (!/^[a-z0-9-]+$/.test(slug.value)) {
            slug.style.borderColor = 'var(--error)';
            document.getElementById('slug-error').textContent = 'Solo letras minúsculas, números y guiones';
            document.getElementById('slug-error').style.display = 'flex';
            isValid = false;
        }
        
        return isValid;
    };
    
    // Open modal for new category
    newCategoryBtn?.addEventListener('click', () => openModal());
    firstCategoryBtn?.addEventListener('click', () => openModal());
    
    // Open modal for editing
    document.querySelectorAll('tbody tr[data-id]').forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.type === 'checkbox' || e.target.closest('button, a')) return;
            const id = this.dataset.id;
            // Fetch category data via AJAX or use data attributes
            // For now, we'll redirect to edit page
            window.location.href = '<?php echo AppConfig::url('/admin/categorias/editar/'); ?>' + id;
        });
        row.style.cursor = 'pointer';
    });
    
    // Close modal
    const doClose = () => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        editingId = null;
    };
    
    closeBtn?.addEventListener('click', doClose);
    cancelBtn?.addEventListener('click', doClose);
    
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) doClose();
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            doClose();
        }
    });
    
    // Form submission
    form?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            const firstError = form.querySelector('.form-control[style*="var(--error)"]');
            if (firstError) firstError.focus();
            return;
        }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Guardando...';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                closeModal();
                // Reload page to show updated list
                location.reload();
            } else {
                const text = await response.text();
                // Try to parse error
                alert('Error al guardar la categoría');
            }
        } catch (error) {
            alert('Error de conexión');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    // Delete category
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const name = this.dataset.name;
            const count = parseInt(this.dataset.count) || 0;
            
            let message = '¿Eliminar la categoría "' + name + '"?';
            if (count > 0) {
                message += ' Tiene ' + count + ' producto(s) asociado(s). Se eliminarán también.';
            }
            message += ' Esta acción no se puede deshacer.';
            
            if (!confirm(message)) return;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo AppConfig::url('/admin/categorias/eliminar/'); ?>' + id;
            
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = '<?php echo htmlspecialchars($csrf_token); ?>';
            form.appendChild(token);
            
            document.body.appendChild(form);
            form.submit();
        });
    });
    
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    if (nameInput && slugInput) {
        let slugGenerated = false;
        
        nameInput.addEventListener('input', function() {
            if (!slugGenerated || slugInput.dataset.autoGenerated === 'true') {
                const slug = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });
        
        slugInput.addEventListener('input', function() {
            if (this.value) {
                this.dataset.autoGenerated = 'false';
                slugGenerated = true;
            }
        });
    }
    
    // Image preview
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    imageInput?.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            preview.style.display = 'none';
        }
    });
    
    // Bulk actions and row selection (same as products)
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }
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

.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    animation: fadeIn 0.2s ease;
}

.modal-dialog {
    max-width: 600px;
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