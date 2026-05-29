<?php
class ProductController {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /* =====================================================
       قائمة المنتجات
    ===================================================== */
    public function index(): void {
        Auth::requirePermission('products');

        // متغيرات الـ Layout — يجب تعريفها أولاً
        $page   = 'products';
        $action = 'index';

        $search      = Helper::sanitize($_GET['search'] ?? '');
        $cat         = (int)($_GET['cat'] ?? 0);
        $currentPage = max(1, (int)($_GET['p'] ?? 1));   // ← اسم مختلف عن $page
        $perPage     = 20;
        $offset      = ($currentPage - 1) * $perPage;

        $where  = "WHERE p.is_active = 1";
        $params = [];
        if ($search) {
            $where   .= " AND (p.name LIKE ? OR p.barcode LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        if ($cat) {
            $where   .= " AND p.category_id = ?";
            $params[] = $cat;
        }

        $total      = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM products p {$where}", $params);
        $products   = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             {$where}
             ORDER BY p.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $totalPages = (int)ceil($total / $perPage);
        $settings   = Helper::getSettings();

        require ROOT . '/app/Views/layouts/main.php';
    }

    /* =====================================================
       نموذج إضافة منتج جديد
    ===================================================== */
    public function create(): void {
        Auth::requirePermission('products');

        $page   = 'products';
        $action = 'create';

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $settings   = Helper::getSettings();
        $product    = null;   // لا يوجد منتج عند الإضافة

        require ROOT . '/app/Views/layouts/main.php';
    }

    /* =====================================================
       حفظ منتج جديد
    ===================================================== */
    public function store(): void {
        Auth::requirePermission('products');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('?page=products');
        }

        $name      = Helper::sanitize($_POST['name'] ?? '');
        $salePrice = (float)($_POST['sale_price'] ?? 0);

        if (empty($name)) {
            Session::flash('error', 'اسم المنتج مطلوب');
            Helper::redirect('?page=products&action=create');
        }
        if ($salePrice <= 0) {
            Session::flash('error', 'سعر البيع يجب أن يكون أكبر من صفر');
            Helper::redirect('?page=products&action=create');
        }

        $data = [
            'name'            => $name,
            'barcode'         => Helper::sanitize($_POST['barcode'] ?? '') ?: null,
            'category_id'     => (int)($_POST['category_id'] ?? 0) ?: null,
            'unit'            => Helper::sanitize($_POST['unit'] ?? 'قطعة'),
            'purchase_price'  => (float)($_POST['purchase_price'] ?? 0),
            'sale_price'      => $salePrice,
            'stock_qty'       => (float)($_POST['stock_qty'] ?? 0),
            'min_stock_alert' => (float)($_POST['min_stock_alert'] ?? 5),
            'expiry_date'     => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
            'description'     => Helper::sanitize($_POST['description'] ?? ''),
            'is_active'       => 1,
            'created_by'      => Auth::id(),
        ];

        // رفع الصورة
        if (!empty($_FILES['image']['name'])) {
            $img = Helper::uploadImage($_FILES['image'], 'products');
            if ($img) $data['image'] = $img;
        }

        $id = $this->db->insert('products', $data);

        // تسجيل حركة مخزون أولية
        if ($data['stock_qty'] > 0) {
            $this->db->insert('stock_movements', [
                'product_id'     => $id,
                'type'           => 'in',
                'quantity'       => $data['stock_qty'],
                'before_qty'     => 0,
                'after_qty'      => $data['stock_qty'],
                'reference_type' => 'initial',
                'user_id'        => Auth::id(),
                'notes'          => 'كمية افتتاحية عند إضافة المنتج',
            ]);
        }

