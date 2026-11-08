<?php
/** @var array $data */
use Nicastore\Config\AppConfig;
?>

<?php
    $items = $data['items'];
    $subtotal = $data['subtotal'];
    $shipping = $data['shipping'];
    $tax = $data['tax'];
    $discount = $data['discount'];
    $total = $data['total'];
    $user = $data['user'];
    $step = $data['step'] ?? 1;
    $errors = $data['errors'] ?? [];
    $formData = $data['formData'] ?? [];
    
    // Nicaraguan departments and major cities
    $departments = [
        'Managua' => ['Managua', 'Ciudad Sandino', 'San Francisco Libre', 'San Rafael del Sur', 'Ticuantepe', 'Tipitapa', 'Villa El Carmen'],
        'León' => ['León', 'El Jicaral', 'El Sauce', 'La Paz Centro', 'Larreynaga', 'Nagarote', 'Quezalguaque', 'San José de Achuapa', 'Santa Rosa del Peñón', 'Telica'],
        'Granada' => ['Granada', 'Diriá', 'Diriomo', 'Nandaime'],
        'Estelí' => ['Estelí', 'Condor', 'La Concordia', 'La Trinidad', 'Pueblo Nuevo', 'San Juan de Limay', 'San Nicolás'],
        'Matagalpa' => ['Matagalpa', 'Ciudad Darío', 'El Tuma-La Dalia', 'Esquipulas', 'Matiguás', 'Muy Muy', 'Río Blanco', 'San Dionisio', 'San Isidro', 'San Ramón', 'Sébaco', 'Terrabona'],
        'Jinotega' => ['Jinotega', 'El Cuá', 'La Concordia', 'San José de Bocay', 'San Rafael del Norte', 'San Sebastián de Yalí', 'Santa María de Pantasma', 'Wiwilí de Jinotega'],
        'Chinandega' => ['Chinandega', 'Chichigalpa', 'Corinto', 'El Realejo', 'El Viejo', 'Posoltega', 'San Francisco del Norte', 'San Pedro del Norte', 'Santo Tomás del Norte', 'Somoto', 'Villa Nueva'],
        'Masaya' => ['Masaya', 'Catarina', 'La Concepción', 'Masatepe', 'Nandasmo', 'Nindirí', 'Niquinohomo', 'San Juan de Oriente', 'Tisma'],
        'Carazo' => ['Jinotepe', 'Dolores', 'El Rosario', 'La Conquista', 'La Paz de Carazo', 'San Marcos', 'Santa Teresa', 'Diriamba'],
        'Rivas' => ['Rivas', 'Altagracia', 'Belén', 'Buenos Aires', 'Cárdenas', 'Moyogalpa', 'Potosí', 'San Jorge', 'San Juan del Sur', 'Tola'],
        'Boaco' => ['Boaco', 'Camoapa', 'San Lorenzo', 'San José de los Remates', 'Teustepe'],
        'Chontales' => ['Juigalpa', 'Acoyapa', 'Comalapa', 'El Coral', 'La Libertad', 'San Francisco de Cuapa', 'San Pedro de Lóvago', 'Santo Domingo', 'Santo Tomás', 'Villa Sandino'],
        'Río San Juan' => ['San Carlos', 'El Almendro', 'El Castillo', 'Morrito', 'San Juan de Nicaragua'],
        'Madriz' => ['Somoto', 'Las Sabanas', 'Palacagüina', 'San José de Cusmapa', 'San Juan de Río Coco', 'San Lucas', 'Telpaneca', 'Totogalpa', 'Yalagüina'],
        'Nueva Segovia' => ['Ocotal', 'Ciudad Antigua', 'Dipilto', 'El Jícaro', 'Macuelizo', 'Mozonte', 'Murra', 'Quilalí', 'San Fernando', 'Santa María', 'Wiwilí de Nueva Segovia'],
    ];
    
    $allCities = [];
    foreach ($departments as $dept => $cities) {
        $allCities = array_merge($allCities, $cities);
    }
?>

