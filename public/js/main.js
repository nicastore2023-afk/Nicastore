// ===================================
// Nicastore - Main JavaScript
// Modern, Accessible, Performant
// ===================================

(function() {
    'use strict';

    // ===================================
    // Utility Functions
    // ===================================

    const $ = (selector, context = document) => context.querySelector(selector);
    const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

    const debounce = (fn, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => fn.apply(this, args), delay);
        };
    };

    const throttle = (fn, limit) => {
        let inThrottle;
        return (...args) => {
            if (!inThrottle) {
                fn.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    };

    // ===================================
    // Mobile Menu
    // ===================================

    const initMobileMenu = () => {
        const toggle = $('.mobile-menu-toggle');
        const menu = $('.mobile-menu');
        const overlay = $('.mobile-overlay');
        const closeBtn = $('.mobile-menu-close');

        if (!toggle || !menu) return;

        const openMenu = () => {
            menu.classList.add('active');
            overlay.classList.add('active');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            // Focus trap
            const focusable = menu.querySelectorAll('a, button, input');
            if (focusable.length) focusable[0].focus();
        };

        const closeMenu = () => {
            menu.classList.remove('active');
            overlay.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        };

        toggle.addEventListener('click', () => {
            if (menu.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        closeBtn?.addEventListener('click', closeMenu);
        overlay?.addEventListener('click', closeMenu);

        // Close on escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu.classList.contains('active')) {
                closeMenu();
            }
        });

        // Close on link click
        $$('.mobile-nav a', menu).forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    };

    // ===================================
    // User Dropdown
    // ===================================

    const initUserDropdown = () => {
        const userMenus = $$('.user-menu');

        if (!userMenus.length) return;

        userMenus.forEach(userMenu => {
            const toggle = $('.user-toggle', userMenu);
            const dropdown = $('.user-dropdown', userMenu);

            if (!toggle || !dropdown) return;

            const closeDropdown = () => {
                userMenu.removeAttribute('aria-expanded');
                toggle.setAttribute('aria-expanded', 'false');
            };

            const openDropdown = () => {
                userMenu.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-expanded', 'true');
            };

            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const isOpen =
                    userMenu.getAttribute('aria-expanded') === 'true';

                if (isOpen) {
                    closeDropdown();
                } else {
                    userMenus.forEach(menu => {
                        menu.removeAttribute('aria-expanded');

                        const button = $('.user-toggle', menu);
                        button?.setAttribute('aria-expanded', 'false');
                    });

                    openDropdown();
                }
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) {
                    closeDropdown();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            dropdown.addEventListener('click', (e) => {
                const item = e.target.closest('a, button');

                if (item) {
                    closeDropdown();
                }
            });
        });
    };

    // ===================================
    // Search Enhancements
    // ===================================

    const initSearch = () => {
        const searchForms = $$('.search-form, .mobile-search');

        searchForms.forEach(form => {
            const input = $('input[type="search"]', form);
            if (!input) return;

            // Clear button
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'search-clear';
            clearBtn.innerHTML = '<i class="bi bi-x"></i>';
            clearBtn.setAttribute('aria-label', 'Limpiar búsqueda');
            clearBtn.style.cssText = `
                position: absolute;
                right: 2.5rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: var(--text-muted);
                padding: 0.25rem;
                display: none;
                cursor: pointer;
            `;

            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(clearBtn);

            input.addEventListener('input', () => {
                clearBtn.style.display = input.value ? 'block' : 'none';
            });

            clearBtn.addEventListener('click', () => {
                input.value = '';
                clearBtn.style.display = 'none';
                input.focus();
            });

            // Prevent submit on empty
            form.addEventListener('submit', (e) => {
                if (!input.value.trim()) {
                    e.preventDefault();
                    input.focus();
                }
            });
        });
    };

    // ===================================
    // Flash Messages
    // ===================================

    const initFlashMessages = () => {
        const container = $('.flash-messages');
        if (!container) return;

        $$('.flash', container).forEach(flash => {
            const closeBtn = $('.flash-close', flash);

            // Auto dismiss
            let dismissTimeout = setTimeout(() => {
                dismissFlash(flash);
            }, 5000);

            // Pause on hover
            flash.addEventListener('mouseenter', () => {
                clearTimeout(dismissTimeout);
            });

            flash.addEventListener('mouseleave', () => {
                dismissTimeout = setTimeout(() => {
                    dismissFlash(flash);
                }, 3000);
            });

            closeBtn?.addEventListener('click', () => {
                clearTimeout(dismissTimeout);
                dismissFlash(flash);
            });
        });
    };

    const dismissFlash = (flash) => {
        flash.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            flash.remove();
            // Remove container if empty
            const container = $('.flash-messages');
            if (container && !container.children.length) {
                container.remove();
            }
        }, 300);
    };

    // ===================================
    // Flash Message Helper
    // ===================================

    const showFlash = (type, message) => {
        let container = $('.flash-messages');
        if (!container) {
            container = document.createElement('div');
            container.className = 'flash-messages';
            container.style.cssText = `
                position: fixed;
                top: 1.5rem;
                right: 1.5rem;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
                max-width: 380px;
            `;
            document.body.appendChild(container);
        }

        const flash = document.createElement('div');
        flash.className = `flash flash-${type}`;
        flash.style.cssText = `
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideInRight 0.3s ease;
            max-width: 380px;
        `;

        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        flash.innerHTML = `
            <i class="bi ${icons[type] || icons.info}"></i>
            <span>${message}</span>
            <button type="button" class="flash-close" aria-label="Cerrar" style="background: none; border: none; color: inherit; font-size: 1.25rem; cursor: pointer; padding: 0.25rem; line-height: 1;">&times;</button>
        `;

        container.appendChild(flash);

        const dismissTimeout = setTimeout(() => dismissFlash(flash), 5000);

        flash.addEventListener('mouseenter', () => clearTimeout(dismissTimeout));
        flash.addEventListener('mouseleave', () => {
            setTimeout(() => dismissFlash(flash), 3000);
        });

        const closeBtn = flash.querySelector('.flash-close');
        closeBtn?.addEventListener('click', () => {
            clearTimeout(dismissTimeout);
            dismissFlash(flash);
        });
    };

    // ===================================
    // Guest Add to Cart Prompt
    // ===================================

    const showAddToCartGuestPrompt = (message) => {
        // Create a modern, engaging prompt for guest users
        const container = document.createElement('div');
        container.className = 'guest-prompt-toast';
        container.style.cssText = `
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            max-width: 380px;
            animation: slideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        `;

        container.innerHTML = `
            <div style="
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
                border-radius: var(--radius-lg);
                padding: 1.25rem;
                color: white;
                box-shadow: var(--shadow-xl);
                border: 1px solid rgba(255,255,255,0.1);
            ">
                <div style="display: flex; align-items: flex-start; gap: 0.875rem;">
                    <div style="
                        width: 48px; height: 48px;
                        background: rgba(255,255,255,0.15);
                        border-radius: var(--radius);
                        display: flex; align-items: center; justify-content: center;
                        flex-shrink: 0;
                        font-size: 1.5rem;
                    ">
                        <i class="bi bi-bag-heart-fill"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h4 style="margin: 0 0 0.375rem; font-size: 0.9375rem; font-weight: 600;">
                            ¡Producto añadido! 🎉
                        </h4>
                        <p style="margin: 0 0 0.75rem; font-size: 0.8125rem; opacity: 0.9; line-height: 1.4;">
                            Tu producto está en el carrito. <strong>Inicia sesión o regístrate</strong> para guardar tu carrito y obtener <strong>10% de descuento</strong> en tu primera compra con el cupón <strong>WELCOME10</strong>.
                        </p>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="/login" style="
                                flex: 1; text-align: center;
                                padding: 0.625rem 1rem;
                                background: rgba(255,255,255,0.2);
                                border: 1px solid rgba(255,255,255,0.3);
                                border-radius: var(--radius);
                                color: white; text-decoration: none;
                                font-size: 0.8125rem; font-weight: 500;
                                transition: all 0.2s ease;
                            " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                            </a>
                            <a href="/register" style="
                                flex: 1; text-align: center;
                                padding: 0.625rem 1rem;
                                background: white; color: var(--primary);
                                border-radius: var(--radius);
                                text-decoration: none;
                                font-size: 0.8125rem; font-weight: 600;
                                transition: all 0.2s ease;
                            " onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                                <i class="bi bi-person-plus-fill me-1"></i> Registrarse
                            </a>
                        </div>
                        <button type="button" class="guest-prompt-close" aria-label="Cerrar" style="
                            position: absolute; top: 0.5rem; right: 0.5rem;
                            background: none; border: none; color: rgba(255,255,255,0.7);
                            font-size: 1.25rem; cursor: pointer; padding: 0.25rem;
                            line-height: 1; opacity: 0.7; transition: opacity 0.2s;
                        " onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">&times;</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(container);

        // Add entrance animation
        setTimeout(() => {
            container.style.opacity = '1';
            container.style.transform = 'translateX(0)';
        }, 10);

        // Close button handler
        const closeBtn = container.querySelector('.guest-prompt-close');
        const dismiss = () => {
            container.style.animation = 'slideInRight 0.3s ease reverse';
            setTimeout(() => container.remove(), 300);
        };

        closeBtn?.addEventListener('click', dismiss);

        // Auto dismiss after 10 seconds
        setTimeout(() => {
            if (document.body.contains(container)) {
                container.style.animation = 'slideInRight 0.4s ease reverse';
                setTimeout(() => container.remove(), 400);
            }
        }, 10000);

        // Store reference for potential reuse
        window.lastGuestPrompt = container;
    };

    // ===================================
    // Cart Count Updates
    // ===================================

    const updateCartCount = (count) => {
        $$('#cart-count, .cart-count').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? 'flex' : 'none';
            // Animate
            el.style.animation = 'none';
            el.offsetHeight; // Force reflow
            el.style.animation = 'pulse 0.5s ease';
        });
    };

    // ===================================
    // Add to Cart Forms
    // ===================================

    const initAddToCart = () => {
        $$('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const submitBtn = $('button[type="submit"]', form);
                const originalHTML = submitBtn?.innerHTML;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> Añadiendo...';
                }

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        updateCartCount(result.count);

                        // Check if user is not logged in (guest cart)
                        const isGuest = !document.body.classList.contains('user-logged-in');

                        if (isGuest) {
                            // Show modern login/register prompt with welcome coupon
                            showAddToCartGuestPrompt(result.message);
                        } else {
                            Nicastore.showFlash('success', result.message);
                        }
                    } else {
                        Nicastore.showFlash('error', result.message);
                    }
                } catch (error) {
                    Nicastore.showFlash('error', 'Error al añadir al carrito');
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHTML;
                    }
                }
            });
        });
    };

    // ===================================
    // Cart Page Interactions
    // ===================================

    const initCartPage = () => {
        // Quantity controls
        $$('.qty-btn[data-action]').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.parentElement.querySelector('.qty-input');
                if (!input) return;

                const current = parseInt(input.value) || 1;
                const action = btn.dataset.action;
                const max = parseInt(input.max) || 999;
                let newValue = current;

                if (action === 'increase') {
                    newValue = Math.min(max, current + 1);
                } else if (action === 'decrease') {
                    newValue = Math.max(1, current - 1);
                }

                if (newValue !== current) {
                    input.value = newValue;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        // Quantity input change
        $$('.qty-input').forEach(input => {
            let updateTimeout;
            input.addEventListener('change', debounce(async () => {
                const form = input.closest('.update-cart-form, .quantity-form');
                if (!form) return;

                const itemId = form.dataset.itemId;
                const quantity = parseInt(input.value) || 1;

                if (!itemId) return;

                try {
                    const formData = new FormData();
                    formData.append('quantity', quantity);
                    formData.append('_token', $('input[name="_token"]', form)?.value || '');

                    const response = await fetch(`/carrito/actualizar/${itemId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        updateCartCount(result.count);
                        updateCartSummary(result);
                    } else {
                        Nicastore.showFlash('error', result.message);
                        // Reset input
                        input.value = input.dataset.previous || 1;
                    }
                } catch (error) {
                    Nicastore.showFlash('error', 'Error al actualizar el carrito');
                }
            }, 300));

            input.addEventListener('focus', () => {
                input.dataset.previous = input.value;
            });
        });

        // Remove item
        $$('.remove-cart-form, .remove-btn[data-item-id]').forEach(el => {
            const form = el.tagName === 'FORM' ? el : el.closest('form');
            if (!form) return;

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!confirm('¿Eliminar este producto del carrito?')) return;

                const itemId = form.dataset.itemId || el.dataset.itemId;
                if (!itemId) return;

                try {
                    const formData = new FormData();
                    formData.append('_token', $('input[name="_token"]', form)?.value || '');

                    const response = await fetch(`/carrito/eliminar/${itemId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        updateCartCount(result.count);
                        updateCartSummary(result);
                        // Remove row with animation
                        const row = form.closest('tr');
                        if (row) {
                            row.style.animation = 'fadeOut 0.3s ease';
                            setTimeout(() => {
                                row.remove();
                                checkEmptyCart();
                            }, 300);
                        }
                    } else {
                        Nicastore.showFlash('error', result.message);
                    }
                } catch (error) {
                    Nicastore.showFlash('error', 'Error al eliminar el artículo');
                }
            });
        });
    };

    const updateCartSummary = (data) => {
        const mappings = {
            'cart-subtotal': data.subtotal,
            'cart-shipping': data.shipping,
            'cart-tax': data.tax,
            'cart-total': data.total
        };

        Object.entries(mappings).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el && value !== undefined) {
                el.textContent = formatPrice(value);
            }
        });

        // Update discount row
        const discountRow = document.querySelector('.summary-discount');
        if (discountRow && data.discount) {
            discountRow.querySelector('.value').textContent = `-${formatPrice(data.discount)}`;
        }
    };

    const checkEmptyCart = () => {
        const rows = $$('.cart-table tbody tr');
        if (rows.length === 0) {
            location.reload(); // Reload to show empty state
        }
    };

    // ===================================
    // Coupon Form
    // ===================================

    const initCouponForm = () => {
        const couponForm = $('#coupon-form');
        if (!couponForm) return;

        couponForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = $('button[type="submit"]', couponForm);
            const input = $('input[name="coupon_code"]', couponForm);
            const originalHTML = submitBtn?.innerHTML;

            if (!input?.value.trim()) {
                Nicastore.showFlash('error', 'Introduce un código de cupón');
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Aplicando...';
            }

            try {
                const formData = new FormData(couponForm);
                const response = await fetch(couponForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Nicastore.showFlash('success', result.message);
                    updateCartSummary(result);
                    couponForm.reset();
                } else {
                    Nicastore.showFlash('error', result.message);
                }
            } catch (error) {
                Nicastore.showFlash('error', 'Error al aplicar el cupón');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            }
        });
    };

    // ===================================
    // Product Gallery
    // ===================================

    const initProductGallery = () => {
        const thumbs = $$('.thumb');
        const mainImage = $('.main-image img');

        if (!thumbs.length || !mainImage) return;

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', () => {
                const img = $('img', thumb);
                if (!img) return;

                // Update active state
                thumbs.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');

                // Swap image with fade
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.src = img.src;
                    mainImage.alt = img.alt;
                    mainImage.style.opacity = '1';
                }, 150);
            });
        });
    };

    // ===================================
    // Password Toggle
    // ===================================

    const initPasswordToggle = () => {
        $$('input[type="password"]').forEach(input => {
            if (input.parentElement.classList.contains('password-wrapper')) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'password-wrapper';
            wrapper.style.position = 'relative';

            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'password-toggle';
            toggle.innerHTML = '<i class="bi bi-eye"></i>';
            toggle.setAttribute('aria-label', 'Mostrar contraseña');
            toggle.style.cssText = `
                position: absolute;
                right: 1rem;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: var(--text-muted);
                padding: 0.25rem;
                cursor: pointer;
                font-size: 1.125rem;
            `;

            wrapper.appendChild(toggle);

            toggle.addEventListener('click', () => {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                toggle.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
                toggle.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });
    };

    // ===================================
    // Back to Top
    // ===================================

    const initBackToTop = () => {
        const btn = $('.back-to-top');
        if (!btn) return;

        const toggleVisibility = throttle(() => {
            if (window.scrollY > 300) {
                btn.classList.add('visible');
            } else {
                btn.classList.remove('visible');
            }
        }, 100);

        window.addEventListener('scroll', toggleVisibility);

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    };

    // ===================================
    // Form Validation
    // ===================================

    const initFormValidation = () => {
        $$('form[data-validate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                let valid = true;
                const requiredFields = $$('[required]', form);

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        showFieldError(field, 'Este campo es obligatorio');
                        valid = false;
                    } else {
                        clearFieldError(field);
                    }

                    // Email validation
                    if (field.type === 'email' && field.value.trim()) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(field.value)) {
                            showFieldError(field, 'Email inválido');
                            valid = false;
                        }
                    }

                    // Min length
                    if (field.minLength && field.value.length < field.minLength) {
                        showFieldError(field, `Mínimo ${field.minLength} caracteres`);
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    // Focus first invalid field
                    const firstInvalid = $('.form-control[aria-invalid="true"]', form);
                    firstInvalid?.focus();
                }
            });
        });
    };

    const showFieldError = (field, message) => {
        field.setAttribute('aria-invalid', 'true');
        field.style.borderColor = 'var(--error)';

        let errorEl = field.parentElement.querySelector('.form-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.className = 'form-error';
            field.parentElement.appendChild(errorEl);
        }
        errorEl.textContent = message;
    };

    const clearFieldError = (field) => {
        field.removeAttribute('aria-invalid');
        field.style.borderColor = '';
        const errorEl = field.parentElement.querySelector('.form-error');
        errorEl?.remove();
    };

    // ===================================
    // Image Error Handling
    // ===================================

    const initImageErrorHandling = () => {
        $$('img').forEach(img => {
            // Skip if already has error handler
            if (img.dataset.errorHandled) return;

            img.addEventListener('error', function() {
                // Prevent infinite loop
                if (this.dataset.fallbackApplied) return;
                this.dataset.fallbackApplied = 'true';

                // Replace with placeholder
                this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjFmMWYxIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9IjUuNSI+SW1hZ2VuIG5vIGRpc3BvbmlibGU8L3RleHQ+PC9zdmc+';
                this.alt = 'Imagen no disponible';
                this.style.objectFit = 'cover';
            });

            // Mark as handled
            img.dataset.errorHandled = 'true';
        });
    };

    // ===================================
    // Delete Confirmation
    // ===================================

    const initDeleteConfirmation = () => {
        $$('.delete-form, [data-confirm]').forEach(el => {
            const form = el.tagName === 'FORM' ? el : el.closest('form');
            if (!form) return;

            const message = el.dataset.confirm || '¿Estás seguro de que deseas eliminar este elemento?';

            form.addEventListener('submit', (e) => {
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });
    };

    // ===================================
    // Table Row Click
    // ===================================

    const initTableRowClick = () => {
        $$('.data-table tbody tr[data-href]').forEach(row => {
            row.style.cursor = 'pointer';
            row.addEventListener('click', (e) => {
                // Don't navigate if clicking on a link or button
                if (e.target.closest('a, button, input, select')) return;
                window.location.href = row.dataset.href;
            });
        });
    };

    // ===================================
    // Loading States
    // ===================================

    const showLoading = (element) => {
        element.dataset.originalHTML = element.innerHTML;
        element.disabled = true;
        element.innerHTML = '<span class="loading-spinner"></span> Cargando...';
    };

    const hideLoading = (element) => {
        element.disabled = false;
        element.innerHTML = element.dataset.originalHTML || '';
    };

    // Expose globally
    window.Nicastore = {
        showFlash,
        updateCartCount,
        showLoading,
        hideLoading,
        formatPrice,
        
        // CSRF Helpers
        csrf: {
            getToken: () => {
                const meta = document.querySelector('meta[name="csrf-token"]');
                const input = document.querySelector('input[name="_token"]');
                return meta?.getAttribute('content') || input?.value || '';
            },
            getTokenName: () => '_token',
            getHeaders: () => {
                const token = window.Nicastore.csrf.getToken();
                return token ? { 'X-CSRF-TOKEN': token } : {};
            },
            addToFormData: (formData) => {
                const token = window.Nicastore.csrf.getToken();
                if (token) formData.append('_token', token);
            },
            addToFetchOptions: (options = {}) => {
                const headers = new Headers(options.headers || {});
                const csrfHeaders = window.Nicastore.csrf.getHeaders();
                Object.entries(csrfHeaders).forEach(([k, v]) => headers.set(k, v));
                return { ...options, headers };
            }
        }
    };

    // ===================================
    // Price Formatting
    // ===================================

    function formatPrice(price) {
        return new Intl.NumberFormat('es-NI', {
            style: 'currency',
            currency: 'USD', // Using USD format for display, adjust as needed
            minimumFractionDigits: 2
        }).format(price);
    }

    // ===================================
    // Initialize All
    // ===================================

    document.addEventListener('DOMContentLoaded', () => {
        initMobileMenu();
        initUserDropdown();
        initSearch();
        initFlashMessages();
        initAddToCart();
        initCartPage();
        initCouponForm();
        initProductGallery();
        initPasswordToggle();
        initBackToTop();
        initFormValidation();
        initDeleteConfirmation();
        initTableRowClick();
        initImageErrorHandling();

        // Add loaded class for CSS animations
        document.body.classList.add('loaded');
    });

    // Handle page transitions (if using Turbo/Turbolinks-like behavior)
    document.addEventListener('astro:after-swap', () => {
        // Re-initialize if needed
    });

})();