<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $categories = $data['categories']; ?>

<div class="admin-header">
    <h1>Nuevo Producto</h1>
    <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="admin-form" style="max-width: 900px;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/productos/guardar'); ?>" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required autocomplete="off" placeholder="Nombre del producto">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="slug">Slug (URL amigable) <span aria-hidden="true">*</span></label>
                <input type="text" id="slug" name="slug" class="form-control" required placeholder="ej: laptop-gaming-rtx-4080" autocomplete="off">
                <small class="form-hint">Solo letras, números, guiones. Se genera automáticamente si se deja vacío.</small>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="category_id">Categoría <span aria-hidden="true">*</span></label>
            <select id="category_id" name="category_id" class="form-control select-wrapper" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="short_description">Descripción Corta</label>
            <input type="text" id="short_description" name="short_description" class="form-control" placeholder="Descripción breve para listados (máx. 500 caracteres)" maxlength="500">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="description">Descripción Completa</label>
            <textarea id="description" name="description" class="form-control" rows="6" placeholder="Descripción detallada del producto..."></textarea>
        </div>
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="price">Precio <span aria-hidden="true">*</span></label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">$</span>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required placeholder="0.00">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sale_price">Precio Oferta</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">$</span>
                    <input type="number" id="sale_price" name="sale_price" class="form-control" step="0.01" min="0" placeholder="Precio con descuento (opcional)">
                </div>
                <small class="form-hint">Debe ser menor al precio normal para activarse</small>
            </div>
        </div>
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="sku">SKU / Referencia</label>
                <input type="text" id="sku" name="sku" class="form-control" placeholder="Código único del producto">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="stock">Stock Inicial <span aria-hidden="true">*</span></label>
                <input type="number" id="stock" name="stock" class="form-control" value="0" min="0" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="image">Imagen Principal</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small class="form-hint">Formatos: JPG, PNG, WebP. Máx. 5MB. Recomendado: 800x800px</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Galería de Imágenes (adicionales)</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            <small class="form-hint">Múltiples imágenes para la galería del producto. Máx. 5 archivos, 5MB cada uno.</small>
        </div>
        
        <div class="form-group" style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: center;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_active" checked>
                <span>Producto Activo</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_featured">
                <span>Destacado en Inicio</span>
            </label>
        </div>
        
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Guardar Producto
            </button>
            <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-secondary btn-lg">
                <i class="bi bi-x"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from name
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>