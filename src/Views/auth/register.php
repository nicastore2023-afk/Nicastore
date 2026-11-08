<?php
/** @var string $title */
/** @var string $app_name */
use Nicastore\Config\AppConfig;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Registrarse'); ?> - <?php echo htmlspecialchars($app_name); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo AppConfig::asset('css/style.css'); ?>">
</head>
<body class="auth-page">
    <div class="auth-card fade-in-up">
        <div class="auth-header" style="text-align: center; margin-bottom: 2rem;">
            <a href="<?php echo AppConfig::url('/'); ?>" class="logo" style="justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                <i class="bi bi-cpu-fill"></i>
                <span>Nicastore</span>
            </a>
            <h2 style="margin-bottom: 0.5rem;">Crear Cuenta</h2>
            <p class="auth-subtitle">Únete a Nicastore y disfruta de los mejores precios</p>
        </div>
        
        <?php if (isset($flash) && $flash): ?>
            <div class="flash flash-<?php echo htmlspecialchars($flash['type']); ?>" style="margin-bottom: 1.5rem;" role="alert">
                <i class="bi <?php echo $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'; ?> me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo AppConfig::url('/register'); ?>" data-validate>
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <label class="form-label" for="name">Nombre Completo <span aria-hidden="true">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required autofocus placeholder="Tu nombre completo" autocomplete="name">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email <span aria-hidden="true">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required placeholder="tu@email.com" autocomplete="email">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Contraseña <span aria-hidden="true">*</span></label>
                <input type="password" id="password" name="password" class="form-control" required placeholder="Mínimo 8 caracteres" minlength="8" autocomplete="new-password">
                <small class="form-hint">Mínimo 8 caracteres, combina letras y números</small>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password_confirm">Confirmar Contraseña <span aria-hidden="true">*</span></label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required placeholder="Repite tu contraseña" autocomplete="new-password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 0.5rem;">
                <i class="bi bi-person-plus"></i> Registrarse
            </button>
        </form>
        
        <div class="auth-footer">
            <p>¿Ya tienes cuenta? <a href="<?php echo AppConfig::url('/login'); ?>">Inicia sesión</a></p>
        </div>
    </div>
</body>
</html>