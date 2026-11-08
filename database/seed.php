<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nicastore\Core\Database;
use Nicastore\Config\DatabaseConfig;

$pdo = DatabaseConfig::getConnection();

// Check if data already exists
$existingUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
if ($existingUsers > 0) {
    echo "Sample data already exists. Skipping seeder.\n";
    exit;
}

$passwordHash = password_hash('password123', PASSWORD_BCRYPT, ['cost' => 12]);

// Insert admin user
$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password, role, created_at, updated_at)
    VALUES (?, ?, ?, ?, datetime('now'), datetime('now'))
");
$stmt->execute(['Admin User', 'admin@nicastore.com', $passwordHash, 'admin']);

// Insert test customer
$stmt->execute(['Test Customer', 'customer@nicastore.com', $passwordHash, 'customer']);

// Insert categories
$categories = [
    ['Laptops', 'laptops', 'Portátiles, ultrabooks y notebooks de las mejores marcas', null],
    ['Smartphones', 'smartphones', 'Teléfonos inteligentes Android e iOS', null],
    ['Componentes', 'componentes', 'Procesadores, tarjetas gráficas, RAM, placas base', null],
    ['Periféricos', 'perifericos', 'Teclados, ratones, alfombrillas, webcams', null],
    ['Audio', 'audio', 'Auriculares, altavoces, micrófonos, interfaces', null],
    ['Gaming', 'gaming', 'Consolas, sillas gaming, monitores, accesorios', null],
    ['Almacenamiento', 'almacenamiento', 'SSD, HDD, USB, tarjetas de memoria, NAS', null],
    ['Redes', 'redes', 'Routers, switches, extensores WiFi, cables', null],
    ['Accesorios', 'accesorios', 'Fundas, cables, cargadores, adaptadores', null],
    ['Monitores', 'monitores', 'Monitores gaming, profesionales, ultrawide', null],
];

