<?php
/** @var string $app_name */
/** @var string $title */
/** @var array|null $flash */
/** @var string $csrf_token */
/** @var array|null $user */
use Nicastore\Config\AppConfig;
use Nicastore\Core\Session;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($app_name); ?> - Tu tienda de tecnología en Nicaragua">
    <title><?php echo htmlspecialchars($title ?? $app_name); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(AppConfig::asset('css/style.css')); ?>">
</head>
<body<?php echo Session::isLoggedIn() ? ' class="user-logged-in"' : ''; ?>>
    <?php if (isset($flash) && $flash): ?>
        <div class="flash-messages" role="alert" aria-live="polite">
            <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>">
                <i class="bi <?php echo $flash['type'] === 'success' ? 'bi-check-circle-fill' : ($flash['type'] === 'error' ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill'); ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="flash-close" aria-label="Cerrar">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Header -->
    <header class="site-header" role="banner">
        <div class="header-top">
            <div class="container">
                <div class="header-contact">
                    <span><i class="bi bi-geo-alt-fill"></i> Managua, Nicaragua</span>
                    <span><i class="bi bi-telephone-fill"></i> +505 2270-XXXX</span>
                    <span><i class="bi bi-envelope-fill"></i> info@nicastore.com.ni</span>
                </div>
                <div class="header-social">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
        
        <nav class="main-nav" role="navigation" aria-label="Navegación principal">
            <div class="container">
                <a href="<?php echo AppConfig::url('/'); ?>" class="logo" aria-label="Nicastore - Inicio">
                    <i class="bi bi-cpu-fill"></i>
                    <span>Nicastore</span>
                </a>
                
                <!-- Search -->
                <form class="search-form" action="<?php echo AppConfig::url('/buscar'); ?>" method="GET" role="search">
                    <label for="search-input" class="visually-hidden">Buscar productos</label>
                    <i class="bi bi-search search-icon" aria-hidden="true"></i>
                    <input type="search" id="search-input" name="q" class="search-input" placeholder="Buscar productos, marcas, categorías..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
                    <button type="submit" class="search-btn" aria-label="Buscar"><i class="bi bi-search"></i></button>
                </form>
                
                <div class="nav-actions">
                    <div class="nav-item cart-wrapper">
                        <a href="<?php echo AppConfig::url('/carrito'); ?>" class="nav-link cart-link" aria-label="Carrito de compras">
                            <i class="bi bi-bag-fill" aria-hidden="true"></i>
                            <span class="cart-count" id="cart-count" aria-live="polite"><?php 
                                $cart = new \Nicastore\Models\Cart();
                                echo $cart->getCount(); 
                            ?></span>
                        </a>
                    </div>
                    
                    <?php if ($user): ?>
                        <div class="nav-item user-menu">
                            <button class="nav-link user-toggle" aria-expanded="false" aria-haspopup="true" aria-label="Menú de usuario">
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
                                <span><?php echo htmlspecialchars(explode(' ', $user['name'])[0]); ?></span>
                                <i class="bi bi-chevron-down" aria-hidden="true"></i>
                            </button>
                            <div class="user-dropdown" role="menu">
                                <div class="user-info">
                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                    <small><?php echo htmlspecialchars($user['email']); ?></small>
                                </div>
                                <hr class="dropdown-divider">
                                <a href="<?php echo AppConfig::url('/cuenta'); ?>" class="dropdown-item" role="menuitem"><i class="bi bi-speedometer2"></i> Mi Cuenta</a>
                                <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>" class="dropdown-item" role="menuitem"><i class="bi bi-bag-check"></i> Mis Pedidos</a>
                                <a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>" class="dropdown-item" role="menuitem"><i class="bi bi-person-gear"></i> Perfil</a>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <hr class="dropdown-divider">
                                    <a href="<?php echo AppConfig::url('/admin'); ?>" class="dropdown-item" role="menuitem"><i class="bi bi-gear-fill"></i> Panel Admin</a>
                                <?php endif; ?>
                                <hr class="dropdown-divider">
                                <form action="<?php echo AppConfig::url('/logout'); ?>" method="POST" style="width: 100%;">
                                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <button type="submit" class="dropdown-item logout-btn" role="menuitem"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="nav-item auth-buttons">
                            <a href="<?php echo AppConfig::url('/login'); ?>" class="btn btn-outline">Iniciar Sesión</a>
                            <a href="<?php echo AppConfig::url('/register'); ?>" class="btn btn-primary">Registrarse</a>
                        </div>
                    <?php endif; ?>
                    
                    <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="main-menu" aria-label="Abrir menú">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
            </div>
        </nav>
        
        <!-- Mobile Menu -->
        <div class="mobile-menu" id="main-menu" role="navigation" aria-label="Menú móvil">
            <div class="mobile-menu-header">
                <a href="<?php echo AppConfig::url('/'); ?>" class="logo">
                    <i class="bi bi-cpu-fill"></i>
                    <span>Nicastore</span>
                </a>
                <button class="mobile-menu-close" aria-label="Cerrar menú"><i class="bi bi-x-lg"></i></button>
            </div>
            <form class="mobile-search" action="<?php echo AppConfig::url('/buscar'); ?>" method="GET" role="search">
                <i class="bi bi-search"></i>
                <input type="search" name="q" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            </form>
            <ul class="mobile-nav">
                <li><a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-house"></i> Inicio</a></li>
                <li><a href="<?php echo AppConfig::url('/productos'); ?>"><i class="bi bi-grid"></i> Productos</a></li>
                <li><a href="<?php echo AppConfig::url('/productos'); ?>"><i class="bi bi-tags"></i> Categorías</a></li>
                <?php if ($user): ?>
                    <li><a href="<?php echo AppConfig::url('/cuenta'); ?>"><i class="bi bi-person"></i> Mi Cuenta</a></li>
                    <li><a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>"><i class="bi bi-bag-check"></i> Mis Pedidos</a></li>
                    <?php if ($user['role'] === 'admin'): ?>
                        <li><a href="<?php echo AppConfig::url('/admin'); ?>"><i class="bi bi-gear"></i> Panel Admin</a></li>
                    <?php endif; ?>
                    <li>
                        <form action="<?php echo AppConfig::url('/logout'); ?>" method="POST">
                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                            <button type="submit" class="mobile-nav-link"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="<?php echo AppConfig::url('/login'); ?>"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a></li>
                    <li><a href="<?php echo AppConfig::url('/register'); ?>"><i class="bi bi-person-plus"></i> Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="mobile-overlay" aria-hidden="true"></div>
    </header>
    
    <!-- Main Content -->
    <main id="main-content" role="main">
        <?php echo $content ?? ''; ?>
    </main>
    
    <!-- Footer -->
    <footer class="site-footer" role="contentinfo">
        <div class="footer-main">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-brand">
                        <a href="<?php echo AppConfig::url('/'); ?>" class="footer-logo" aria-label="Nicastore - Inicio">
                            <i class="bi bi-cpu-fill"></i>
                            <span>Nicastore</span>
                        </a>
                        <p class="footer-tagline">Tu tienda de tecnología de confianza en Nicaragua. Productos de calidad, precios justos y atención personalizada.</p>
                        <div class="footer-social">
                            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                            <a href="#" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>
                    
                    <nav class="footer-nav" aria-label="Enlaces principales">
                        <h4>Enlaces Rápidos</h4>
                        <ul>
                            <li><a href="<?php echo AppConfig::url('/'); ?>"><i class="bi bi-chevron-right"></i> Inicio</a></li>
                            <li><a href="<?php echo AppConfig::url('/productos'); ?>"><i class="bi bi-chevron-right"></i> Productos</a></li>
                            <li><a href="<?php echo AppConfig::url('/productos'); ?>"><i class="bi bi-chevron-right"></i> Categorías</a></li>
                            <li><a href="<?php echo AppConfig::url('/carrito'); ?>"><i class="bi bi-chevron-right"></i> Carrito</a></li>
                            <li><a href="<?php echo AppConfig::url('/buscar'); ?>"><i class="bi bi-chevron-right"></i> Buscar</a></li>
                        </ul>
                    </nav>
                    
                    <nav class="footer-nav" aria-label="Mi cuenta">
                        <h4>Mi Cuenta</h4>
                        <ul>
                            <?php if ($user): ?>
                                <li><a href="<?php echo AppConfig::url('/cuenta'); ?>"><i class="bi bi-chevron-right"></i> Panel de Control</a></li>
                                <li><a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>"><i class="bi bi-chevron-right"></i> Mis Pedidos</a></li>
                                <li><a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>"><i class="bi bi-chevron-right"></i> Mi Perfil</a></li>
                            <?php else: ?>
                                <li><a href="<?php echo AppConfig::url('/login'); ?>"><i class="bi bi-chevron-right"></i> Iniciar Sesión</a></li>
                                <li><a href="<?php echo AppConfig::url('/register'); ?>"><i class="bi bi-chevron-right"></i> Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    
                    <div class="footer-contact">
                        <h4>Contacto</h4>
                        <ul>
                            <li><i class="bi bi-geo-alt"></i> <address><?php echo htmlspecialchars($settings['store_address'] ?? 'Km 4.5 Carretera a Masaya, Managua, Nicaragua'); ?></address></li>
                            <li><i class="bi bi-telephone"></i> <a href="tel:<?php echo htmlspecialchars($settings['store_phone'] ?? '+5052270XXXX'); ?>"><?php echo htmlspecialchars($settings['store_phone'] ?? '+505 2270-XXXX'); ?></a></li>
                            <li><i class="bi bi-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($settings['store_email'] ?? 'info@nicastore.com.ni'); ?>"><?php echo htmlspecialchars($settings['store_email'] ?? 'info@nicastore.com.ni'); ?></a></li>
                            <li><i class="bi bi-whatsapp"></i> <a href="https://wa.me/5058888XXXX">WhatsApp: +505 8888-XXXX</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($app_name); ?>. Todos los derechos reservados.</p>
                    <div class="footer-legal">
                        <a href="#">Términos y Condiciones</a>
                        <a href="#">Política de Privacidad</a>
                        <a href="#">Política de Devoluciones</a>
                        <a href="#">Envíos y Entregas</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top -->
    <button class="back-to-top" aria-label="Volver arriba" title="Volver arriba">
        <i class="bi bi-chevron-up"></i>
    </button>
    
    <script src="<?php echo htmlspecialchars(AppConfig::asset('js/main.js')); ?>"></script>
</body>
</html>