        Session::flash('success', 'تمت إضافة المنتج «' . $name . '» بنجاح');
        Helper::redirect('?page=products');
    }

    /* =====================================================
       نموذج تعديل منتج موجود
    ===================================================== */
    public function edit(): void {
        Auth::requirePermission('products');

        $page   = 'products';
        $action = 'edit';

        $id      = (int)($_GET['id'] ?? 0);
        $product = $this->db->fetchOne("SELECT * FROM products WHERE id = ? AND is_active = 1", [$id]);
        if (!$product) {
            Session::flash('error', 'المنتج غير موجود');
            Helper::redirect('?page=products');
        }

        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $settings   = Helper::getSettings();

        require ROOT . '/app/Views/layouts/main.php';
    }

    /* =====================================================
       حفظ تعديلات منتج
    ===================================================== */
    public function update(): void {
        Auth::requirePermission('products');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helper::redirect('?page=products');
        }

        $id        = (int)($_POST['id'] ?? 0);
        $name      = Helper::sanitize($_POST['name'] ?? '');
        $salePrice = (float)($_POST['sale_price'] ?? 0);

        if (empty($name) || $salePrice <= 0) {
            Session::flash('error', 'اسم المنتج وسعر البيع مطلوبان');
            Helper::redirect('?page=products&action=edit&id=' . $id);
        }

        $data = [
            'name'            => $name,
            'barcode'         => Helper::sanitize($_POST['barcode'] ?? '') ?: null,
            'category_id'     => (int)($_POST['category_id'] ?? 0) ?: null,
            'unit'            => Helper::sanitize($_POST['unit'] ?? 'قطعة'),
            'purchase_price'  => (float)($_POST['purchase_price'] ?? 0),
            'sale_price'      => $salePrice,
            'min_stock_alert' => (float)($_POST['min_stock_alert'] ?? 5),
            'expiry_date'     => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
            'description'     => Helper::sanitize($_POST['description'] ?? ''),
        ];

        if (!empty($_FILES['image']['name'])) {
            $img = Helper::uploadImage($_FILES['image'], 'products');
            if ($img) $data['image'] = $img;
        }

        $this->db->update('products', $data, 'id = ?', [$id]);
        Session::flash('success', 'تم تحديث المنتج «' . $name . '» بنجاح');
        Helper::redirect('?page=products');
    }

    /* =====================================================
       حذف منتج (تعطيل)
    ===================================================== */
    public function delete(): void {
        Auth::requirePermission('products');
        $id      = (int)($_GET['id'] ?? 0);
        $product = $this->db->fetchOne("SELECT name FROM products WHERE id = ?", [$id]);
        if ($product) {
            $this->db->update('products', ['is_active' => 0], 'id = ?', [$id]);
            Session::flash('success', 'تم حذف المنتج «' . $product['name'] . '»');
        }
        Helper::redirect('?page=products');
    }

    /* =====================================================
       API: تعديل المخزون يدوياً
    ===================================================== */
    public function adjustStock(): void {
        Auth::requirePermission('inventory');
        $data      = json_decode(file_get_contents('php://input'), true);
        $productId = (int)($data['product_id'] ?? 0);
        $qty       = (float)($data['quantity'] ?? 0);
        $type      = $data['type'] ?? 'in';

        $product = $this->db->fetchOne("SELECT stock_qty FROM products WHERE id = ?", [$productId]);
        if (!$product) {
            Helper::jsonResponse(['success' => false, 'message' => 'المنتج غير موجود']);
        }

        $before = (float)$product['stock_qty'];
        $after  = match ($type) {
            'adjustment' => $qty,
            'out'        => max(0, $before - $qty),
            default      => $before + $qty,
        };

        $this->db->update('products', ['stock_qty' => $after], 'id = ?', [$productId]);
        $this->db->insert('stock_movements', [
            'product_id'     => $productId,
            'type'           => $type,
            'quantity'       => abs($after - $before),
            'before_qty'     => $before,
            'after_qty'      => $after,
            'reference_type' => 'manual',
            'user_id'        => Auth::id(),
            'notes'          => $data['notes'] ?? '',
        ]);

        Helper::jsonResponse(['success' => true, 'new_qty' => $after]);
    }
}
