<?php
/** @var array $data */
/** @var string $csrf_token */
use Nicastore\Config\AppConfig;
?>

<?php
$user = $data['user'];
?>

<div class="container account-page" style="padding: 3rem 0;">
    <header class="fade-in-up" style="margin-bottom: 2rem;">
        <h1 class="section-title" style="text-align: left; margin: 0;">Mi Perfil</h1>
        <p style="color: var(--text-secondary); margin-top: 0.5rem;">Actualiza tu información personal</p>
    </header>
    
    <div class="account-layout">
        <!-- Sidebar -->
        <aside class="account-sidebar fade-in-up">
            <div class="account-sidebar-header">
                <div class="account-avatar">
                    <i class="bi bi-person"></i>
                </div>
                <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <nav class="account-nav" aria-label="Navegación de cuenta">
                <a href="<?php echo AppConfig::url('/cuenta'); ?>">
                    <i class="bi bi-speedometer2"></i> Panel
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/pedidos'); ?>">
                    <i class="bi bi-bag-check"></i> Mis Pedidos
                </a>
                <a href="<?php echo AppConfig::url('/cuenta/perfil'); ?>" class="active">
                    <i class="bi bi-person-gear"></i> Mi Perfil
                </a>
                <hr style="margin: 1rem; border-color: var(--border);">
                <form action="<?php echo AppConfig::url('/logout'); ?>" method="POST">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: left; background: none; border: none; color: var(--error);">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </button>
                </form>
            </nav>
        </aside>
        
        <!-- Content -->
        <main class="account-content fade-in-up" style="animation-delay: 100ms;">
            <form method="POST" action="<?php echo AppConfig::url('/cuenta/perfil/actualizar'); ?>" class="profile-form" data-validate>
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="data-table" style="padding: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-person"></i> Información Personal</h3>
                    
                    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label" for="name">Nombre Completo <span aria-hidden="true">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($user['name']); ?>" autocomplete="name">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email <span aria-hidden="true">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($user['email']); ?>" autocomplete="email">
                        </div>
                    </div>
                    
                    <hr style="margin: 2rem 0; border-color: var(--border);">
                    
                    <h3 style="margin-bottom: 1.5rem; color: var(--primary);"><i class="bi bi-lock"></i> Cambiar Contraseña</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Deja los campos en blanco si no deseas cambiar la contraseña.</p>
                    
                    <div class="form-group">
                        <label class="form-label" for="current_password">Contraseña Actual <span aria-hidden="true">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-control" autocomplete="current-password" placeholder="Ingresa tu contraseña actual">
                        <small class="form-hint">Requerido para cambiar la contraseña</small>
                    </div>
                    
                    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label" for="new_password">Nueva Contraseña</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="new_password_confirm">Confirmar Nueva Contraseña</label>
                            <input type="password" id="new_password_confirm" name="new_password_confirm" class="form-control" autocomplete="new-password" placeholder="Repite la nueva contraseña">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Danger Zone -->
            <div class="data-table" style="margin-top: 2rem; padding: 2rem; border-color: #FEF2F2; background: #FEF2F2;">
                <h3 style="margin-bottom: 1rem; color: var(--error);"><i class="bi bi-exclamation-triangle"></i> Zona de Peligro</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Una vez eliminada, tu cuenta no podrá ser recuperada.</p>
                <form action="<?php echo AppConfig::url('/cuenta/eliminar'); ?>" method="POST" onsubmit="return confirm('¿ESTÁS SEGURO? Esta acción es irreversible. Se eliminarán todos tus datos, pedidos e historial.');">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <button type="submit" class="btn btn-danger" style="background: var(--error); border-color: var(--error);">
                        <i class="bi bi-trash"></i> Eliminar Mi Cuenta
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>