<div class="container checkout-page" style="padding: 3rem 0;">
    <nav class="breadcrumb" aria-label="Navegación" style="margin-bottom: 2rem;">
        <a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <a href="<?php echo AppConfig::url('/carrito'); ?>"><i class="bi bi-bag"></i> Carrito</a>
        <i class="bi bi-chevron-right breadcrumb-separator"></i>
        <span aria-current="page">Finalizar Compra</span>
    </nav>
    
    <!-- Progress indicator -->
    <div class="checkout-progress" style="margin-bottom: 2rem;">
        <div class="progress-steps" style="display: flex; justify-content: space-between; position: relative;">
            <div class="progress-line" style="position: absolute; top: 20px; left: 10%; right: 10%; height: 3px; background: var(--border); z-index: 0;">
                <div class="progress-fill" style="height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); width: 33%; transition: width 0.3s ease;"></div>
            </div>
            <div class="step <?php echo $step >= 1 ? 'active' : ''; ?>" data-step="1" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $step >= 1 ? 'var(--primary)' : 'var(--bg-white)'; ?>; border: 3px solid <?php echo $step >= 1 ? 'var(--primary)' : 'var(--border)'; ?>; color: <?php echo $step >= 1 ? 'white' : 'var(--text-muted)'; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;">1</div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: <?php echo $step >= 1 ? 'var(--primary)' : 'var(--text-muted)'; ?>">Envío</span>
            </div>
            <div class="step <?php echo $step >= 2 ? 'active' : ''; ?>" data-step="2" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $step >= 2 ? 'var(--primary)' : 'var(--bg-white)'; ?>; border: 3px solid <?php echo $step >= 2 ? 'var(--primary)' : 'var(--border)'; ?>; color: <?php echo $step >= 2 ? 'white' : 'var(--text-muted)'; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;">2</div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: <?php echo $step >= 2 ? 'var(--primary)' : 'var(--text-muted)'; ?>">Pago</span>
            </div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>" data-step="3" style="position: relative; z-index: 1; text-align: center; flex: 1;">
                <div class="step-circle" style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo $step >= 3 ? 'var(--primary)' : 'var(--bg-white)'; ?>; border: 3px solid <?php echo $step >= 3 ? 'var(--primary)' : 'var(--border)'; ?>; color: <?php echo $step >= 3 ? 'white' : 'var(--text-muted)'; ?>; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.5rem; font-weight: 600;">3</div>
                <span style="font-size: 0.8125rem; font-weight: 500; color: <?php echo $step >= 3 ? 'var(--primary)' : 'var(--text-muted)'; ?>">Confirmar</span>
            </div>
        </div>
    </div>
    
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;">Finalizar Compra</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Revisa tu pedido y completa la información de envío</p>
    </header>
    
    <?php if ($step === 1): ?>
        <!-- Step 1: Shipping Information -->
        <form method="POST" action="<?php echo AppConfig::url('/checkout/paso1'); ?>" id="checkout-form" novalidate>
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="checkout-grid">
                <!-- Shipping Form -->
                <div class="fade-in-up">
                    <div class="checkout-form" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm);">
                        <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;"><i class="bi bi-truck"></i> Información de Envío</h3>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="flash flash-error" style="margin-bottom: 1.5rem;">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?php echo implode('<br>', $errors); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label" for="shipping_name">Nombre Completo <span class="required" aria-hidden="true">*</span></label>
                                <input type="text" id="shipping_name" name="shipping_name" class="form-control" required value="<?php echo htmlspecialchars($formData['shipping_name'] ?? $user['name'] ?? ''); ?>" autocomplete="shipping name" aria-describedby="name-error">
                                <div class="form-error" id="name-error" style="display: none;"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="shipping_email">Email <span class="required" aria-hidden="true">*</span></label>
                                <input type="email" id="shipping_email" name="shipping_email" class="form-control" required value="<?php echo htmlspecialchars($formData['shipping_email'] ?? $user['email'] ?? ''); ?>" autocomplete="shipping email" aria-describedby="email-error">
                                <div class="form-error" id="email-error" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="shipping_phone">Teléfono <span class="required" aria-hidden="true">*</span></label>
                            <div class="input-group" style="display: flex;">
                                <span class="input-group-text" style="background: var(--bg-light); border: 2px solid var(--border); border-right: none; border-top-left-radius: var(--radius); border-bottom-left-radius: var(--radius); padding: 0.875rem 1rem; color: var(--text-secondary);">+505</span>
                                <input type="tel" id="shipping_phone" name="shipping_phone" class="form-control" required placeholder="XXXX-XXXX" autocomplete="shipping tel" aria-describedby="phone-error" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" value="<?php echo htmlspecialchars($formData['shipping_phone'] ?? ''); ?>">
                            </div>
                            <div class="form-error" id="phone-error" style="display: none;"></div>
                            <div class="form-hint">Formato: 8888-8888 o 2222-2222</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="shipping_address">Dirección Exacta <span class="required" aria-hidden="true">*</span></label>
                            <input type="text" id="shipping_address" name="shipping_address" class="form-control" required placeholder="Calle, número, casa, punto de referencia" autocomplete="shipping street-address" aria-describedby="address-error" value="<?php echo htmlspecialchars($formData['shipping_address'] ?? ''); ?>">
                            <div class="form-error" id="address-error" style="display: none;"></div>
                            <div class="form-hint">Incluye referencias cercanas para facilitar la entrega</div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label" for="shipping_city">Ciudad <span class="required" aria-hidden="true">*</span></label>
                                <div class="autocomplete-wrapper" style="position: relative;">
                                    <input type="text" id="shipping_city" name="shipping_city" class="form-control" required autocomplete="shipping address-level2" aria-describedby="city-error" list="cities-list" value="<?php echo htmlspecialchars($formData['shipping_city'] ?? ''); ?>">
                                    <datalist id="cities-list">
                                        <?php foreach ($allCities as $city): ?>
                                            <option value="<?php echo htmlspecialchars($city); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                    <div class="autocomplete-suggestions" id="city-suggestions" style="position: absolute; top: 100%; left: 0; right: 0; background: var(--bg-white); border: 1px solid var(--border); border-top: none; border-radius: 0 0 var(--radius) var(--radius); max-height: 200px; overflow-y: auto; z-index: 10; display: none;"></div>
                                </div>
                                <div class="form-error" id="city-error" style="display: none;"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="shipping_state">Departamento</label>
                                <select id="shipping_state" name="shipping_state" class="form-control select-wrapper" autocomplete="shipping address-level1" aria-describedby="state-error">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($departments as $dept => $cities): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>" data-cities='<?php echo json_encode($cities); ?>' <?php echo ($formData['shipping_state'] ?? '') === $dept ? 'selected' : ''; ?>><?php echo htmlspecialchars($dept); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-error" id="state-error" style="display: none;"></div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="shipping_zip">Código Postal <span class="required" aria-hidden="true">*</span></label>
                                <input type="text" id="shipping_zip" name="shipping_zip" class="form-control" required autocomplete="shipping postal-code" aria-describedby="zip-error" maxlength="5" pattern="\d{5}" value="<?php echo htmlspecialchars($formData['shipping_zip'] ?? ''); ?>">
                                <div class="form-error" id="zip-error" style="display: none;"></div>
                                <div class="form-hint">5 dígitos</div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="shipping_country">País</label>
                            <select id="shipping_country" name="shipping_country" class="form-control select-wrapper" autocomplete="shipping country" disabled>
                                <option value="Nicaragua" selected>Nicaragua</option>
                            </select>
                            <input type="hidden" name="shipping_country" value="Nicaragua">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="notes">Notas del Pedido (opcional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Instrucciones de entrega, referencias adicionales, horario preferido, etc."><?php echo htmlspecialchars($formData['notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-light); border-radius: var(--radius); border: 1px solid var(--border);">
                            <h4 style="margin: 0 0 0.5rem; font-size: 0.875rem; color: var(--text-primary);">¿Primera vez comprando?</h4>
                            <p style="margin: 0; font-size: 0.8125rem; color: var(--text-secondary);">Tu información se guardará para futuras compras. Puedes crear una cuenta después del pedido.</p>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; margin-top: 2rem;">
                            <button type="submit" class="btn btn-primary btn-lg" style="padding: 1rem 2rem;">
                                <i class="bi bi-arrow-right"></i> Continuar al Pago
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <aside class="fade-in-up" style="animation-delay: 200ms;">
                    <div class="checkout-summary" style="background: var(--bg-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; height: fit-content; position: sticky; top: calc(var(--header-height) + var(--header-top-height) + 1.5rem); box-shadow: var(--shadow);">
                        <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border);"><i class="bi bi-receipt"></i> Resumen del Pedido</h3>
                        
                        <div class="order-items" style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 0.5rem;">
                            <?php foreach ($items as $item): ?>
                                <?php
                                    $itemPrice = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                                    $itemTotal = $itemPrice * $item['quantity'];
                                ?>
                                <div class="order-item" style="display: flex; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">
                                    <div class="order-item-image" style="width: 50px; height: 50px; border-radius: var(--radius); background: var(--bg-light); overflow: hidden; flex-shrink: 0;">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo AppConfig::asset('images/products/' . basename($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <i class="bi bi-box" style="font-size: 1.5rem; color: var(--text-muted); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="order-item-info" style="flex: 1; min-width: 0;">
                                        <div class="order-item-name" style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin-bottom: 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($item['name']); ?></div>
                                        <div class="order-item-qty" style="font-size: 0.8125rem; color: var(--text-muted);">Cant: <?php echo $item['quantity']; ?> <?php if ($item['on_sale']): ?><span class="sale-badge" style="margin-left: 0.5rem; font-size: 0.6875rem; background: var(--error); color: white; padding: 0.125rem 0.375rem; border-radius: var(--radius-full);">OFERTA</span><?php endif; ?></div>
                                    </div>
                                    <div class="order-item-price" style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem; white-space: nowrap;"><?php echo number_format($itemTotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="checkout-totals">
                            <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                                <span class="label">Subtotal (<?php echo count($items); ?> art.)</span>
                                <span class="value"><?php echo number_format($subtotal, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            </div>
                            
                            <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                                <span class="label">Envío</span>
                                <span class="value"><?php echo $shipping == 0 ? '<span style="color: var(--success); font-weight: 600;">Gratis</span>' : number_format($shipping, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            </div>
                            
                            <div class="summary-row" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--text-secondary);">
                                <span class="label">Impuestos (<?php echo $data['settings']['tax_rate'] ?? 15; ?>%)</span>
                                <span class="value"><?php echo number_format($tax, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            </div>
                            
                            <?php if ($discount > 0): ?>
                                <div class="summary-row summary-discount" style="display: flex; justify-content: space-between; padding: 0.5rem 0; font-size: 0.9375rem; color: var(--success);">
                                    <span class="label">Descuento</span>
                                    <span class="value">-<?php echo number_format($discount, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="summary-row total" style="display: flex; justify-content: space-between; padding: 0.75rem 0 0; margin-top: 0.5rem; border-top: 2px solid var(--border); font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                                <span class="label">Total a Pagar</span>
                                <span class="value" style="color: var(--secondary);"><?php echo number_format($total, 2, ',', '.') . ' ' . AppConfig::currencySymbol(); ?></span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkout-form');
    const cityInput = document.getElementById('shipping_city');
    const stateSelect = document.getElementById('shipping_state');
    const citySuggestions = document.getElementById('city-suggestions');
    const zipInput = document.getElementById('shipping_zip');
    const phoneInput = document.getElementById('shipping_phone');
    
    // Department -> Cities mapping
    const departmentCities = {};
    <?php foreach ($departments as $dept => $cities): ?>
        departmentCities['<?php echo addslashes($dept); ?>'] = <?php echo json_encode($cities); ?>;
    <?php endforeach; ?>
    
    // Auto-update state when city is selected
    cityInput.addEventListener('input', function() {
        const value = this.value.trim();
        // Find matching department
        for (const [dept, cities] of Object.entries(departmentCities)) {
            if (cities.includes(value)) {
                stateSelect.value = dept;
                break;
            }
        }
    });
    
    // Show city suggestions on focus
    cityInput.addEventListener('focus', function() {
        const state = stateSelect.value;
        if (state && departmentCities[state]) {
            showSuggestions(departmentCities[state]);
        } else {
            showSuggestions(Object.values(departmentCities).flat());
        }
    });
    
    // Filter suggestions when state changes
    stateSelect.addEventListener('change', function() {
        if (this.value && departmentCities[this.value]) {
            showSuggestions(departmentCities[this.value]);
            cityInput.value = '';
        } else {
            showSuggestions(Object.values(departmentCities).flat());
        }
    });
    
    function showSuggestions(cities) {
        citySuggestions.innerHTML = '';
        cities.forEach(city => {
            const div = document.createElement('div');
            div.textContent = city;
            div.style.cssText = 'padding: 0.75rem 1rem; cursor: pointer; transition: var(--transition-fast); border-bottom: 1px solid var(--border);';
            div.addEventListener('mouseenter', () => div.style.background = 'var(--bg-light)');
            div.addEventListener('mouseleave', () => div.style.background = '');
            div.addEventListener('click', () => {
                cityInput.value = city;
                citySuggestions.style.display = 'none';
                // Auto-select state
                for (const [dept, deptCities] of Object.entries(departmentCities)) {
                    if (deptCities.includes(city)) {
                        stateSelect.value = dept;
                        break;
                    }
                }
            });
            citySuggestions.appendChild(div);
        });
        citySuggestions.style.display = 'block';
    }
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!cityInput.contains(e.target) && !citySuggestions.contains(e.target) && !stateSelect.contains(e.target)) {
            citySuggestions.style.display = 'none';
        }
    });
    
    // Phone formatting
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 4) {
            value = value.slice(0, 4) + '-' + value.slice(4, 8);
        }
        this.value = value;
    });
    
    // ZIP validation (5 digits)
    zipInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 5);
    });
    
    // Form validation
    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.form-error').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            document.querySelectorAll('.form-control').forEach(el => {
                el.style.borderColor = '';
            });
            
            // Validate required fields
            const requiredFields = [
                { id: 'shipping_name', errorId: 'name-error', message: 'El nombre es requerido' },
                { id: 'shipping_email', errorId: 'email-error', message: 'Email inválido', validate: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) },
                { id: 'shipping_phone', errorId: 'phone-error', message: 'Teléfono requerido (formato: XXXX-XXXX)', validate: v => /^\d{4}-\d{4}$/.test(v) },
                { id: 'shipping_address', errorId: 'address-error', message: 'La dirección es requerida' },
                { id: 'shipping_city', errorId: 'city-error', message: 'La ciudad es requerida' },
                { id: 'shipping_state', errorId: 'state-error', message: 'Seleccione un departamento' },
                { id: 'shipping_zip', errorId: 'zip-error', message: 'Código postal requerido (5 dígitos)', validate: v => /^\d{5}$/.test(v) }
            ];
            
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                const errorEl = document.getElementById(field.errorId);
                
                if (!input.value.trim() || (field.validate && !field.validate(input.value.trim()))) {
                    isValid = false;
                    input.style.borderColor = 'var(--error)';
                    if (errorEl) {
                        errorEl.textContent = field.message;
                        errorEl.style.display = 'flex';
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.form-control[style*="var(--error)"]');
                if (firstError) {
                    firstError.focus();
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
    
    // Real-time validation clearing
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            if (this.style.borderColor.includes('rgb(239, 68, 68)') || this.style.borderColor.includes('var(--error)')) {
                this.style.borderColor = '';
                const errorId = this.id + '-error';
                const errorEl = document.getElementById(errorId);
                if (errorEl) {
                    errorEl.style.display = 'none';
                }
            }
        });
    });
    
    // Submit button loading state
    if (form) {
        form.addEventListener('submit', function(e) {
            if (this.checkValidity()) {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-hourglass-split spinner-inline"></i> Procesando...';
            }
        });
    }
});
</script>