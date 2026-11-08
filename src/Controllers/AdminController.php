<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\User;
use Nicastore\Models\Product;
use Nicastore\Models\Category;
use Nicastore\Models\Order;
use Nicastore\Models\Setting;
use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class AdminController extends Controller
{
    private User $userModel;
    private Product $productModel;
    private Category $categoryModel;
    private Order $orderModel;
    private Setting $settingModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->settingModel = new Setting();
        
        if (!Session::isLoggedIn() || Session::getUser()['role'] !== 'admin') {
            $this->redirect(AppConfig::url('/'));
        }
    }
    
    public function dashboard(): void
    {
        $stats = [
            'users' => (int)$this->userModel->query("SELECT COUNT(*) as count FROM users")->fetch()['count'],
            'products' => (int)$this->productModel->query("SELECT COUNT(*) as count FROM products")->fetch()['count'],
            'orders' => (int)$this->orderModel->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'],
            'revenue' => (float)$this->orderModel->query("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled'")->fetch()['total'],
            'pending_orders' => (int)$this->orderModel->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch()['count'],
            'monthly_revenue' => (float)$this->orderModel->query("
                SELECT COALESCE(SUM(total), 0) as total 
                FROM orders 
                WHERE status != 'cancelled' 
                AND created_at >= date('now', 'start of month')
            ")->fetch()['total'],
        ];
        
        $recentOrders = $this->orderModel->query("
            SELECT o.*, u.name as user_name, u.email as user_email
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 5
        ")->fetchAll();
        
        $lowStockProducts = $this->productModel->query("
            SELECT id, name, sku, stock, price, sale_price, image
            FROM products 
            WHERE is_active = 1 AND stock <= 10
            ORDER BY stock ASC
            LIMIT 10
        ")->fetchAll();
        
        // Sales chart data (last 30 days)
        $salesChart = $this->orderModel->query("
            SELECT date(created_at) as date, COALESCE(SUM(total), 0) as total
            FROM orders
            WHERE status != 'cancelled' 
            AND created_at >= date('now', '-30 days')
            GROUP BY date(created_at)
            ORDER BY date ASC
        ")->fetchAll();
        
        // Fill missing dates with 0
        $labels = [];
        $data = [];
        $chartData = [];
        foreach ($salesChart as $row) {
            $chartData[$row['date']] = (float)$row['total'];
        }
        
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $labels[] = date('d/m', strtotime($date));
            $data[] = $chartData[$date] ?? 0;
        }
        
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/dashboard', [
            'title' => 'Panel de administración',
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'salesChart' => ['labels' => $labels, 'data' => $data],
            'settings' => $settings
        ]);
    }
    
    // Products
    public function products(int $page = 1): void
    {
        $perPage = 20;
        
        // Build filters from GET parameters
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int)$_GET['category'] : null,
            'status' => isset($_GET['status']) && $_GET['status'] !== '' ? (int)$_GET['status'] : null,
            'stock' => $_GET['stock'] ?? null,
            'featured' => isset($_GET['featured']) && $_GET['featured'] !== '' ? (int)$_GET['featured'] : null,
            'sort' => $this->getProductSortOrder($_GET['sort'] ?? ''),
        ];
        
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');
        
        $result = $this->productModel->getActive($page, $perPage, $filters);
        
        $this->view('admin/products/index', [
            'title' => 'Gestión de productos',
            'products' => $result['data'],
            'pagination' => $result,
            'settings' => $this->settingModel->getAll()
        ]);
    }
    
    public function bulkProducts(): void
    {
        $action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids) || empty($action)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'No se seleccionaron productos o acción inválida']);
            $this->redirect(AppConfig::url('/admin/productos'));
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $params = $ids;
        
        try {
            switch ($action) {
                case 'activate':
                    $this->productModel->query("UPDATE products SET is_active = 1 WHERE id IN ($placeholders)", $params);
                    $msg = count($ids) . ' producto(s) activado(s)';
                    break;
                case 'deactivate':
                    $this->productModel->query("UPDATE products SET is_active = 0 WHERE id IN ($placeholders)", $params);
                    $msg = count($ids) . ' producto(s) desactivado(s)';
                    break;
                case 'feature':
                    $this->productModel->query("UPDATE products SET is_featured = 1 WHERE id IN ($placeholders)", $params);
                    $msg = count($ids) . ' producto(s) marcado(s) como destacado(s)';
                    break;
                case 'unfeature':
                    $this->productModel->query("UPDATE products SET is_featured = 0 WHERE id IN ($placeholders)", $params);
                    $msg = count($ids) . ' producto(s) quitado(s) de destacados';
                    break;
                case 'delete':
                    $this->productModel->query("DELETE FROM products WHERE id IN ($placeholders)", $params);
                    $msg = count($ids) . ' producto(s) eliminado(s)';
                    break;
                default:
                    throw new \Exception('Acción no válida');
            }
            
            Session::flash('flash', ['type' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    private function getProductSortOrder(string $sort): string
    {
        $options = [
            'newest' => 'id DESC',
            'oldest' => 'id ASC',
            'name_asc' => 'name ASC',
            'name_desc' => 'name DESC',
            'price_asc' => 'COALESCE(sale_price, price) ASC',
            'price_desc' => 'COALESCE(sale_price, price) DESC',
            'stock_asc' => 'stock ASC',
            'stock_desc' => 'stock DESC',
        ];
        return $options[$sort] ?? 'id DESC';
    }
    
    public function createProduct(): void
    {
        $categories = $this->categoryModel->getActive();
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/products/create', [
            'title' => 'Nuevo producto',
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function storeProduct(): void
    {
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'sku' => trim($_POST['sku'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageValidation = $this->validateImageUpload($_FILES['image']);
            if ($imageValidation !== true) {
                $errors[] = $imageValidation;
            } else {
                $uploadDir = __DIR__ . '/../../public/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $allowedExt)) {
                    $ext = 'jpg';
                }
                $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    $data['image'] = '/images/products/' . $filename;
                }
            }
        }
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['slug'])) $errors[] = 'El slug es requerido';
        if ($data['price'] <= 0) $errors[] = 'El precio debe ser mayor a 0';
        if ($data['category_id'] <= 0) $errors[] = 'Debe seleccionar una categoría';
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $this->productModel->create($data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto creado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    /**
     * Validate image upload
     * @return true|string
     */
    private function validateImageUpload(array $file): bool|string
    {
        // Check file size (max 5MB)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return 'La imagen no debe exceder 5MB';
        }
        
        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedMimes)) {
            return 'Formato de imagen no válido. Use JPG, PNG o WebP';
        }
        
        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowedExt)) {
            return 'Extensión de archivo no permitida';
        }
        
        // Check if it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return 'El archivo no es una imagen válida';
        }
        
        return true;
    }
    
    public function editProduct(int $id): void
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect(AppConfig::url('/admin/productos'));
        }
        
        $categories = $this->categoryModel->getActive();
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/products/edit', [
            'title' => 'Editar producto: ' . $product['name'],
            'product' => $product,
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function updateProduct(int $id): void
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect(AppConfig::url('/admin/productos'));
        }
        
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'sku' => trim($_POST['sku'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageValidation = $this->validateImageUpload($_FILES['image']);
            if ($imageValidation !== true) {
                $errors[] = $imageValidation;
            } else {
                $uploadDir = __DIR__ . '/../../public/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $allowedExt)) {
                    $ext = 'jpg';
                }
                $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filepath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                    $data['image'] = '/images/products/' . $filename;
                }
            }
        }
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['slug'])) $errors[] = 'El slug es requerido';
        if ($data['price'] <= 0) $errors[] = 'El precio debe ser mayor a 0';
        if ($data['category_id'] <= 0) $errors[] = 'Debe seleccionar una categoría';
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $this->productModel->update($id, $data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto actualizado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    public function deleteProduct(int $id): void
    {
        $this->productModel->delete($id);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto eliminado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    // Categories
    public function categories(): void
    {
        $categories = $this->categoryModel->all();
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/categories/index', [
            'title' => 'Gestión de categorías',
            'categories' => $categories,
            'settings' => $settings
        ]);
    }
    
    public function storeCategory(): void
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        if (empty($data['name']) || empty($data['slug'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Nombre y slug son requeridos']);
            $this->back();
        }
        
        $this->categoryModel->create($data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría creada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    public function updateCategory(int $id): void
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        if (empty($data['name']) || empty($data['slug'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Nombre y slug son requeridos']);
            $this->back();
        }
        
        $this->categoryModel->update($id, $data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría actualizada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    public function deleteCategory(int $id): void
    {
        $this->categoryModel->delete($id);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría eliminada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    // Orders
    public function orders(int $page = 1, string $status = ''): void
    {
        // Build filters from GET parameters
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort' => $this->getOrderSortOrder($_GET['sort'] ?? ''),
        ];
        
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');
        
        $result = $this->orderModel->getAll($page, 20, $filters);
        
        $this->view('admin/orders/index', [
            'title' => 'Gestión de pedidos',
            'orders' => $result['data'],
            'pagination' => $result,
            'currentStatus' => $status,
            'settings' => $this->settingModel->getAll()
        ]);
    }
    
    public function bulkOrders(): void
    {
        $action = $_POST['action'] ?? '';
        $ids = $_POST['ids'] ?? [];
        
        if (empty($ids) || empty($action)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'No se seleccionaron pedidos o acción inválida']);
            $this->redirect(AppConfig::url('/admin/pedidos'));
        }
        
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($action, $validStatuses)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Estado no válido']);
            $this->redirect(AppConfig::url('/admin/pedidos'));
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $params = array_merge([$action], $ids);
        
        try {
            $this->orderModel->query(
                "UPDATE orders SET status = ? WHERE id IN ($placeholders)",
                $params
            );
            
            $statusLabels = [
                'pending' => 'Pendientes',
                'processing' => 'Procesando',
                'shipped' => 'Enviados',
                'delivered' => 'Entregados',
                'cancelled' => 'Cancelados'
            ];
            
            $msg = count($ids) . ' pedido(s) marcado(s) como ' . $statusLabels[$action];
            Session::flash('flash', ['type' => 'success', 'message' => $msg]);
        } catch (\Exception $e) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        
        $this->redirect(AppConfig::url('/admin/pedidos'));
    }
    
    private function getOrderSortOrder(string $sort): string
    {
        $options = [
            'newest' => 'created_at DESC',
            'oldest' => 'created_at ASC',
            'total_desc' => 'total DESC',
            'total_asc' => 'total ASC',
        ];
        return $options[$sort] ?? 'created_at DESC';
    }
    
    public function orderDetail(int $id): void
    {
        $order = $this->orderModel->findById($id);
        
        if (!$order) {
            $this->redirect(AppConfig::url('/admin/pedidos'));
        }
        
        $items = $this->orderModel->getItems($id);
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/orders/detail', [
            'title' => 'Pedido #' . $id,
            'order' => $order,
            'items' => $items,
            'settings' => $settings
        ]);
    }
    
    public function updateOrderStatus(int $id): void
    {
        $status = $_POST['status'] ?? '';
        
        try {
            $this->orderModel->updateStatus($id, $status);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    // User Management
    public function users(int $page = 1): void
    {
        $perPage = 20;
        
        // Build filters from GET parameters
        $filters = [
            'q' => trim($_GET['q'] ?? ''),
            'role' => $_GET['role'] ?? '',
            'status' => $_GET['status'] ?? '',
        ];
        
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');
        
        // Get users with pagination
        $whereConditions = [];
        $params = [];
        
        if (!empty($filters['q'])) {
            $searchTerm = "%{$filters['q']}%";
            $whereConditions[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['role'])) {
            $whereConditions[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $whereConditions[] = $filters['status'] === 'active' ? "is_active = 1" : "is_active = 0";
        }
        
        $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $offset = ($page - 1) * $perPage;
        
        // Count total
        $total = (int)$this->userModel->query(
            "SELECT COUNT(*) as count FROM users {$whereClause}",
            $params
        )->fetch()['count'];
        
        // Get users with stats
        $paramsWithLimit = array_merge($params, [$perPage, $offset]);
        $users = $this->userModel->query(
            "SELECT u.*, 
                (SELECT COUNT(*) FROM orders WHERE user_id = u.id AND status != 'cancelled') as orders_count,
                (SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = u.id AND status != 'cancelled') as total_spent
             FROM users u 
             {$whereClause} 
             ORDER BY u.created_at DESC 
             LIMIT ? OFFSET ?",
            $paramsWithLimit
        )->fetchAll();
        
        $pagination = [
            'data' => $users,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
        
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/users/index', [
            'title' => 'Gestión de usuarios',
            'users' => $users,
            'pagination' => $pagination,
            'current_user_id' => Session::getUserId(),
            'settings' => $settings
        ]);
    }
    
    public function userDetail(int $id): void
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $this->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
            }
            $this->redirect(AppConfig::url('/admin/usuarios'));
        }
        
        $orders = $this->orderModel->getUserOrders($id, 1, 10);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $settings = $this->settingModel->getAll();
            $this->view('admin/users/detail', [
                'title' => 'Usuario #' . $id,
                'user' => $user,
                'orders' => $orders['data'],
                'settings' => $settings
            ]);
        } else {
            $settings = $this->settingModel->getAll();
            $this->view('admin/users/detail', [
                'title' => 'Usuario #' . $id,
                'user' => $user,
                'orders' => $orders['data'],
                'settings' => $settings
            ]);
        }
    }
    
    public function editUser(int $id): void
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->redirect(AppConfig::url('/admin/usuarios'));
        }
        
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/users/edit', [
            'title' => 'Editar Usuario',
            'user' => $user,
            'settings' => $settings
        ]);
    }
    
    public function updateUser(int $id): void
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Usuario no encontrado']);
            $this->redirect(AppConfig::url('/admin/usuarios'));
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role' => $_POST['role'] ?? 'customer',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        $errors = [];
        
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
        if (!in_array($data['role'], ['admin', 'customer'])) $errors[] = 'Rol no válido';
        
        // Check email uniqueness
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing && $existing['id'] != $id) {
            $errors[] = 'El email ya está en uso';
        }
        
        // Password change
        if (!empty($_POST['password'])) {
            $password = $_POST['password'];
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            $passwordErrors = $this->validatePasswordStrength($password);
            if (!empty($passwordErrors)) {
                $errors = array_merge($errors, $passwordErrors);
            }
            
            if ($password !== $passwordConfirm) {
                $errors[] = 'Las contraseñas no coinciden';
            }
            
            $data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $this->userModel->update($id, $data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Usuario actualizado correctamente']);
        $this->redirect(AppConfig::url('/admin/usuarios'));
    }
    
    public function deleteUser(int $id): void
    {
        $currentUserId = Session::getUserId();
        
        if ($id == $currentUserId) {
            Session::flash('flash', ['type' => 'error', 'message' => 'No puedes eliminarte a ti mismo']);
            $this->redirect(AppConfig::url('/admin/usuarios'));
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Usuario no encontrado']);
            $this->redirect(AppConfig::url('/admin/usuarios'));
        }
        
        // Delete user (orders will be handled by foreign key or cascade)
        $this->userModel->delete($id);
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Usuario eliminado correctamente']);
        $this->redirect(AppConfig::url('/admin/usuarios'));
    }
    
    // Settings
    public function settings(): void
    {
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/settings/index', [
            'title' => 'Configuración',
            'settings' => $settings
        ]);
    }
    
    public function updateSettings(): void
    {
        $keys = ['store_name', 'store_email', 'store_phone', 'store_address', 'currency', 'tax_rate', 'shipping_cost', 'free_shipping_threshold'];
        
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $this->settingModel->set($key, trim($_POST[$key]));
            }
        }
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Configuración guardada correctamente']);
        $this->redirect(AppConfig::url('/admin/configuracion'));
    }
}