<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php 
$product = $data['product'];
$categories = $data['categories'];
?>

<div class="admin-header">
    <h1>Editar Producto: <?php echo htmlspecialchars($product['name']); ?></h1>
    <div style="display: flex; gap: 0.5rem;">
        <a href="<?php echo AppConfig::url('/producto/' . $product['slug']); ?>" class="btn btn-secondary" target="_blank">
            <i class="bi bi-eye"></i> Ver en Tienda
        </a>
        <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="admin-form" style="max-width: 900px;">
    <form method="POST" action="<?php echo AppConfig::url('/admin/productos/actualizar/' . $product['id']); ?>" enctype="multipart/form-data" data-validate>
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Current Image Preview -->
        <?php if ($product['image']): ?>
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: var(--bg-light); border-radius: var(--radius);">
                <strong style="color: var(--text-secondary); font-size: 0.875rem;">Imagen Actual:</strong><br>
                <img src="<?php echo AppConfig::asset('images/products/' . basename($product['image'])); ?>" alt="" style="max-width: 200px; max-height: 200px; border-radius: var(--radius); margin-top: 0.5rem; border: 1px solid var(--border);">
            </div>
        <?php endif; ?>
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="name">Nombre <span aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name']); ?>" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="slug">Slug (URL amigable) <span aria-hidden="true">*</span></label>
                <input type="text" id="slug" name="slug" class="form-control" required value="<?php echo htmlspecialchars($product['slug']); ?>" autocomplete="off">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="category_id">Categoría <span aria-hidden="true">*</span></label>
            <select id="category_id" name="category_id" class="form-control select-wrapper" required>
                <option value="">Seleccionar categoría</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="short_description">Descripción Corta</label>
            <input type="text" id="short_description" name="short_description" class="form-control" value="<?php echo htmlspecialchars($product['short_description'] ?? ''); ?>" maxlength="500">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="description">Descripción Completa</label>
            <textarea id="description" name="description" class="form-control" rows="6"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="price">Precio <span aria-hidden="true">*</span></label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">$</span>
                    <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required value="<?php echo $product['price']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="sale_price">Precio Oferta</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted);">$</span>
                    <input type="number" id="sale_price" name="sale_price" class="form-control" step="0.01" min="0" value="<?php echo $product['sale_price'] ?? ''; ?>" placeholder="Opcional">
                </div>
                <small class="form-hint">Debe ser menor al precio normal para activarse</small>
            </div>
        </div>
        
        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="sku">SKU / Referencia</label>
                <input type="text" id="sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="stock">Stock <span aria-hidden="true">*</span></label>
                <input type="number" id="stock" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" min="0" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="image">Cambiar Imagen Principal</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <small class="form-hint">Deja vacío para mantener la imagen actual. Formatos: JPG, PNG, WebP. Máx. 5MB.</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Añadir a Galería</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            <small class="form-hint">Imágenes adicionales para la galería. Máx. 5 archivos, 5MB cada uno.</small>
        </div>
        
        <div class="form-group" style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: center;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_active" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                <span>Producto Activo</span>
            </label>
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_featured" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                <span>Destacado en Inicio</span>
            </label>
        </div>
        
        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save"></i> Actualizar Producto
            </button>
            <a href="<?php echo AppConfig::url('/admin/productos'); ?>" class="btn btn-secondary btn-lg">
                <i class="bi bi-x"></i> Cancelar
            </a>
        </div>
    </form>
</div>