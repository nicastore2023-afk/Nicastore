# Nicastore - Tienda Online Minimalista

Tienda online minimalista desarrollada en PHP con diseño en tonos morado y rosado.

## Características

- ✅ Diseño minimalista con tema morado/rosado
- ✅ Sistema de registro y login de usuarios
- ✅ Catálogo de productos con paginación
- ✅ Búsqueda de productos
- ✅ Carrito de compras persistente
- ✅ Cupones de descuento
- ✅ Proceso de checkout
- ✅ Panel de administración
- ✅ Gestión de productos, categorías y pedidos
- ✅ Pruebas unitarias con PHPUnit

## Requisitos

- PHP 8.0 o superior
- SQLite (incluido en PHP)
- Composer

## Instalación

1. Clona o descarga el proyecto

2. Instala las dependencias:
```bash
composer install
```

3. Crea la base de datos y ejecuta las migraciones:
```bash
php database/migrate.php
```

4. Inserta datos de prueba:
```bash
php database/seed.php
```

5. Inicia el servidor de desarrollo:
```bash
php -S localhost:8000 -t public
```

6. Visita `http://localhost:8000` en tu navegador

## Cuentas de Prueba

### Administrador
- Email: `admin@nicastore.com`
- Contraseña: `password123`

### Cliente
- Email: `customer@nicastore.com`
- Contraseña: `password123`

## Cupones de Prueba

- `WELCOME10` - 10% de descuento en pedidos > €50
- `NIKASTORE20` - 20% de descuento en pedidos > €100
- `FREESHIP` - Envío gratuito

## Estructura del Proyecto

```
nicastore/
├── public/              # Punto de entrada público
│   ├── index.php       # Front controller
│   ├── css/
│   │   └── style.css   # Estilos principales
│   ├── js/
│   │   └── main.js     # JavaScript
│   └── images/         # Imágenes subidas
├── src/
│   ├── Core/           # Clases base
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   └── Router.php
│   ├── Config/         # Configuraciones
│   │   ├── App.php
│   │   ├── Database.php
│   │   └── Session.php
│   ├── Models/         # Modelos de datos
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Cart.php
│   │   ├── Order.php
│   │   ├── Coupon.php
│   │   └── Setting.php
│   ├── Controllers/    # Controladores
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── ProductController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── AccountController.php
│   │   └── AdminController.php
│   └── Views/          # Vistas
│       ├── layouts/    # Layouts principales
│       ├── auth/       # Vistas de autenticación
│       ├── products/   # Vistas de productos
│       ├── cart/       # Vistas del carrito
│       ├── checkout/   # Vistas de checkout
│       ├── account/    # Vistas de cuenta
│       └── admin/      # Vistas de administración
├── database/
│   ├── migrate.php     # Migraciones
│   └── seed.php        # Datos de prueba
├── tests/              # Pruebas unitarias
│   ├── DatabaseTest.php
│   ├── ModelTest.php
│   └── ControllerTest.php
└── composer.json
```

## Testing

Para ejecutar las pruebas unitarias:

```bash
./vendor/bin/phpunit
```

Para ejecutar pruebas con cobertura:

```bash
./vendor/bin/phpunit --coverage-html coverage
```

## Panel de Administración

Accede al panel de administración en:
```
http://localhost:8000/admin
```

Usa las credenciales de administrador para acceder.

## Características de Diseño

- **Paleta de colores**: Morado (#6B2FA0) y Rosado (#E91E63)
- **Interfaz minimalista**: Diseño limpio y moderno
- **Responsive**: Adaptable a móviles y tablets
- **Animaciones suaves**: Transiciones y micro-interacciones
- **Tipografía moderna**: Sistema de fuentes limpio y legible

## Seguridad

- Protección CSRF en todos los formularios
- Validación y sanitización de entradas
- Hashing seguro de contraseñas con bcrypt
- Sesiones seguras con regeneración de IDs
- Protección contra SQL injection con prepared statements

## Licencia

MIT License - ver archivo LICENSE para más detalles.