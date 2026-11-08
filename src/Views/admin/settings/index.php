<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php $settings = $data['settings']; ?>

<div class="admin-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0;">Configuración</h1>
        <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Configura los aspectos generales de tu tienda</p>
    </div>
</div>

<!-- Settings Tabs -->
<div class="settings-tabs" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;">
    <nav class="tabs-nav" style="display: flex; border-bottom: 1px solid var(--border); background: var(--bg-light); overflow-x: auto;">
        <button class="tab-btn active" data-tab="store" style="padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 600; color: var(--primary); cursor: pointer; transition: var(--transition-fast); white-space: nowrap;"><i class="bi bi-shop"></i> Tienda</button>
        <button class="tab-btn" data-tab="shipping" style="padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition-fast); white-space: nowrap;"><i class="bi bi-truck"></i> Envíos e Impuestos</button>
        <button class="tab-btn" data-tab="email" style="padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition-fast); white-space: nowrap;"><i class="bi bi-envelope"></i> Email (SMTP)</button>
        <button class="tab-btn" data-tab="security" style="padding: 1rem 1.5rem; border: none; background: transparent; font-weight: 500; color: var(--text-secondary); cursor: pointer; transition: var(--transition-fast); white-space: nowrap;"><i class="bi bi-shield"></i> Seguridad</button>
    </nav>
    
    <form method="POST" action="<?php echo AppConfig::url('/admin/configuracion/guardar'); ?>" id="settings-form">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Store Info Tab -->
        <div class="tab-panel active" id="tab-store" style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-shop"></i> Información de la Tienda</h3>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="store_name">Nombre de la Tienda <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" id="store_name" name="store_name" class="form-control" required value="<?php echo htmlspecialchars($settings['store_name'] ?? 'Nicastore'); ?>" aria-describedby="store_name-error">
                    <div class="form-error" id="store_name-error" style="display: none;"></div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="store_email">Email de Contacto <span class="required" aria-hidden="true">*</span></label>
                    <input type="email" id="store_email" name="store_email" class="form-control" required value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>" aria-describedby="store_email-error">
                    <div class="form-error" id="store_email-error" style="display: none;"></div>
                </div>
            </div>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="store_phone">Teléfono</label>
                    <input type="tel" id="store_phone" name="store_phone" class="form-control" value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>" placeholder="+505 XXXX-XXXX">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="currency">Moneda <span class="required" aria-hidden="true">*</span></label>
                    <select id="currency" name="currency" class="form-control select-wrapper" required aria-describedby="currency-error">
                        <option value="USD" <?php echo ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                        <option value="NIO" <?php echo ($settings['currency'] ?? '') === 'NIO' ? 'selected' : ''; ?>>Córdobas (C$)</option>
                        <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                    </select>
                    <div class="form-error" id="currency-error" style="display: none;"></div>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="store_address">Dirección</label>
                <textarea id="store_address" name="store_address" class="form-control" rows="3"><?php echo htmlspecialchars($settings['store_address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="store_logo">Logo de la Tienda (opcional)</label>
                <input type="file" id="store_logo" name="store_logo" class="form-control" accept="image/*">
                <small class="form-hint">Formatos: JPG, PNG, WebP. Máx. 2MB. Recomendado: 200x60px</small>
                <?php if (!empty($settings['store_logo'])): ?>
                    <div style="margin-top: 0.75rem;">
                        <img src="<?php echo AppConfig::asset('images/' . $settings['store_logo']); ?>" alt="Logo actual" style="max-height: 60px; border-radius: var(--radius); border: 1px solid var(--border);">
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Shipping & Tax Tab -->
        <div class="tab-panel" id="tab-shipping" style="padding: 2rem; display: none;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-truck"></i> Envíos e Impuestos</h3>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="tax_rate">Impuesto (%) <span class="required" aria-hidden="true">*</span></label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="number" id="tax_rate" name="tax_rate" class="form-control" step="0.01" min="0" max="100" required value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '15'); ?>" aria-describedby="tax_rate-error">
                        <span style="color: var(--text-muted); font-weight: 500;">%</span>
                    </div>
                    <div class="form-error" id="tax_rate-error" style="display: none;"></div>
                    <small class="form-hint">Porcentaje de impuesto aplicado a los pedidos (ej: 15 para 15%)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="shipping_cost">Costo de Envío Estándar <span class="required" aria-hidden="true">*</span></label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="color: var(--text-muted); font-weight: 500;">$</span>
                        <input type="number" id="shipping_cost" name="shipping_cost" class="form-control" step="0.01" min="0" required value="<?php echo htmlspecialchars($settings['shipping_cost'] ?? '5.00'); ?>" aria-describedby="shipping_cost-error">
                    </div>
                    <div class="form-error" id="shipping_cost-error" style="display: none;"></div>
                    <small class="form-hint">Costo base de envío para pedidos por debajo del umbral gratis</small>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="free_shipping_threshold">Envío Gratis a partir de <span class="required" aria-hidden="true">*</span></label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="color: var(--text-muted); font-weight: 500;">$</span>
                    <input type="number" id="free_shipping_threshold" name="free_shipping_threshold" class="form-control" step="0.01" min="0" required value="<?php echo htmlspecialchars($settings['free_shipping_threshold'] ?? '50'); ?>" aria-describedby="free_shipping_threshold-error">
                </div>
                <div class="form-error" id="free_shipping_threshold-error" style="display: none;"></div>
                <small class="form-hint">Pedidos iguales o superiores a este monto tendrán envío gratis. Usa 0 para desactivar.</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="free_shipping_enabled">Envío Gratis <span class="required" aria-hidden="true">*</span></label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="free_shipping_enabled" id="free_shipping_enabled" <?php echo ($settings['free_shipping_enabled'] ?? '1') ? 'checked' : ''; ?> value="1">
                        <span>Activar envío gratis</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Email Tab -->
        <div class="tab-panel" id="tab-email" style="padding: 2rem; display: none;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-envelope"></i> Configuración de Email (SMTP)</h3>
            
            <div class="alert" style="background: #DBEAFE; border: 1px solid #93C5FD; border-radius: var(--radius); padding: 1rem; margin-bottom: 1.5rem; color: #1E40AF;">
                <i class="bi bi-info-circle"></i> <strong>Nota:</strong> La configuración SMTP es opcional. Si no se configura, se usará la función mail() de PHP (menos confiable).
            </div>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="smtp_host">Servidor SMTP</label>
                    <input type="text" id="smtp_host" name="smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="smtp_port">Puerto</label>
                    <input type="number" id="smtp_port" name="smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" placeholder="587" min="1" max="65535">
                </div>
            </div>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="smtp_user">Usuario</label>
                    <input type="text" id="smtp_user" name="smtp_user" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="smtp_pass">Contraseña</label>
                    <input type="password" id="smtp_pass" name="smtp_pass" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_pass'] ?? ''); ?>" autocomplete="current-password">
                    <small class="form-hint">Deja en blanco para no cambiar la contraseña actual</small>
                </div>
            </div>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="smtp_from_email">Email Remitente</label>
                    <input type="email" id="smtp_from_email" name="smtp_from_email" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_from_email'] ?? ''); ?>" placeholder="noreply@nicastore.com.ni">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="smtp_from_name">Nombre Remitente</label>
                    <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_from_name'] ?? 'Nicastore'); ?>">
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="smtp_encryption">Cifrado</label>
                <select id="smtp_encryption" name="smtp_encryption" class="form-control select-wrapper">
                    <option value="tls" <?php echo ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS (Recomendado)</option>
                    <option value="ssl" <?php echo ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                    <option value="none" <?php echo ($settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>Sin cifrado (No recomendado)</option>
                </select>
            </div>
            
            <button type="button" class="btn btn-secondary" id="test-email-btn">
                <i class="bi bi-send"></i> Enviar Email de Prueba
            </button>
        </div>
        
        <!-- Security Tab -->
        <div class="tab-panel" id="tab-security" style="padding: 2rem; display: none;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-shield"></i> Seguridad</h3>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="session_lifetime">Duración de Sesión (minutos)</label>
                    <input type="number" id="session_lifetime" name="session_lifetime" class="form-control" value="<?php echo htmlspecialchars($settings['session_lifetime'] ?? '120'); ?>" min="10" max="10080" aria-describedby="session_lifetime-error">
                    <div class="form-error" id="session_lifetime-error" style="display: none;"></div>
                    <small class="form-hint">Tiempo de inactividad antes de cerrar sesión (10-10080 min = 1 semana)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_min_length">Longitud Mínima de Contraseña</label>
                    <input type="number" id="password_min_length" name="password_min_length" class="form-control" value="<?php echo htmlspecialchars($settings['password_min_length'] ?? '10'); ?>" min="8" max="32" aria-describedby="password_min_length-error">
                    <div class="form-error" id="password_min_length-error" style="display: none;"></div>
                    <small class="form-hint">Mínimo recomendado: 10 caracteres</small>
                </div>
            </div>
            
            <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="login_max_attempts">Máx. Intentos de Login</label>
                    <input type="number" id="login_max_attempts" name="login_max_attempts" class="form-control" value="<?php echo htmlspecialchars($settings['login_max_attempts'] ?? '5'); ?>" min="3" max="20" aria-describedby="login_max_attempts-error">
                    <div class="form-error" id="login_max_attempts-error" style="display: none;"></div>
                    <small class="form-hint">Intentos fallidos antes de bloqueo temporal</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="lockout_duration">Duración Bloqueo (minutos)</label>
                    <input type="number" id="lockout_duration" name="lockout_duration" class="form-control" value="<?php echo htmlspecialchars($settings['lockout_duration'] ?? '15'); ?>" min="5" max="1440" aria-describedby="lockout_duration-error">
                    <div class="form-error" id="lockout_duration-error" style="display: none;"></div>
                    <small class="form-hint">Tiempo de espera tras exceder intentos</small>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="require_2fa">Autenticación de Dos Factores (2FA)</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="require_2fa" id="require_2fa" <?php echo ($settings['require_2fa'] ?? '0') ? 'checked' : ''; ?> value="1">
                        <span>Requerir 2FA para administradores</span>
                    </label>
                </div>
                <small class="form-hint">Los administradores deberán configurar 2FA en su perfil</small>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="maintenance_mode">Modo Mantenimiento</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500;">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo ($settings['maintenance_mode'] ?? '0') ? 'checked' : ''; ?> value="1">
                        <span>Activar modo mantenimiento</span>
                    </label>
                </div>
                <small class="form-hint">Solo administradores podrán acceder a la tienda</small>
            </div>
        </div>
        
        <!-- Save Button (always visible) -->
        <div style="padding: 1.5rem 2rem; border-top: 1px solid var(--border); background: var(--bg-light); display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="button" class="btn btn-secondary" id="reset-form">
                <i class="bi bi-arrow-counterclockwise"></i> Restablecer
            </button>
            <button type="submit" class="btn btn-primary btn-lg" id="save-settings-btn">
                <i class="bi bi-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab navigation
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('active');
                b.style.color = 'var(--text-secondary)';
                b.style.fontWeight = '500';
            });
            this.classList.add('active');
            this.style.color = 'var(--primary)';
            this.style.fontWeight = '600';
            
            document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
            document.getElementById('tab-' + tab).style.display = 'block';
        });
    });
    
    // Form validation
    const form = document.getElementById('settings-form');
    const saveBtn = document.getElementById('save-settings-btn');
    const resetBtn = document.getElementById('reset-form');
    
    function validateField(input) {
        const errorId = input.id + '-error';
        const errorEl = document.getElementById(errorId);
        let isValid = true;
        
        // Clear previous
        input.style.borderColor = '';
        if (errorEl) errorEl.style.display = 'none';
        
        if (input.required && !input.value.trim()) {
            isValid = false;
            input.style.borderColor = 'var(--error)';
            if (errorEl) {
                errorEl.textContent = 'Este campo es requerido';
                errorEl.style.display = 'flex';
            }
        }
        
        // Type-specific validation
        if (input.type === 'email' && input.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
            isValid = false;
            input.style.borderColor = 'var(--error)';
            if (errorEl) {
                errorEl.textContent = 'Email inválido';
                errorEl.style.display = 'flex';
            }
        }
        
        if (input.type === 'number') {
            const min = parseFloat(input.min);
            const max = parseFloat(input.max);
            const val = parseFloat(input.value);
            if (!isNaN(min) && val < min) {
                isValid = false;
                input.style.borderColor = 'var(--error)';
                if (errorEl) {
                    errorEl.textContent = 'Mínimo: ' + min;
                    errorEl.style.display = 'flex';
                }
            }
            if (!isNaN(max) && val > max) {
                isValid = false;
                input.style.borderColor = 'var(--error)';
                if (errorEl) {
                    errorEl.textContent = 'Máximo: ' + max;
                    errorEl.style.display = 'flex';
                }
            }
        }
        
        return isValid;
    }
    
    // Real-time validation
    form.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('blur', () => validateField(input));
        input.addEventListener('input', () => {
            if (input.style.borderColor === 'var(--error)' || input.style.borderColor === 'rgb(239, 68, 68)') {
                validateField(input);
            }
        });
    });
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        let isValid = true;
        form.querySelectorAll('.form-control[required]').forEach(input => {
            if (!validateField(input)) isValid = false;
        });
        
        if (!isValid) {
            const firstError = form.querySelector('.form-control[style*="var(--error)"]');
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }
        
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Guardando...';
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                showToast('Configuración guardada correctamente', 'success');
            } else {
                showToast('Error al guardar la configuración', 'error');
            }
        } catch (error) {
            showToast('Error de conexión', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-save"></i> Guardar Cambios';
        }
    });
    
    // Reset form
    resetBtn.addEventListener('click', function() {
        if (confirm('¿Restablecer el formulario a los valores actuales guardados?')) {
            location.reload();
        }
    });
    
    // Test email
    document.getElementById('test-email-btn')?.addEventListener('click', async function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        const smtpData = {
            host: document.getElementById('smtp_host').value,
            port: document.getElementById('smtp_port').value,
            user: document.getElementById('smtp_user').value,
            pass: document.getElementById('smtp_pass').value,
            from_email: document.getElementById('smtp_from_email').value,
            from_name: document.getElementById('smtp_from_name').value,
            encryption: document.getElementById('smtp_encryption').value
        };
        
        if (!smtpData.host || !smtpData.user || !smtpData.pass) {
            showToast('Completa la configuración SMTP primero', 'error');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Enviando...';
        
        try {
            const response = await fetch('<?php echo AppConfig::url('/admin/configuracion/test-email'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo htmlspecialchars($csrf_token); ?>'
                },
                body: JSON.stringify(smtpData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Email de prueba enviado correctamente', 'success');
            } else {
                showToast(result.message || 'Error al enviar email', 'error');
            }
        } catch (error) {
            showToast('Error de conexión', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Currency change warning
    const currencySelect = document.getElementById('currency');
    let originalCurrency = currencySelect.value;
    
    currencySelect.addEventListener('change', function() {
        if (this.value !== originalCurrency) {
            showToast('Cambiar la moneda afectará precios existentes. Verifica los productos después.', 'warning');
        }
    });
    
    function showToast(message, type = 'info') {
        document.querySelectorAll('.toast').forEach(t => t.remove());
        
        const toast = document.createElement('div');
        toast.className = `toast flash flash-${type}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 9999;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideInRight 0.3s ease;
            max-width: 350px;
        `;
        
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        
        toast.innerHTML = `<i class="bi ${icons[type] || icons.info}"></i> <span>${message}</span>`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeIn 0.3s ease reverse';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
});
</script>

<style>
@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100%); }
    to { opacity: 1; transform: translateX(0); }
}

.settings-tabs .tab-btn:hover:not(.active) {
    color: var(--primary);
    background: var(--bg-white);
}

.tab-panel {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.alert i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}
</style>