$stmt = $pdo->prepare("
    INSERT INTO categories (name, slug, description, image, is_active, created_at, updated_at)
    VALUES (?, ?, ?, ?, 1, datetime('now'), datetime('now'))
");

foreach ($categories as $cat) {
    $stmt->execute($cat);
}

// Insert products
$products = [
    // Laptops (cat 1)
    [1, 'MacBook Air M2', 'macbook-air-m2', 'Apple MacBook Air con chip M2, 13.6", 8GB RAM, 256GB SSD', 'Laptop ultraligera con chip M2', 1199.00, 1099.00, 'MB-AIR-M2-256', 15, null, '["/placeholder1.jpg"]', 1, 1],
    [1, 'Dell XPS 13', 'dell-xps-13', 'Dell XPS 13 9320, Intel i7-1250U, 16GB RAM, 512GB SSD, 13.4" FHD+', 'Ultrabook premium Windows', 1299.00, null, 'DELL-XPS13-512', 10, null, '["/placeholder2.jpg"]', 1, 0],
    [1, 'ASUS ROG Zephyrus G14', 'asus-rog-zephyrus-g14', 'AMD Ryzen 9 7940HS, RTX 4060, 16GB, 1TB SSD, 14" QHD 165Hz', 'Laptop gaming potente y portable', 1599.00, 1449.00, 'ROG-G14-RTX4060', 8, null, '["/placeholder3.jpg"]', 1, 1],
    [1, 'Lenovo ThinkPad X1 Carbon', 'lenovo-thinkpad-x1-carbon', 'Intel i7-1355U, 32GB RAM, 1TB SSD, 14" 2.8K OLED', 'Laptop empresarial ultraligera', 1899.00, 1749.00, 'THINKPAD-X1C-G11', 5, null, '["/placeholder4.jpg"]', 1, 1],
    
    // Smartphones (cat 2)
    [2, 'iPhone 15 Pro', 'iphone-15-pro', 'Apple iPhone 15 Pro 128GB, Chip A17 Pro, Pantalla 6.1" Super Retina XDR', 'Smartphone flagship Apple', 999.00, null, 'IPHONE15PRO-128', 25, null, '["/placeholder5.jpg"]', 1, 1],
    [2, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Samsung Galaxy S24 Ultra 256GB, Snapdragon 8 Gen 3, 6.8" QHD+ 120Hz', 'Android flagship con S Pen', 1199.00, 1099.00, 'S24ULTRA-256', 20, null, '["/placeholder6.jpg"]', 1, 1],
    [2, 'Google Pixel 8 Pro', 'google-pixel-8-pro', 'Google Pixel 8 Pro 128GB, Tensor G3, 6.7" LTPO OLED 120Hz', 'Mejor cámara computacional', 849.00, 799.00, 'PIXEL8PRO-128', 15, null, '["/placeholder7.jpg"]', 1, 0],
    [2, 'Xiaomi 14 Ultra', 'xiaomi-14-ultra', 'Xiaomi 14 Ultra 512GB, Snapdragon 8 Gen 3, Leica Quad Camera', 'Fotografía móvil profesional', 999.00, null, 'XIAOMI14U-512', 12, null, '["/placeholder8.jpg"]', 1, 1],
    
    // Componentes (cat 3)
    [3, 'AMD Ryzen 7 7800X3D', 'amd-ryzen-7-7800x3d', 'Procesador 8 núcleos, 16 hilos, 96MB L3 Cache, AM5', 'Mejor CPU gaming actual', 449.00, 419.00, 'RYZEN-7800X3D', 30, null, '["/placeholder9.jpg"]', 1, 1],
    [3, 'Intel Core i7-14700K', 'intel-core-i7-14700k', 'Procesador 20 núcleos (8P+12E), 28 hilos, LGA1700', 'Rendimiento híbrido extremo', 429.00, null, 'I7-14700K', 25, null, '["/placeholder10.jpg"]', 1, 0],
    [3, 'NVIDIA RTX 4080 Super', 'nvidia-rtx-4080-super', 'Tarjeta gráfica 16GB GDDR6X, 10240 CUDA cores, DLSS 3.5', 'Gaming 4K y creación', 1099.00, null, 'RTX4080S-16G', 8, null, '["/placeholder11.jpg"]', 1, 1],
    [3, 'Corsair Vengeance 32GB DDR5-6000', 'corsair-vengeance-32gb-ddr5', 'Kit 2x16GB DDR5 6000MHz CL36, Intel XMP 3.0 / AMD EXPO', 'RAM gaming alta velocidad', 149.00, 129.00, 'CMK32GX5M2B6000C36', 40, null, '["/placeholder12.jpg"]', 1, 0],
    
    // Periféricos (cat 4)
    [4, 'Logitech MX Master 3S', 'logitech-mx-master-3s', 'Ratón ergonómico inalámbrico, 8000 DPI, MagSpeed scrolling', 'Productividad profesional', 99.00, 89.00, 'MX-MASTER-3S', 50, null, '["/placeholder13.jpg"]', 1, 1],
    [4, 'Keychron K2 Pro', 'keychron-k2-pro', 'Teclado mecánico 75% inalámbrico, switches Gateron G Pro, QMK/VIA', 'Teclado para desarrolladores', 89.00, null, 'KEYCHRON-K2-PRO', 35, null, '["/placeholder14.jpg"]', 1, 1],
    [4, 'Logitech Brio 4K', 'logitech-brio-4k', 'Webcam 4K UHD 30fps / 1080p 60fps, HDR, Windows Hello', 'Videoconferencia profesional', 169.00, 149.00, 'BRIO-4K', 25, null, '["/placeholder15.jpg"]', 1, 0],
    [4, 'Razer DeathAdder V3 Pro', 'razer-deathadder-v3-pro', 'Ratón gaming ultraligero 63g, Focus Pro 30K, 90h batería', 'Esports competitivo', 159.00, null, 'DEATHADDER-V3P', 20, null, '["/placeholder16.jpg"]', 1, 1],
    
    // Audio (cat 5)
    [5, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'Auriculares inalámbricos con cancelación de ruido líder, 30h batería', 'Mejor ANC del mercado', 399.00, 349.00, 'WH1000XM5', 30, null, '["/placeholder17.jpg"]', 1, 1],
    [5, 'AirPods Pro 2', 'airpods-pro-2', 'Auriculares TWS con ANC adaptativo, Audio Espacial, USB-C', 'Ecosistema Apple perfecto', 249.00, 229.00, 'AIRPODS-PRO2', 40, null, '["/placeholder18.jpg"]', 1, 1],
    [5, 'SteelSeries Arctis Nova Pro', 'steelseries-arctis-nova-pro', 'Auriculares gaming Hi-Fi, DAC GameDAC Gen 2, 360° Audio Espacial', 'Audio gaming premium', 349.00, null, 'ARCTIS-NOVA-PRO', 15, null, '["/placeholder19.jpg"]', 1, 0],
    [5, 'JBL Charge 5', 'jbl-charge-5', 'Altavoz portátil Bluetooth, 40W, 20h batería, Powerbank, IP67', 'Fiesta en cualquier lugar', 179.00, 159.00, 'CHARGE-5', 35, null, '["/placeholder20.jpg"]', 1, 1],
    
    // Gaming (cat 6)
    [6, 'PlayStation 5 Slim', 'playstation-5-slim', 'Consola PS5 Slim 1TB, DualSense, 4K 120fps, Ray Tracing', 'Nueva generación gaming', 499.00, null, 'PS5-SLIM-1TB', 20, null, '["/placeholder21.jpg"]', 1, 1],
    [6, 'Xbox Series X', 'xbox-series-x', 'Consola Xbox Series X 1TB, 4K 120fps, Quick Resume, Game Pass', 'Potencia bruta Microsoft', 499.00, null, 'XBOX-SERIES-X', 18, null, '["/placeholder22.jpg"]', 1, 0],
    [6, 'Secretlab Titan Evo 2024', 'secretlab-titan-evo-2024', 'Silla gaming ergonómica, lumbar 4D, magnet headrest, SoftWeave Plus', 'Comodidad sesiones largas', 549.00, 499.00, 'TITAN-EVO-2024', 10, null, '["/placeholder23.jpg"]', 1, 1],
    [6, 'LG 27GP95R-B', 'lg-27gp95r-b', 'Monitor 27" 4K 144Hz Nano IPS, G-Sync, HDMI 2.1, DisplayHDR 600', 'Monitor gaming 4K alta refresco', 799.00, 699.00, 'LG-27GP95R', 8, null, '["/placeholder24.jpg"]', 1, 1],
    
    // Almacenamiento (cat 7)
    [7, 'Samsung 990 Pro 2TB', 'samsung-990-pro-2tb', 'SSD NVMe PCIe 4.0, 7450/6900 MB/s, disipador térmico', 'SSD gaming/creadores top', 189.00, 169.00, '990PRO-2TB', 40, null, '["/placeholder25.jpg"]', 1, 1],
    [7, 'WD Black SN850X 1TB', 'wd-black-sn850x-1tb', 'SSD NVMe PCIe 4.0, 7300/6600 MB/s, heatsink incluido', 'Rendimiento sostenido', 129.00, 109.00, 'SN850X-1TB', 35, null, '["/placeholder26.jpg"]', 1, 0],
    [7, 'Seagate Expansion 18TB', 'seagate-expansion-18tb', 'Disco duro externo 18TB USB 3.0, backup masivo', 'Almacenamiento masivo', 349.00, 319.00, 'EXPANSION-18TB', 15, null, '["/placeholder27.jpg"]', 1, 1],
    [7, 'SanDisk Extreme Pro 1TB', 'sandisk-extreme-pro-1tb', 'SSD portátil USB 3.2 Gen 2x2, 2000 MB/s, IP55', 'Velocidad en movimiento', 179.00, 159.00, 'EXTREME-PRO-1TB', 25, null, '["/placeholder28.jpg"]', 1, 0],
    
    // Redes (cat 8)
    [8, 'ASUS RT-AX88U Pro', 'asus-rt-ax88u-pro', 'Router WiFi 6 dual-band, 6000 Mbps, 8 antenas, 2.5G WAN/LAN', 'Red doméstica avanzada', 299.00, 279.00, 'RT-AX88U-PRO', 12, null, '["/placeholder29.jpg"]', 1, 1],
    [8, 'TP-Link Deco XE75 Pro', 'tp-link-deco-xe75-pro', 'Mesh WiFi 6E tri-band AXE5400, 2.5G puertos, cobertura 650m²', 'WiFi toda la casa', 399.00, 349.00, 'DECO-XE75-PRO', 10, null, '["/placeholder30.jpg"]', 1, 1],
    [8, 'Ubiquiti UniFi Switch 24 PoE', 'ubiquiti-unifi-switch-24-poe', 'Switch gestionado 24 puertos Gigabit, 16 PoE+, SFP+, 195W', 'Red empresarial', 449.00, null, 'USW-24-POE', 8, null, '["/placeholder31.jpg"]', 1, 0],
    [8, 'Cat6a Cable Ethernet 10m', 'cat6a-cable-ethernet-10m', 'Cable red Cat6a blindado 10m, 10 Gbps, conectores oro', 'Conexión cableada fiable', 14.99, 11.99, 'CAT6A-10M', 100, null, '["/placeholder32.jpg"]', 1, 1],
    
    // Accesorios (cat 9)
    [9, 'Anker 737 Power Bank', 'anker-737-power-bank', 'Powerbank 24000mAh 140W, 2x USB-C, 1x USB-A, pantalla inteligente', 'Carga portátil máxima', 149.00, 129.00, 'ANKER-737-24K', 30, null, '["/placeholder33.jpg"]', 1, 1],
    [9, 'Satechi 165W USB-C GaN', 'satechi-165w-usbc-gan', 'Cargador 4 puertos USB-C GaN, 165W total, distribución inteligente', 'Un cargador para todo', 119.00, 99.00, 'SATECHI-165W', 25, null, '["/placeholder34.jpg"]', 1, 0],
    [9, 'Twelve South BookArc', 'twelve-south-bookarc', 'Soporte vertical MacBook, aluminio, compatible M1/M2/M3', 'Escritorio minimalista', 59.00, null, 'BOOKARC-MAC', 20, null, '["/placeholder35.jpg"]', 1, 1],
    [9, 'Belkin USB-C Hub 7-en-1', 'belkin-usbc-hub-7en1', 'Hub USB-C 7 puertos: HDMI 4K, USB-C PD, 2x USB-A, SD, microSD', 'Conectividad completa', 79.00, 69.00, 'BELKIN-HUB-7IN1', 40, null, '["/placeholder36.jpg"]', 1, 1],
    
    // Monitores (cat 10)
    [10, 'Dell UltraSharp U2723QE', 'dell-ultrasharp-u2723qe', 'Monitor 27" 4K IPS Black, USB-C 90W, KVM, 98% DCI-P3', 'Productividad color precisa', 549.00, 499.00, 'U2723QE', 15, null, '["/placeholder37.jpg"]', 1, 1],
    [10, 'Samsung Odyssey G9', 'samsung-odyssey-g9', 'Monitor 49" DQHD 1000R, 240Hz, QLED, G-Sync, HDR1000', 'Inmersión gaming extrema', 1299.00, 1099.00, 'ODYSSEY-G9', 5, null, '["/placeholder38.jpg"]', 1, 1],
    [10, 'LG UltraGear 27GR95QE', 'lg-ultragear-27gr95qe', 'Monitor 27" OLED 240Hz QHD, 0.03ms, G-Sync, 98.5% DCI-P3', 'OLED gaming competitivo', 799.00, 699.00, '27GR95QE', 10, null, '["/placeholder39.jpg"]', 1, 0],
    [10, 'BenQ PD2705U', 'benq-pd2705u', 'Monitor 27" 4K USB-C 90W, AQCOLOR, KVM, Daisy Chain', 'Diseño y desarrollo', 449.00, 399.00, 'PD2705U', 12, null, '["/placeholder40.jpg"]', 1, 1],
];

$stmt = $pdo->prepare("
    INSERT INTO products (category_id, name, slug, description, short_description, price, sale_price, sku, stock, image, images, is_active, is_featured, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
");

foreach ($products as $product) {
    $stmt->execute($product);
}

// Insert sample coupons
$coupons = [
    ['WELCOME10', 'percentage', 10.00, 50.00, 50.00, 100, 0],
    ['NIKASTORE20', 'percentage', 15.00, 200.00, 100.00, 50, 0],
    ['TECH100', 'fixed', 100.00, 500.00, null, 20, 0],
    ['FREESHIP', 'fixed', 5.00, 0.00, null, 200, 0],
];

$stmt = $pdo->prepare("
    INSERT INTO coupons (code, type, value, min_order_amount, max_discount, usage_limit, used_count, is_active, created_at, updated_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1, datetime('now'), datetime('now'))
");

foreach ($coupons as $coupon) {
    $stmt->execute($coupon);
}

// Insert settings
$settings = [
    ['store_name', 'Nicastore'],
    ['store_email', 'info@nicastore.com.ni'],
    ['store_phone', '+505 2270-XXXX'],
    ['store_address', 'Km 4.5 Carretera a Masaya, Managua, Nicaragua'],
    ['currency', 'USD'],
    ['tax_rate', '15'],
    ['shipping_cost', '5.00'],
    ['free_shipping_threshold', '50.00'],
];

$stmt = $pdo->prepare("
    INSERT INTO settings (key, value, created_at, updated_at)
    VALUES (?, ?, datetime('now'), datetime('now'))
");

foreach ($settings as $setting) {
    $stmt->execute($setting);
}

echo "Sample data inserted successfully!\n";