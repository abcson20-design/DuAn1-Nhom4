<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Coupon.php';

class AdminController extends BaseController {
    private function checkAuth(): void {
        // Tự động đồng bộ quyền nếu tài khoản đang đăng nhập có role = 'admin'
        if (empty($_SESSION['admin_user']) && !empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin') {
            $_SESSION['admin_user'] = $_SESSION['user'];
        }

        if (empty($_SESSION['admin_user']) || ($_SESSION['admin_user']['role'] ?? '') !== 'admin') {
            $this->redirect(BASE_URL . 'index.php?url=admin/login');
        }
    }

    private function createSlug(string $string): string {
        $search = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ',
            'À', 'Á', 'Ạ', 'Ả', 'Ã', 'Â', 'Ầ', 'Ấ', 'Ậ', 'Ẩ', 'Ẫ', 'Ă', 'Ằ', 'Ắ', 'Ặ', 'Ẳ', 'Ẵ',
            'È', 'É', 'Ẹ', 'Ẻ', 'Ẽ', 'Ê', 'Ề', 'Ế', 'Ệ', 'Ể', 'Ễ',
            'Ì', 'Í', 'Ị', 'Ỉ', 'Ĩ',
            'Ò', 'Ó', 'Ọ', 'Ỏ', 'Õ', 'Ô', 'Ồ', 'Ố', 'Ộ', 'Ổ', 'Ỗ', 'Ơ', 'Ờ', 'Ớ', 'Ợ', 'Ở', 'Ỡ',
            'Ù', 'Ú', 'Ụ', 'Ủ', 'Ũ', 'Ư', 'Ừ', 'Ứ', 'Ự', 'Ử', 'Ữ',
            'Ỳ', 'Ý', 'Ỵ', 'Ỷ', 'Ỹ',
            'Đ'
        ];
        $replace = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd',
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];
        $string = str_replace($search, $replace, $string);
        $string = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return trim($string, '-');
    }

    public function login(): void {
        if (!empty($_SESSION['admin_user'])) {
            $this->redirect(BASE_URL . 'index.php?url=admin');
        }
        if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin') {
            $_SESSION['admin_user'] = $_SESSION['user'];
            $this->redirect(BASE_URL . 'index.php?url=admin');
        }

        $error = '';
        $clientIp = RateLimiter::getClientIp();
        $ipLockTime = RateLimiter::isLocked('login_admin_ip', $clientIp);

        if ($ipLockTime !== null) {
            http_response_code(429);
            $wait = ceil($ipLockTime / 60);
            $error = "IP của bạn đã bị tạm khóa do nhập sai mật khẩu Quản trị quá 5 lần. Vui lòng quay lại sau {$wait} phút!";
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $accountKey = strtolower($email);
            $accountLockTime = RateLimiter::isLocked('login_admin_account', $accountKey);

            if ($accountLockTime !== null) {
                http_response_code(429);
                $wait = ceil($accountLockTime / 60);
                $error = "Tài khoản Quản trị \"{$email}\" đã bị tạm khóa 15 phút do nhập sai mật khẩu quá 5 lần. Vui lòng quay lại sau {$wait} phút!";
            } else {
                $userModel = new User();
                $user = $userModel->login($email, $password);

                if ($user && $user['role'] === 'admin') {
                    // Xóa đếm vi phạm cho cả tài khoản và IP
                    RateLimiter::clear('login_admin_account', $accountKey);
                    RateLimiter::clear('login_admin_ip', $clientIp);

                    // Chống Session Fixation
                    session_regenerate_id(true);

                    $_SESSION['admin_user'] = $user;
                    $_SESSION['user'] = $user;
                    $this->redirect(BASE_URL . 'index.php?url=admin');
                    return;
                } else {
                    $ipRes = RateLimiter::hit('login_admin_ip', $clientIp, 5, 900);
                    $accRes = RateLimiter::hit('login_admin_account', $accountKey, 5, 900);

                    if (!$accRes['allowed']) {
                        http_response_code(429);
                        $error = 'Tài khoản Quản trị này đã bị khóa 15 phút do nhập sai 5 lần liên tiếp!';
                    } elseif (!$ipRes['allowed']) {
                        http_response_code(429);
                        $error = 'Địa chỉ IP của bạn đã bị khóa 15 phút do thử sai quá 5 lần!';
                    } else {
                        $remaining = min($accRes['remaining'], $ipRes['remaining']);
                        $error = "Email hoặc mật khẩu không chính xác hoặc bạn không có quyền Quản trị! (Còn {$remaining} lần thử)";
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function logout(): void {
        unset($_SESSION['admin_user']);
        $this->redirect(BASE_URL . 'index.php?url=admin/login');
    }

    public function index(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $stats = $orderModel->getStats();
        $recentOrders = $orderModel->getAll(10);
        $contactModel = new Contact();
        $newContactsCount = $contactModel->countNew();
        $categoryModel = new Category();
        $categoriesCount = count($categoryModel->getAll());
        $brandModel = new Brand();
        $brandsCount = count($brandModel->getAll());

        require_once __DIR__ . '/../views/admin/index.php';
    }

    // TRANG THỐNG KÊ CHI TIẾT (ANALYTICS & REPORT)
    public function statistics(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $detailedStats = $orderModel->getDetailedStats();

        require_once __DIR__ . '/../views/admin/statistics.php';
    }

    // ==========================================
    // QUẢN LÝ SẢN PHẨM (PRODUCTS CRUD)
    // ==========================================
    public function products(): void {
        $this->checkAuth();
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $brandId = !empty($_GET['brand_id']) ? (int)$_GET['brand_id'] : null;
        $type = $_GET['type'] ?? 'all'; // 'all', 'phone', 'accessory'

        $filters = [];
        if ($keyword !== '') {
            $filters['keyword'] = $keyword;
        }
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        if ($brandId) {
            $filters['brand_id'] = $brandId;
        }
        if ($type === 'phone') {
            $filters['category_type'] = 'phone';
        } elseif ($type === 'accessory') {
            $filters['category_type'] = 'accessory';
        }

        $products = $productModel->searchAndFilter($filters, 200);

        // Đếm số lượng theo từng phân hệ để gắn huy hiệu số lượng trên các Tab
        $countAll = $productModel->countSearchAndFilter([]);
        $countPhones = $productModel->countSearchAndFilter(['category_type' => 'phone']);
        $countAccessories = $productModel->countSearchAndFilter(['category_type' => 'accessory']);
        $countTrash = count($productModel->getTrash());

        require_once __DIR__ . '/../views/admin/products.php';
    }

    /**
     * Tự động xóa nền ảnh (loại bỏ màu nền góc/viền, chuyển thành PNG trong suốt mượt mà)
     */
    public function removeBackgroundGD(string $sourcePath, string $destPath, int $tolerance = 38): bool {
        if (!file_exists($sourcePath) || !extension_loaded('gd')) {
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (!$info) return false;

        $mime = $info['mime'] ?? '';
        $src = null;
        switch ($mime) {
            case 'image/jpeg':
                $src = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $src = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $src = @imagecreatefromwebp($sourcePath);
                }
                break;
            case 'image/gif':
                $src = @imagecreatefromgif($sourcePath);
                break;
            default:
                $raw = @file_get_contents($sourcePath);
                if ($raw && function_exists('imagecreatefromstring')) {
                    $src = @imagecreatefromstring($raw);
                }
                break;
        }

        if (!$src) return false;

        $origW = imagesx($src);
        $origH = imagesy($src);

        // Kiểm tra góc (0,0) xem ảnh đã có kênh alpha trong suốt sẵn chưa
        $c0 = imagecolorat($src, 0, 0);
        $a0 = ($c0 >> 24) & 0x7F;
        if ($a0 > 100) {
            imagealphablending($src, false);
            imagesavealpha($src, true);
            $saved = @imagepng($src, $destPath, 6);
            imagedestroy($src);
            return $saved;
        }

        // Tối ưu kích thước tối đa 1400px để xử lý nhanh và tiết kiệm dung lượng
        $maxDim = 1400;
        if ($origW > $maxDim || $origH > $maxDim) {
            $scale = min($maxDim / $origW, $maxDim / $origH);
            $newW = (int)round($origW * $scale);
            $newH = (int)round($origH * $scale);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $resized;
            $w = $newW;
            $h = $newH;
        } else {
            $w = $origW;
            $h = $origH;
        }

        // Tạo ảnh đích hỗ trợ alpha channel
        $dst = imagecreatetruecolor($w, $h);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        // Lấy màu mẫu từ 4 góc ảnh
        $corners = [
            imagecolorat($src, 0, 0),
            imagecolorat($src, $w - 1, 0),
            imagecolorat($src, 0, $h - 1),
            imagecolorat($src, $w - 1, $h - 1)
        ];

        $avgR = 0; $avgG = 0; $avgB = 0;
        foreach ($corners as $c) {
            $avgR += ($c >> 16) & 0xFF;
            $avgG += ($c >> 8) & 0xFF;
            $avgB += $c & 0xFF;
        }
        $bgR = $avgR / 4.0;
        $bgG = $avgG / 4.0;
        $bgB = $avgB / 4.0;

        // BFS Flood Fill từ 4 cạnh để chỉ xóa nền bên ngoài
        $visited = array_fill(0, $w * $h, false);
        $queue = new SplQueue();

        for ($x = 0; $x < $w; $x++) {
            $queue->enqueue([$x, 0]);
            $queue->enqueue([$x, $h - 1]);
            $visited[0 * $w + $x] = true;
            $visited[($h - 1) * $w + $x] = true;
        }
        for ($y = 1; $y < $h - 1; $y++) {
            $queue->enqueue([0, $y]);
            $queue->enqueue([$w - 1, $y]);
            $visited[$y * $w + 0] = true;
            $visited[$y * $w + ($w - 1)] = true;
        }

        $transparentPixels = array_fill(0, $w * $h, 0); // 0 = giữ, 127 = trong suốt hoàn toàn
        $dx = [1, -1, 0, 0];
        $dy = [0, 0, 1, -1];

        while (!$queue->isEmpty()) {
            list($cx, $cy) = $queue->dequeue();
            $idx = $cy * $w + $cx;

            $c = imagecolorat($src, $cx, $cy);
            $r = ($c >> 16) & 0xFF;
            $g = ($c >> 8) & 0xFF;
            $b = $c & 0xFF;

            $dist = sqrt(pow($r - $bgR, 2) + pow($g - $bgG, 2) + pow($b - $bgB, 2));

            if ($dist <= $tolerance) {
                $transparentPixels[$idx] = 127;
                for ($i = 0; $i < 4; $i++) {
                    $nx = $cx + $dx[$i];
                    $ny = $cy + $dy[$i];
                    if ($nx >= 0 && $nx < $w && $ny >= 0 && $ny < $h) {
                        $nidx = $ny * $w + $nx;
                        if (!$visited[$nidx]) {
                            $visited[$nidx] = true;
                            $queue->enqueue([$nx, $ny]);
                        }
                    }
                }
            } elseif ($dist <= $tolerance + 15) {
                // Vùng biên chuyển tiếp mềm (Anti-aliasing)
                $alphaRatio = 1.0 - (($dist - $tolerance) / 15.0);
                $transparentPixels[$idx] = (int)round($alphaRatio * 127);
            }
        }

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $idx = $y * $w + $x;
                $alpha = $transparentPixels[$idx];
                $c = imagecolorat($src, $x, $y);
                $r = ($c >> 16) & 0xFF;
                $g = ($c >> 8) & 0xFF;
                $b = $c & 0xFF;

                if ($alpha >= 127) {
                    $tColor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagesetpixel($dst, $x, $y, $tColor);
                } elseif ($alpha > 0) {
                    $tColor = imagecolorallocatealpha($dst, $r, $g, $b, $alpha);
                    imagesetpixel($dst, $x, $y, $tColor);
                } else {
                    $tColor = imagecolorallocatealpha($dst, $r, $g, $b, 0);
                    imagesetpixel($dst, $x, $y, $tColor);
                }
            }
        }

        $ok = @imagepng($dst, $destPath, 6);
        imagedestroy($src);
        imagedestroy($dst);
        return $ok;
    }

    /**
     * Xử lý lưu file ảnh tải lên từ máy tính vào thư mục public/uploads/products/
     * Tự động xóa nền (remove background) và xuất ra file PNG trong suốt
     */
    private function handleImageUpload(array $file): ?string {
        if (empty($file['name']) || empty($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        // Giới hạn dung lượng ảnh tối đa 5MB
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }

        // Tuyệt đối không cho phép định dạng svg (chống Stored XSS)
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            return null;
        }

        // Kiểm tra thực tế xem có phải là file ảnh hợp lệ không (chống giả mạo đuôi file)
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return null;
        }

        $uploadDir = __DIR__ . '/../public/uploads/products/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $tempName = 'temp_raw_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $tempPath = $uploadDir . $tempName;

        if (move_uploaded_file($file['tmp_name'], $tempPath)) {
            // Tự động xóa phông nền và xuất thành file PNG trong suốt
            $pngName = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.png';
            $pngPath = $uploadDir . $pngName;

            if ($this->removeBackgroundGD($tempPath, $pngPath)) {
                @unlink($tempPath); // Dọn file tạm
                return BASE_URL . 'public/uploads/products/' . $pngName;
            }

            // Nếu không tách được bằng GD (hoặc lỗi), fallback giữ file gốc
            $finalRawName = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            @rename($tempPath, $uploadDir . $finalRawName);
            return BASE_URL . 'public/uploads/products/' . $finalRawName;
        }

        return null;
    }

    /**
     * Xử lý lưu file video tải lên từ máy tính vào thư mục public/uploads/videos/
     */
    private function handleVideoUpload(array $file): ?string {
        if (empty($file['name']) || empty($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        // Giới hạn dung lượng video tối đa 30MB
        if (($file['size'] ?? 0) > 30 * 1024 * 1024) {
            return null;
        }

        $allowedExts = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        // Kiểm tra MIME type thực tế bằng Fileinfo để chống giả mạo phần mở rộng
        $allowedMimes = [
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime',
            'video/x-m4v'
        ];
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
            if ($finfo) {
                finfo_close($finfo);
            }
            if (!$mime || !in_array($mime, $allowedMimes, true)) {
                return null;
            }
        }

        $uploadDir = __DIR__ . '/../public/uploads/videos/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $fileName = 'vid_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return BASE_URL . 'public/uploads/videos/' . $fileName;
        }

        return null;
    }

    public function productCreate(): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $brandModel = new Brand();
        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        $formType = ($_GET['type'] ?? ($_POST['form_product_type'] ?? 'phone')) === 'accessory' ? 'accessory' : 'phone';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $name = trim($_POST['name'] ?? '');
            $rawPrice = trim((string)($_POST['price'] ?? ''));
            $rawQty = trim((string)($_POST['quantity'] ?? '50'));

            if ($name === '') {
                $_SESSION['flash_admin_err'] = 'Tên sản phẩm không được để trống!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-create&type=' . $formType);
                return;
            }

            if (!is_numeric($rawPrice) || (float)$rawPrice <= 0) {
                $_SESSION['flash_admin_err'] = 'Giá bán phải là một số lớn hơn 0!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-create&type=' . $formType);
                return;
            }

            if (!ctype_digit($rawQty) || (int)$rawQty < 0) {
                $_SESSION['flash_admin_err'] = 'Số lượng tồn kho phải là số nguyên không âm!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-create&type=' . $formType);
                return;
            }

            $price = (float)$rawPrice;
            $quantity = (int)$rawQty;

            $productModel = new Product();
            $slug = $this->createSlug($name);

            // 1. Xử lý ảnh đại diện: ưu tiên file tải lên từ máy tính, nếu không thì lấy link URL
            $mainImage = '';
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = $this->handleImageUpload($_FILES['image_file']);
                if ($uploaded) {
                    $mainImage = $uploaded;
                }
            }
            if (empty($mainImage)) {
                $mainImage = trim($_POST['image'] ?? '');
            }

            // 2. Xử lý danh sách ảnh slideshow (kết hợp cả file tải lên từ máy và link URL)
            $gallerySlides = [];
            $submittedImages = $_POST['slide_images'] ?? [];
            $submittedTitles = $_POST['slide_titles'] ?? [];
            $submittedTags = $_POST['slide_tags'] ?? [];
            $submittedDescs = $_POST['slide_descs'] ?? [];

            $maxSlides = max(
                is_array($submittedImages) ? count($submittedImages) : 0,
                isset($_FILES['slide_files']['name']) && is_array($_FILES['slide_files']['name']) ? count($_FILES['slide_files']['name']) : 0
            );

            for ($idx = 0; $idx < $maxSlides; $idx++) {
                $imgUrl = '';
                // Kiểm tra nếu có file tải lên ở vị trí này
                if (!empty($_FILES['slide_files']['name'][$idx])) {
                    $fileItem = [
                        'name' => $_FILES['slide_files']['name'][$idx],
                        'type' => $_FILES['slide_files']['type'][$idx] ?? '',
                        'tmp_name' => $_FILES['slide_files']['tmp_name'][$idx],
                        'error' => $_FILES['slide_files']['error'][$idx],
                        'size' => $_FILES['slide_files']['size'][$idx] ?? 0
                    ];
                    $uploadedSlide = $this->handleImageUpload($fileItem);
                    if ($uploadedSlide) {
                        $imgUrl = $uploadedSlide;
                    }
                }

                // Nếu không có file mới tải lên, dùng link URL
                if (empty($imgUrl)) {
                    $imgUrl = trim($submittedImages[$idx] ?? '');
                }

                if (!empty($imgUrl)) {
                    $title = trim($submittedTitles[$idx] ?? ('Góc chụp ' . ($idx + 1)));
                    $desc = trim($submittedDescs[$idx] ?? '');
                    $tag = trim($submittedTags[$idx] ?? 'Cấu tạo');
                    $gallerySlides[] = [
                        'image' => $imgUrl,
                        'title' => $title,
                        'desc' => $desc,
                        'tag' => $tag,
                        'icon' => 'fa-solid fa-layer-group',
                        'badge' => ($idx + 1) . '. ' . $title
                    ];
                }
            }
            $galleryJson = !empty($gallerySlides) ? json_encode($gallerySlides, JSON_UNESCAPED_UNICODE) : null;

            // 3. Xử lý Video (tải lên từ máy hoặc link YouTube/URL)
            $mediaType = in_array($_POST['media_type'] ?? '', ['slideshow', 'video']) ? $_POST['media_type'] : 'slideshow';
            $videoUrl = '';
            if (!empty($_FILES['video_file']['name'])) {
                $uploadedVideo = $this->handleVideoUpload($_FILES['video_file']);
                if ($uploadedVideo) {
                    $videoUrl = $uploadedVideo;
                }
            }
            if (empty($videoUrl)) {
                $videoUrl = trim($_POST['video_url'] ?? '');
            }

            // 4. Xử lý Màu sắc sản phẩm (1 hoặc nhiều màu: Tên màu + Ảnh tải từ máy hoặc link URL + Số lượng kho)
            $colorsData = [];
            $cNames = $_POST['color_names'] ?? [];
            $cImages = $_POST['color_images'] ?? [];
            $cQuantities = $_POST['color_quantities'] ?? [];
            
            $maxColors = max(
                is_array($cNames) ? count($cNames) : 0,
                isset($_FILES['color_files']['name']) && is_array($_FILES['color_files']['name']) ? count($_FILES['color_files']['name']) : 0,
                is_array($cQuantities) ? count($cQuantities) : 0
            );

            for ($cIdx = 0; $cIdx < $maxColors; $cIdx++) {
                $cName = trim($cNames[$cIdx] ?? '');
                $cImg = '';
                $cQty = isset($cQuantities[$cIdx]) ? (int)$cQuantities[$cIdx] : 50;

                // Kiểm tra xem có file ảnh tải lên từ máy tính cho màu này không
                if (!empty($_FILES['color_files']['name'][$cIdx])) {
                    $colorFileItem = [
                        'name' => $_FILES['color_files']['name'][$cIdx],
                        'type' => $_FILES['color_files']['type'][$cIdx] ?? '',
                        'tmp_name' => $_FILES['color_files']['tmp_name'][$cIdx],
                        'error' => $_FILES['color_files']['error'][$cIdx],
                        'size' => $_FILES['color_files']['size'][$cIdx] ?? 0
                    ];
                    $uploadedColorImg = $this->handleImageUpload($colorFileItem);
                    if ($uploadedColorImg) {
                        $cImg = $uploadedColorImg;
                    }
                }

                // Nếu không upload file, dùng link URL
                if (empty($cImg)) {
                    $cImg = trim($cImages[$cIdx] ?? '');
                }

                if ($cName !== '' || $cImg !== '') {
                    $colorsData[] = [
                        'name' => $cName ?: ('Màu ' . ($cIdx + 1)),
                        'image' => $cImg ?: ($mainImage ?: ''),
                        'quantity' => max(0, $cQty)
                    ];
                }
            }
            $colorsJson = !empty($colorsData) ? json_encode($colorsData, JSON_UNESCAPED_UNICODE) : null;

            // 5. Xử lý Thông số kỹ thuật (hỗ trợ cả cặp spec_keys[] & spec_values[] hoặc text)
            $specsAssoc = [];
            $sKeys = $_POST['spec_keys'] ?? [];
            $sValues = $_POST['spec_values'] ?? [];
            if (is_array($sKeys)) {
                foreach ($sKeys as $sIdx => $sKey) {
                    $sKey = trim($sKey);
                    $sVal = trim($sValues[$sIdx] ?? '');
                    if ($sKey !== '' && $sVal !== '') {
                        $specsAssoc[$sKey] = $sVal;
                    }
                }
            }
            if (!empty($specsAssoc)) {
                $specsJson = json_encode($specsAssoc, JSON_UNESCAPED_UNICODE);
            } else {
                $rawSpecs = trim($_POST['specs'] ?? '');
                if (!empty($rawSpecs)) {
                    $decoded = json_decode($rawSpecs, true);
                    if (is_array($decoded)) {
                        $specsJson = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    } else {
                        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawSpecs));
                        $parsed = [];
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;
                            if (strpos($line, "\t") !== false) {
                                $p = explode("\t", $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            } elseif (strpos($line, ':') !== false) {
                                $p = explode(':', $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            } elseif (strpos($line, ' - ') !== false) {
                                $p = explode(' - ', $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            }
                        }
                        $specsJson = !empty($parsed) ? json_encode($parsed, JSON_UNESCAPED_UNICODE) : $rawSpecs;
                    }
                } else {
                    $specsJson = null;
                }
            }

            $data = [
                'category_id' => (int)$_POST['category_id'],
                'brand_id' => (int)$_POST['brand_id'],
                'name' => trim($_POST['name']),
                'slug' => $slug . '-' . time(),
                'price' => (float)$_POST['price'],
                'old_price' => !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null,
                'image' => $mainImage,
                'gallery' => $galleryJson,
                'video' => !empty($videoUrl) ? $videoUrl : null,
                'colors' => $colorsJson,
                'media_type' => $mediaType,
                'quantity' => (int)($_POST['quantity'] ?? 50),
                'compatible_device' => trim($_POST['compatible_device'] ?? 'Chính hãng'),
                'description' => trim($_POST['description'] ?? ''),
                'specs' => $specsJson,
                'is_featured' => isset($_POST['is_featured']),
                'is_hot' => isset($_POST['is_hot'])
            ];
            $productModel->create($data);
            $_SESSION['flash_admin_msg'] = 'Thêm sản phẩm mới thành công!';
            $this->redirect(BASE_URL . 'index.php?url=admin/products');
        }

        $selectedType = $formType;
        require_once __DIR__ . '/../views/admin/product_form.php';
    }

    public function productEdit(): void {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $productModel = new Product();
        $product = $productModel->getById($id);

        if (!$product) {
            $this->redirect(BASE_URL . 'index.php?url=admin/products');
        }
        $selectedType = (!empty($product['category_id']) && $product['category_id'] == 1) ? 'phone' : 'accessory';

        $categoryModel = new Category();
        $brandModel = new Brand();
        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $name = trim($_POST['name'] ?? '');
            $rawPrice = trim((string)($_POST['price'] ?? ''));
            $rawQty = trim((string)($_POST['quantity'] ?? '50'));

            if ($name === '') {
                $_SESSION['flash_admin_err'] = 'Tên sản phẩm không được để trống!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-edit&id=' . $id);
                return;
            }

            if (!is_numeric($rawPrice) || (float)$rawPrice <= 0) {
                $_SESSION['flash_admin_err'] = 'Giá bán phải là một số lớn hơn 0!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-edit&id=' . $id);
                return;
            }

            if (!ctype_digit($rawQty) || (int)$rawQty < 0) {
                $_SESSION['flash_admin_err'] = 'Số lượng tồn kho phải là số nguyên không âm!';
                $this->redirect(BASE_URL . 'index.php?url=admin/product-edit&id=' . $id);
                return;
            }

            $price = (float)$rawPrice;
            $quantity = (int)$rawQty;

            $slug = $this->createSlug($name);

            // 1. Xử lý ảnh đại diện: ưu tiên file tải lên từ máy tính, nếu không thì lấy link URL hoặc ảnh cũ
            $mainImage = '';
            if (!empty($_FILES['image_file']['name'])) {
                $uploaded = $this->handleImageUpload($_FILES['image_file']);
                if ($uploaded) {
                    $mainImage = $uploaded;
                }
            }
            if (empty($mainImage)) {
                $mainImage = trim($_POST['image'] ?? '');
            }
            if (empty($mainImage)) {
                $mainImage = $product['image']; // Giữ ảnh cũ nếu không đổi
            }

            // 2. Xử lý danh sách ảnh slideshow (kết hợp cả file tải lên từ máy và link URL)
            $gallerySlides = [];
            $submittedImages = $_POST['slide_images'] ?? [];
            $submittedTitles = $_POST['slide_titles'] ?? [];
            $submittedTags = $_POST['slide_tags'] ?? [];
            $submittedDescs = $_POST['slide_descs'] ?? [];

            $maxSlides = max(
                is_array($submittedImages) ? count($submittedImages) : 0,
                isset($_FILES['slide_files']['name']) && is_array($_FILES['slide_files']['name']) ? count($_FILES['slide_files']['name']) : 0
            );

            for ($idx = 0; $idx < $maxSlides; $idx++) {
                $imgUrl = '';
                if (!empty($_FILES['slide_files']['name'][$idx])) {
                    $fileItem = [
                        'name' => $_FILES['slide_files']['name'][$idx],
                        'type' => $_FILES['slide_files']['type'][$idx] ?? '',
                        'tmp_name' => $_FILES['slide_files']['tmp_name'][$idx],
                        'error' => $_FILES['slide_files']['error'][$idx],
                        'size' => $_FILES['slide_files']['size'][$idx] ?? 0
                    ];
                    $uploadedSlide = $this->handleImageUpload($fileItem);
                    if ($uploadedSlide) {
                        $imgUrl = $uploadedSlide;
                    }
                }

                if (empty($imgUrl)) {
                    $imgUrl = trim($submittedImages[$idx] ?? '');
                }

                if (!empty($imgUrl)) {
                    $title = trim($submittedTitles[$idx] ?? ('Góc chụp ' . ($idx + 1)));
                    $desc = trim($submittedDescs[$idx] ?? '');
                    $tag = trim($submittedTags[$idx] ?? 'Cấu tạo');
                    $gallerySlides[] = [
                        'image' => $imgUrl,
                        'title' => $title,
                        'desc' => $desc,
                        'tag' => $tag,
                        'icon' => 'fa-solid fa-layer-group',
                        'badge' => ($idx + 1) . '. ' . $title
                    ];
                }
            }
            $galleryJson = !empty($gallerySlides) ? json_encode($gallerySlides, JSON_UNESCAPED_UNICODE) : null;

            // 3. Xử lý Video (tải lên từ máy hoặc link YouTube/URL)
            $mediaType = in_array($_POST['media_type'] ?? '', ['slideshow', 'video']) ? $_POST['media_type'] : ($product['media_type'] ?? 'slideshow');
            $videoUrl = '';
            if (!empty($_FILES['video_file']['name'])) {
                $uploadedVideo = $this->handleVideoUpload($_FILES['video_file']);
                if ($uploadedVideo) {
                    $videoUrl = $uploadedVideo;
                }
            }
            if (empty($videoUrl)) {
                $videoUrl = trim($_POST['video_url'] ?? '');
            }
            if (empty($videoUrl)) {
                $videoUrl = $product['video'] ?? null;
            }

            // 4. Xử lý Màu sắc sản phẩm (1 hoặc nhiều màu: Tên màu + Ảnh tải từ máy hoặc link URL + Số lượng kho)
            $colorsData = [];
            $cNames = $_POST['color_names'] ?? [];
            $cImages = $_POST['color_images'] ?? [];
            $cQuantities = $_POST['color_quantities'] ?? [];
            
            $maxColors = max(
                is_array($cNames) ? count($cNames) : 0,
                isset($_FILES['color_files']['name']) && is_array($_FILES['color_files']['name']) ? count($_FILES['color_files']['name']) : 0,
                is_array($cQuantities) ? count($cQuantities) : 0
            );

            for ($cIdx = 0; $cIdx < $maxColors; $cIdx++) {
                $cName = trim($cNames[$cIdx] ?? '');
                $cImg = '';
                $cQty = isset($cQuantities[$cIdx]) ? (int)$cQuantities[$cIdx] : 50;

                // Kiểm tra xem có file ảnh tải lên từ máy tính cho màu này không
                if (!empty($_FILES['color_files']['name'][$cIdx])) {
                    $colorFileItem = [
                        'name' => $_FILES['color_files']['name'][$cIdx],
                        'type' => $_FILES['color_files']['type'][$cIdx] ?? '',
                        'tmp_name' => $_FILES['color_files']['tmp_name'][$cIdx],
                        'error' => $_FILES['color_files']['error'][$cIdx],
                        'size' => $_FILES['color_files']['size'][$cIdx] ?? 0
                    ];
                    $uploadedColorImg = $this->handleImageUpload($colorFileItem);
                    if ($uploadedColorImg) {
                        $cImg = $uploadedColorImg;
                    }
                }

                // Nếu không upload file, dùng link URL
                if (empty($cImg)) {
                    $cImg = trim($cImages[$cIdx] ?? '');
                }

                if ($cName !== '' || $cImg !== '') {
                    $colorsData[] = [
                        'name' => $cName ?: ('Màu ' . ($cIdx + 1)),
                        'image' => $cImg ?: ($mainImage ?: ''),
                        'quantity' => max(0, $cQty)
                    ];
                }
            }
            $colorsJson = !empty($colorsData) ? json_encode($colorsData, JSON_UNESCAPED_UNICODE) : null;

            // 5. Xử lý Thông số kỹ thuật (hỗ trợ cả cặp spec_keys[] & spec_values[] hoặc text)
            $specsAssoc = [];
            $sKeys = $_POST['spec_keys'] ?? [];
            $sValues = $_POST['spec_values'] ?? [];
            if (is_array($sKeys)) {
                foreach ($sKeys as $sIdx => $sKey) {
                    $sKey = trim($sKey);
                    $sVal = trim($specValues[$sIdx] ?? '');
                    if ($sKey !== '' && $sVal !== '') {
                        $specsAssoc[$sKey] = $sVal;
                    }
                }
            }
            if (!empty($specsAssoc)) {
                $specsJson = json_encode($specsAssoc, JSON_UNESCAPED_UNICODE);
            } else {
                $rawSpecs = trim($_POST['specs'] ?? '');
                if (!empty($rawSpecs)) {
                    $decoded = json_decode($rawSpecs, true);
                    if (is_array($decoded)) {
                        $specsJson = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    } else {
                        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawSpecs));
                        $parsed = [];
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;
                            if (strpos($line, "\t") !== false) {
                                $p = explode("\t", $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            } elseif (strpos($line, ':') !== false) {
                                $p = explode(':', $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            } elseif (strpos($line, ' - ') !== false) {
                                $p = explode(' - ', $line, 2);
                                $parsed[trim($p[0])] = trim($p[1]);
                            }
                        }
                        $specsJson = !empty($parsed) ? json_encode($parsed, JSON_UNESCAPED_UNICODE) : $rawSpecs;
                    }
                } else {
                    $specsJson = null;
                }
            }

            $data = [
                'category_id' => (int)$_POST['category_id'],
                'brand_id' => (int)$_POST['brand_id'],
                'name' => trim($_POST['name']),
                'slug' => $slug,
                'price' => (float)$_POST['price'],
                'old_price' => !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null,
                'image' => $mainImage,
                'gallery' => $galleryJson,
                'video' => !empty($videoUrl) ? $videoUrl : null,
                'colors' => $colorsJson,
                'media_type' => $mediaType,
                'quantity' => (int)($_POST['quantity'] ?? 50),
                'compatible_device' => trim($_POST['compatible_device'] ?? 'Chính hãng'),
                'description' => trim($_POST['description'] ?? ''),
                'specs' => $specsJson,
                'is_featured' => isset($_POST['is_featured']),
                'is_hot' => isset($_POST['is_hot'])
            ];
            $productModel->update($id, $data);
            $_SESSION['flash_admin_msg'] = 'Cập nhật sản phẩm thành công!';
            $this->redirect(BASE_URL . 'index.php?url=admin/products');
        }

        require_once __DIR__ . '/../views/admin/product_form.php';
    }

    private function checkCsrf(): void {
        $token = $_POST['_csrf_token'] ?? $_GET['csrf_token'] ?? null;
        if (!Csrf::verify($token)) {
            $_SESSION['flash_admin_err'] = 'Mã xác thực bảo mật (CSRF Token) không hợp lệ hoặc đã hết hạn. Thao tác đã bị chặn để bảo vệ an toàn!';
            $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . 'index.php?url=admin');
            $this->safeRedirect($referer, BASE_URL . 'index.php?url=admin');
            exit;
        }
    }

    /**
     * Xóa mềm sản phẩm (Chuyển vào thùng rác, bảo vệ đơn hàng cũ - Kế thừa từ Base Shop)
     */
    public function productDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $productModel = new Product();
        $productModel->delete($id);
        $_SESSION['flash_admin_msg'] = 'Đã chuyển sản phẩm vào Thùng rác an toàn. Bạn có thể khôi phục lại bất kỳ lúc nào!';
        $this->redirect(BASE_URL . 'index.php?url=admin/products');
    }

    /**
     * Xem danh sách sản phẩm trong Thùng rác
     */
    public function productTrash(): void {
        $this->checkAuth();
        $productModel = new Product();
        $trashProducts = $productModel->getTrash();

        $countAll = $productModel->countSearchAndFilter([]);
        $countPhones = $productModel->countSearchAndFilter(['category_type' => 'phone']);
        $countAccessories = $productModel->countSearchAndFilter(['category_type' => 'accessory']);
        $countTrash = count($trashProducts);

        require_once __DIR__ . '/../views/admin/products_trash.php';
    }

    /**
     * Khôi phục sản phẩm từ Thùng rác
     */
    public function productRestore(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $productModel = new Product();
        $productModel->restore($id);
        $_SESSION['flash_admin_msg'] = 'Đã khôi phục sản phẩm thành công! Sản phẩm đã hiển thị lại bình thường trên website.';
        $this->redirect(BASE_URL . 'index.php?url=admin/product-trash');
    }

    /**
     * Xóa vĩnh viễn sản phẩm khỏi Database (Chỉ cho phép khi sản phẩm CHƯA TỪNG có đơn hàng)
     */
    public function productForceDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $productModel = new Product();

        if ($productModel->hasOrders($id)) {
            $_SESSION['flash_admin_err'] = 'Chặn xóa vĩnh viễn: Sản phẩm này đã từng có trong đơn hàng của khách! Hãy giữ trong Thùng rác để bảo vệ dữ liệu kế toán & lịch sử đơn hàng.';
        } else {
            $productModel->forceDelete($id);
            $_SESSION['flash_admin_msg'] = 'Đã xóa vĩnh viễn sản phẩm khỏi cơ sở dữ liệu!';
        }

        $this->redirect(BASE_URL . 'index.php?url=admin/product-trash');
    }

    // ==========================================
    // QUẢN LÝ DANH MỤC (CATEGORIES CRUD)
    // ==========================================
    public function categories(): void {
        $this->checkAuth();
        $categoryModel = new Category();

        $keyword = trim($_GET['keyword'] ?? '');
        $type = $_GET['type'] ?? 'all'; // 'all', 'phone', 'accessory'

        $allCategories = $categoryModel->getAll();
        $countAll = count($allCategories);
        $countPhones = count(array_filter($allCategories, fn($c) => ($c['type'] ?? '') === 'phone' || $c['id'] == 1));
        $countAccessories = count(array_filter($allCategories, fn($c) => ($c['type'] ?? '') === 'accessory' || $c['id'] > 1));
        $countTrash = $categoryModel->countTrash();

        $categories = $allCategories;
        if ($type === 'phone') {
            $categories = array_filter($categories, fn($c) => ($c['type'] ?? '') === 'phone' || $c['id'] == 1);
        } elseif ($type === 'accessory') {
            $categories = array_filter($categories, fn($c) => ($c['type'] ?? '') === 'accessory' || $c['id'] > 1);
        }

        if ($keyword !== '') {
            $kw = mb_strtolower($keyword, 'UTF-8');
            $categories = array_filter($categories, function($c) use ($kw) {
                return strpos(mb_strtolower($c['name'], 'UTF-8'), $kw) !== false
                    || strpos(mb_strtolower($c['slug'] ?? '', 'UTF-8'), $kw) !== false
                    || strpos(mb_strtolower($c['description'] ?? '', 'UTF-8'), $kw) !== false;
            });
        }

        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function categoryCreate(): void {
        $this->checkAuth();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = $this->createSlug($name);
            } else {
                $slug = $this->createSlug($slug);
            }

            $type = in_array($_POST['type'] ?? '', ['phone', 'accessory']) ? $_POST['type'] : 'accessory';
            $icon = trim($_POST['icon'] ?? 'fa-solid fa-mobile-screen');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $error = 'Vui lòng nhập tên danh mục!';
            } else {
                $categoryModel = new Category();
                // Kiểm tra trùng slug
                $existing = $categoryModel->getBySlug($slug);
                if ($existing) {
                    $slug .= '-' . time();
                }

                $data = [
                    'name' => $name,
                    'slug' => $slug,
                    'type' => $type,
                    'icon' => $icon,
                    'description' => $description
                ];
                $categoryModel->create($data);
                $_SESSION['flash_admin_msg'] = 'Thêm danh mục "' . htmlspecialchars($name) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/categories');
            }
        }

        require_once __DIR__ . '/../views/admin/category_form.php';
    }

    public function categoryEdit(): void {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $categoryModel = new Category();
        $category = $categoryModel->getById($id);

        if (!$category) {
            $this->redirect(BASE_URL . 'index.php?url=admin/categories');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = $this->createSlug($name);
            } else {
                $slug = $this->createSlug($slug);
            }

            $type = in_array($_POST['type'] ?? '', ['phone', 'accessory']) ? $_POST['type'] : 'accessory';
            $icon = trim($_POST['icon'] ?? 'fa-solid fa-mobile-screen');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $error = 'Vui lòng nhập tên danh mục!';
            } else {
                // Kiểm tra trùng slug với bản ghi khác
                $existing = $categoryModel->getBySlug($slug);
                if ($existing && (int)$existing['id'] !== $id) {
                    $slug .= '-' . time();
                }

                $data = [
                    'name' => $name,
                    'slug' => $slug,
                    'type' => $type,
                    'icon' => $icon,
                    'description' => $description
                ];
                $categoryModel->update($id, $data);
                $_SESSION['flash_admin_msg'] = 'Cập nhật danh mục "' . htmlspecialchars($name) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/categories');
            }
        }

        require_once __DIR__ . '/../views/admin/category_form.php';
    }

    public function categoryDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $categoryModel = new Category();
        $category = $categoryModel->getById($id);
        if ($category) {
            $categoryModel->delete($id);
            $_SESSION['flash_admin_msg'] = 'Đã chuyển danh mục "' . htmlspecialchars($category['name']) . '" vào Thùng rác thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/categories');
    }

    public function categoryTrash(): void {
        $this->checkAuth();
        $categoryModel = new Category();
        $trashCategories = $categoryModel->getTrash();

        $allCategories = $categoryModel->getAll();
        $countAll = count($allCategories);
        $countPhones = count(array_filter($allCategories, fn($c) => ($c['type'] ?? '') === 'phone' || $c['id'] == 1));
        $countAccessories = count(array_filter($allCategories, fn($c) => ($c['type'] ?? '') === 'accessory' || $c['id'] > 1));
        $countTrash = count($trashCategories);

        require_once __DIR__ . '/../views/admin/categories_trash.php';
    }

    public function categoryRestore(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $categoryModel = new Category();
        $category = $categoryModel->getById($id);
        if ($category) {
            $categoryModel->restore($id);
            $_SESSION['flash_admin_msg'] = 'Đã khôi phục danh mục "' . htmlspecialchars($category['name']) . '" thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/category-trash');
    }

    public function categoryForceDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $categoryModel = new Category();
        $count = $categoryModel->countProducts($id);
        if ($count > 0) {
            $_SESSION['flash_admin_err'] = 'Không thể xóa vĩnh viễn danh mục này vì còn ' . $count . ' sản phẩm liên kết! Vui lòng chuyển hoặc xóa sản phẩm trước.';
        } else {
            $categoryModel->forceDelete($id);
            $_SESSION['flash_admin_msg'] = 'Đã xóa vĩnh viễn danh mục khỏi cơ sở dữ liệu!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/category-trash');
    }

    // ==========================================
    // QUẢN LÝ THƯƠNG HIỆU / HÃNG (BRANDS CRUD)
    // ==========================================
    public function brands(): void {
        $this->checkAuth();
        $brandModel = new Brand();

        $keyword = trim($_GET['keyword'] ?? '');
        $type = $_GET['type'] ?? 'all'; // 'all', 'phone', 'accessory'

        $allBrands = $brandModel->getAll();
        $countAll = count($allBrands);
        $countPhones = count(array_filter($allBrands, fn($b) => !empty($b['is_phone_brand'])));
        $countAccessories = count(array_filter($allBrands, fn($b) => empty($b['is_phone_brand'])));
        $countTrash = $brandModel->countTrash();

        $brands = $allBrands;
        if ($type === 'phone') {
            $brands = array_filter($brands, fn($b) => !empty($b['is_phone_brand']));
        } elseif ($type === 'accessory') {
            $brands = array_filter($brands, fn($b) => empty($b['is_phone_brand']));
        }

        if ($keyword !== '') {
            $kw = mb_strtolower($keyword, 'UTF-8');
            $brands = array_filter($brands, function($b) use ($kw) {
                return strpos(mb_strtolower($b['name'], 'UTF-8'), $kw) !== false
                    || strpos(mb_strtolower($b['slug'] ?? '', 'UTF-8'), $kw) !== false;
            });
        }

        require_once __DIR__ . '/../views/admin/brands.php';
    }

    public function brandCreate(): void {
        $this->checkAuth();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = $this->createSlug($name);
            } else {
                $slug = $this->createSlug($slug);
            }

            $is_phone_brand = isset($_POST['is_phone_brand']) ? 1 : 0;
            $logo = trim($_POST['logo'] ?? '');

            if (empty($name)) {
                $error = 'Vui lòng nhập tên thương hiệu/hãng!';
            } else {
                $brandModel = new Brand();
                // Kiểm tra trùng slug
                $existing = $brandModel->getBySlug($slug);
                if ($existing) {
                    $slug .= '-' . time();
                }

                $data = [
                    'name' => $name,
                    'slug' => $slug,
                    'is_phone_brand' => $is_phone_brand,
                    'logo' => $logo
                ];
                $brandModel->create($data);
                $_SESSION['flash_admin_msg'] = 'Thêm thương hiệu "' . htmlspecialchars($name) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/brands');
            }
        }

        require_once __DIR__ . '/../views/admin/brand_form.php';
    }

    public function brandEdit(): void {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $brandModel = new Brand();
        $brand = $brandModel->getById($id);

        if (!$brand) {
            $this->redirect(BASE_URL . 'index.php?url=admin/brands');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if (empty($slug)) {
                $slug = $this->createSlug($name);
            } else {
                $slug = $this->createSlug($slug);
            }

            $is_phone_brand = isset($_POST['is_phone_brand']) ? 1 : 0;
            $logo = trim($_POST['logo'] ?? '');

            if (empty($name)) {
                $error = 'Vui lòng nhập tên thương hiệu/hãng!';
            } else {
                $existing = $brandModel->getBySlug($slug);
                if ($existing && (int)$existing['id'] !== $id) {
                    $slug .= '-' . time();
                }

                $data = [
                    'name' => $name,
                    'slug' => $slug,
                    'is_phone_brand' => $is_phone_brand,
                    'logo' => $logo
                ];
                $brandModel->update($id, $data);
                $_SESSION['flash_admin_msg'] = 'Cập nhật thương hiệu "' . htmlspecialchars($name) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/brands');
            }
        }

        require_once __DIR__ . '/../views/admin/brand_form.php';
    }

    public function brandDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $brandModel = new Brand();
        $brand = $brandModel->getById($id);
        if ($brand) {
            $brandModel->delete($id);
            $_SESSION['flash_admin_msg'] = 'Đã chuyển thương hiệu "' . htmlspecialchars($brand['name']) . '" vào Thùng rác thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/brands');
    }

    public function brandTrash(): void {
        $this->checkAuth();
        $brandModel = new Brand();
        $trashBrands = $brandModel->getTrash();

        $allBrands = $brandModel->getAll();
        $countAll = count($allBrands);
        $countPhones = count(array_filter($allBrands, fn($b) => !empty($b['is_phone_brand'])));
        $countAccessories = count(array_filter($allBrands, fn($b) => empty($b['is_phone_brand'])));
        $countTrash = count($trashBrands);

        require_once __DIR__ . '/../views/admin/brands_trash.php';
    }

    public function brandRestore(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $brandModel = new Brand();
        $brand = $brandModel->getById($id);
        if ($brand) {
            $brandModel->restore($id);
            $_SESSION['flash_admin_msg'] = 'Đã khôi phục thương hiệu "' . htmlspecialchars($brand['name']) . '" thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/brand-trash');
    }

    public function brandForceDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $brandModel = new Brand();
        $count = $brandModel->countProducts($id);
        if ($count > 0) {
            $_SESSION['flash_admin_err'] = 'Không thể xóa vĩnh viễn thương hiệu này vì còn ' . $count . ' sản phẩm liên kết! Vui lòng chuyển hoặc xóa sản phẩm trước.';
        } else {
            $brandModel->forceDelete($id);
            $_SESSION['flash_admin_msg'] = 'Đã xóa vĩnh viễn thương hiệu khỏi cơ sở dữ liệu!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/brand-trash');
    }

    // ==========================================
    // QUẢN LÝ MÃ KHUYẾN MẠI / VOUCHER (COUPONS)
    // ==========================================
    public function coupons(): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $keyword = trim($_GET['keyword'] ?? '');
        $statusFilter = trim($_GET['status'] ?? 'all');
        $typeFilter = trim($_GET['type'] ?? 'all');

        $allCoupons = $couponModel->getAll(false);
        $countAll = count($allCoupons);
        $countActive = count(array_filter($allCoupons, fn($c) => $c['status'] === 'active' && (empty($c['end_date']) || strtotime($c['end_date']) >= time()) && $c['quantity'] > 0));
        $countExpired = count(array_filter($allCoupons, fn($c) => (!empty($c['end_date']) && strtotime($c['end_date']) < time()) || $c['quantity'] <= 0));
        $countTrash = $couponModel->countTrash();

        $coupons = $allCoupons;
        if (!empty($keyword)) {
            $coupons = array_filter($coupons, function($c) use ($keyword) {
                return (stripos($c['code'], $keyword) !== false) ||
                       (stripos($c['title'], $keyword) !== false);
            });
        }
        if ($statusFilter === 'active') {
            $coupons = array_filter($coupons, fn($c) => $c['status'] === 'active' && (empty($c['end_date']) || strtotime($c['end_date']) >= time()) && $c['quantity'] > 0);
        } elseif ($statusFilter === 'inactive') {
            $coupons = array_filter($coupons, fn($c) => $c['status'] === 'inactive');
        } elseif ($statusFilter === 'expired') {
            $coupons = array_filter($coupons, fn($c) => (!empty($c['end_date']) && strtotime($c['end_date']) < time()) || $c['quantity'] <= 0);
        }

        if ($typeFilter === 'percent') {
            $coupons = array_filter($coupons, fn($c) => $c['discount_type'] === 'percent');
        } elseif ($typeFilter === 'fixed') {
            $coupons = array_filter($coupons, fn($c) => $c['discount_type'] === 'fixed');
        }

        require_once __DIR__ . '/../views/admin/coupons.php';
    }

    public function couponCreate(): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $categoryModel = new Category();
        $productModel = new Product();
        $error = null;

        $categories = $categoryModel->getAll();
        $products = $productModel->searchAndFilter([], 500);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $title = trim($_POST['title'] ?? '');
            $discount_type = in_array($_POST['discount_type'] ?? '', ['fixed', 'percent']) ? $_POST['discount_type'] : 'fixed';
            $apply_scope = in_array($_POST['apply_scope'] ?? '', ['all', 'category', 'product']) ? $_POST['apply_scope'] : 'all';
            $category_id = ($apply_scope === 'category' && !empty($_POST['category_id'])) ? (int)$_POST['category_id'] : null;
            $product_id = ($apply_scope === 'product' && !empty($_POST['product_id'])) ? (int)$_POST['product_id'] : null;
            $discount_value = (float)($_POST['discount_value'] ?? 0);
            $max_discount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
            $min_order_amount = (float)($_POST['min_order_amount'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 100);
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d H:i:s');
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

            if (empty($code)) {
                $error = 'Vui lòng nhập mã khuyến mại!';
            } elseif (!preg_match('/^[A-Z0-9_-]{3,20}$/', $code)) {
                $error = 'Mã khuyến mại chỉ được chứa chữ cái, chữ số, dấu gạch nối và gạch dưới (3 - 20 ký tự, không chứa dấu cách hoặc ký tự đặc biệt)!';
            } elseif (empty($title)) {
                $error = 'Vui lòng nhập tên / tiêu đề chương trình khuyến mại!';
            } elseif ($quantity < 0) {
                $error = 'Số lượng mã phát hành không được là số âm!';
            } elseif ($apply_scope === 'category' && empty($category_id)) {
                $error = 'Vui lòng chọn danh mục áp dụng khuyến mại!';
            } elseif ($apply_scope === 'product' && empty($product_id)) {
                $error = 'Vui lòng chọn sản phẩm áp dụng khuyến mại!';
            } elseif ($discount_value <= 0) {
                $error = 'Giá trị giảm phải lớn hơn 0!';
            } elseif ($discount_type === 'percent' && ($discount_value < 1 || $discount_value > 99)) {
                $error = 'Phần trăm giảm giá phải nằm trong khoảng từ 1% đến 99%!';
            } elseif (!empty($end_date) && strtotime($end_date) < strtotime($start_date)) {
                $error = 'Thời gian kết thúc không được nhỏ hơn thời gian bắt đầu!';
            } elseif ($couponModel->getByCode($code)) {
                $error = 'Mã khuyến mại "' . htmlspecialchars($code) . '" đã tồn tại!';
            } else {
                $couponModel->create([
                    'code' => $code,
                    'title' => $title,
                    'discount_type' => $discount_type,
                    'apply_scope' => $apply_scope,
                    'category_id' => $category_id,
                    'product_id' => $product_id,
                    'discount_value' => $discount_value,
                    'max_discount' => $max_discount,
                    'min_order_amount' => $min_order_amount,
                    'quantity' => $quantity,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status' => $status
                ]);
                $_SESSION['flash_admin_msg'] = 'Đã thêm mã khuyến mại "' . htmlspecialchars($code) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/coupons');
            }
        }

        require_once __DIR__ . '/../views/admin/coupon_form.php';
    }

    public function couponEdit(): void {
        $this->checkAuth();
        $id = (int)($_GET['id'] ?? 0);
        $couponModel = new Coupon();
        $categoryModel = new Category();
        $productModel = new Product();
        $coupon = $couponModel->getById($id);

        if (!$coupon) {
            $_SESSION['flash_admin_err'] = 'Mã khuyến mại không tồn tại!';
            $this->redirect(BASE_URL . 'index.php?url=admin/coupons');
        }

        $categories = $categoryModel->getAll();
        $products = $productModel->searchAndFilter([], 500);
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $code = strtoupper(trim($_POST['code'] ?? ''));
            $title = trim($_POST['title'] ?? '');
            $discount_type = in_array($_POST['discount_type'] ?? '', ['fixed', 'percent']) ? $_POST['discount_type'] : 'fixed';
            $apply_scope = in_array($_POST['apply_scope'] ?? '', ['all', 'category', 'product']) ? $_POST['apply_scope'] : 'all';
            $category_id = ($apply_scope === 'category' && !empty($_POST['category_id'])) ? (int)$_POST['category_id'] : null;
            $product_id = ($apply_scope === 'product' && !empty($_POST['product_id'])) ? (int)$_POST['product_id'] : null;
            $discount_value = (float)($_POST['discount_value'] ?? 0);
            $max_discount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
            $min_order_amount = (float)($_POST['min_order_amount'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 100);
            $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d H:i:s');
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $status = in_array($_POST['status'] ?? '', ['active', 'inactive']) ? $_POST['status'] : 'active';

            $existing = $couponModel->getByCode($code);
            if (empty($code)) {
                $error = 'Vui lòng nhập mã khuyến mại!';
            } elseif (!preg_match('/^[A-Z0-9_-]{3,20}$/', $code)) {
                $error = 'Mã khuyến mại chỉ được chứa chữ cái, chữ số, dấu gạch nối và gạch dưới (3 - 20 ký tự, không chứa dấu cách hoặc ký tự đặc biệt)!';
            } elseif (empty($title)) {
                $error = 'Vui lòng nhập tên / tiêu đề chương trình khuyến mại!';
            } elseif ($quantity < 0) {
                $error = 'Số lượng mã phát hành không được là số âm!';
            } elseif ($apply_scope === 'category' && empty($category_id)) {
                $error = 'Vui lòng chọn danh mục áp dụng khuyến mại!';
            } elseif ($apply_scope === 'product' && empty($product_id)) {
                $error = 'Vui lòng chọn sản phẩm áp dụng khuyến mại!';
            } elseif ($discount_value <= 0) {
                $error = 'Giá trị giảm phải lớn hơn 0!';
            } elseif ($discount_type === 'percent' && ($discount_value < 1 || $discount_value > 99)) {
                $error = 'Phần trăm giảm giá phải nằm trong khoảng từ 1% đến 99%!';
            } elseif (!empty($end_date) && strtotime($end_date) < strtotime($start_date)) {
                $error = 'Thời gian kết thúc không được nhỏ hơn thời gian bắt đầu!';
            } elseif ($existing && (int)$existing['id'] !== $id) {
                $error = 'Mã khuyến mại "' . htmlspecialchars($code) . '" đã được sử dụng cho chương trình khác!';
            } else {
                $couponModel->update($id, [
                    'code' => $code,
                    'title' => $title,
                    'discount_type' => $discount_type,
                    'apply_scope' => $apply_scope,
                    'category_id' => $category_id,
                    'product_id' => $product_id,
                    'discount_value' => $discount_value,
                    'max_discount' => $max_discount,
                    'min_order_amount' => $min_order_amount,
                    'quantity' => $quantity,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status' => $status
                ]);
                $_SESSION['flash_admin_msg'] = 'Đã cập nhật mã khuyến mại "' . htmlspecialchars($code) . '" thành công!';
                $this->redirect(BASE_URL . 'index.php?url=admin/coupons');
            }
        }

        require_once __DIR__ . '/../views/admin/coupon_form.php';
    }

    public function couponDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_GET['id'] ?? 0);
        $couponModel = new Coupon();
        $coupon = $couponModel->getById($id);
        if ($coupon) {
            $couponModel->delete($id);
            $_SESSION['flash_admin_msg'] = 'Đã chuyển mã khuyến mại "' . htmlspecialchars($coupon['code']) . '" vào Thùng rác thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/coupons');
    }

    public function couponTrash(): void {
        $this->checkAuth();
        $couponModel = new Coupon();
        $trashCoupons = $couponModel->getTrash();

        $allCoupons = $couponModel->getAll();
        $countAll = count($allCoupons);
        $countTrash = count($trashCoupons);

        require_once __DIR__ . '/../views/admin/coupons_trash.php';
    }

    public function couponRestore(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_GET['id'] ?? 0);
        $couponModel = new Coupon();
        $coupon = $couponModel->getById($id);
        if ($coupon) {
            $couponModel->restore($id);
            $_SESSION['flash_admin_msg'] = 'Đã khôi phục mã khuyến mại "' . htmlspecialchars($coupon['code']) . '" thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/coupon-trash');
    }

    public function couponForceDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_GET['id'] ?? 0);
        $couponModel = new Coupon();
        $coupon = $couponModel->getById($id);
        if ($coupon) {
            $couponModel->forceDelete($id);
            $_SESSION['flash_admin_msg'] = 'Đã xóa vĩnh viễn mã khuyến mại khỏi cơ sở dữ liệu!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/coupon-trash');
    }

    // ==========================================
    // QUẢN LÝ ĐƠN HÀNG, LIÊN HỆ, KHÁCH HÀNG
    // ==========================================
    public function orders(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $status = $_GET['status'] ?? 'all';
        $statusFilter = $status;

        $filters = [
            'keyword' => trim((string)($_GET['keyword'] ?? '')),
            'payment_method' => trim((string)($_GET['payment_method'] ?? '')),
            'payment_status' => trim((string)($_GET['payment_status'] ?? '')),
            'date_from' => trim((string)($_GET['date_from'] ?? '')),
            'date_to' => trim((string)($_GET['date_to'] ?? '')),
            'sort' => trim((string)($_GET['sort'] ?? 'newest'))
        ];

        $orders = $orderModel->getAll(200, 0, $status, $filters);
        $trashCount = $orderModel->countTrash();

        // Tính tổng tiền các đơn hàng đang hiển thị (trừ đơn hủy)
        $filteredRevenue = 0;
        foreach ($orders as $ord) {
            if ($ord['order_status'] !== 'cancelled') {
                $filteredRevenue += (float)($ord['final_amount'] ?? 0);
            }
        }

        // Tự động hoàn tất các đơn hàng giao quá 7 ngày theo quy định
        $orderModel->autoCompleteOrdersAfterDays(7);

        // Đếm theo từng trạng thái để hiển thị huy hiệu số lượng trên các Tab
        $countAll = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn();
        $countPending = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'pending'")->fetchColumn();
        $countConfirmed = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'confirmed'")->fetchColumn();
        $countShipping = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'shipping'")->fetchColumn();
        $countCompleted = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'completed'")->fetchColumn();
        $countReturnRequested = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'return_requested'")->fetchColumn();
        $countReturned = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'returned'")->fetchColumn();
        $countCancelled = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'cancelled'")->fetchColumn();

        // Cảnh báo gom đơn cho Admin: Phát hiện khách hàng đặt nhiều đơn chia nhỏ trong 24h qua (chưa giao)
        $splitOrdersSummary = [];
        try {
            $splitStmt = $orderModel->getDb()->query("
                SELECT customer_phone, COUNT(*) as order_count, GROUP_CONCAT(order_code SEPARATOR ', ') as order_codes
                FROM orders 
                WHERE deleted_at IS NULL 
                  AND order_status IN ('pending', 'confirmed') 
                  AND customer_phone IS NOT NULL 
                  AND customer_phone != ''
                  AND created_at >= NOW() - INTERVAL 24 HOUR
                GROUP BY customer_phone
                HAVING COUNT(*) >= 2
            ");
            while ($row = $splitStmt->fetch(PDO::FETCH_ASSOC)) {
                $splitOrdersSummary[$row['customer_phone']] = $row;
            }
        } catch (Throwable $e) {}

        require_once __DIR__ . '/../views/admin/orders.php';
    }

    public function updateOrderStatus(): void {
        $this->checkAuth();
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                  || !empty($_POST['ajax']);

        // [SỬA LỖI BẢO MẬT] Bắt buộc CSRF token khi đổi trạng thái đơn hàng
        if (!Csrf::verify($_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Mã xác thực bảo mật (CSRF Token) không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang!'], 403);
            }
            $this->checkCsrf();
        }

        $id = (int)($_POST['order_id'] ?? $_POST['id'] ?? 0);
        $orderStatus = $_POST['order_status'] ?? $_POST['status'] ?? 'pending';
        $paymentStatus = $_POST['payment_status'] ?? null;
        $orderCode = htmlspecialchars($_POST['order_code'] ?? '');

        // [SỬA LỖI] Chỉ chấp nhận các trạng thái hợp lệ (tránh lỗi SQL khi gửi giá trị lạ)
        $validOrderStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'return_requested', 'returned', 'cancelled'];
        $validPaymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        if (!in_array($orderStatus, $validOrderStatuses, true) || ($paymentStatus !== null && $paymentStatus !== '' && !in_array($paymentStatus, $validPaymentStatuses, true))) {
            $errMsg = 'Trạng thái đơn hàng không hợp lệ!';
            if ($isAjax) {
                $this->json(['success' => false, 'message' => $errMsg], 400);
            }
            $_SESSION['flash_order_err'] = $errMsg;
            $this->redirect(BASE_URL . 'index.php?url=admin/orders');
            return;
        }

        if ($id > 0) {
            $orderModel = new Order();
            $curOrder = $orderModel->getById($id);

            $curStatus = $curOrder['order_status'];

            // [MÁY TRẠNG THÁI 1 CHIỀU - STATE MACHINE] Ràng buộc nghiêm ngặt thứ tự chuyển trạng thái đơn hàng
            $allowedTransitions = [
                'pending' => ['pending', 'confirmed', 'cancelled'],
                'confirmed' => ['confirmed', 'shipping', 'cancelled'],
                'shipping' => ['shipping', 'completed'],
                'completed' => ['completed', 'return_requested', 'returned'],
                'return_requested' => ['return_requested', 'returned', 'completed'],
                'returned' => ['returned'],
                'cancelled' => ['cancelled']
            ];

            if (!isset($allowedTransitions[$curStatus]) || !in_array($orderStatus, $allowedTransitions[$curStatus], true)) {
                $statusLabels = [
                    'pending' => 'Chờ duyệt',
                    'confirmed' => 'Đã duyệt',
                    'shipping' => 'Đang giao',
                    'completed' => 'Hoàn thành',
                    'return_requested' => 'Chờ hoàn hàng',
                    'returned' => 'Đã trả hàng',
                    'cancelled' => 'Đã hủy'
                ];
                $curLabel = $statusLabels[$curStatus] ?? $curStatus;
                $targetLabel = $statusLabels[$orderStatus] ?? $orderStatus;
                $errMsg = "Máy trạng thái 1 chiều: Không thể chuyển ngược đơn hàng từ [{$curLabel}] sang [{$targetLabel}]!";
                if ($isAjax) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json; charset=utf-8');
                        http_response_code(400);
                    }
                    echo json_encode(['success' => false, 'message' => $errMsg], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                $_SESSION['flash_order_err'] = $errMsg;
                $this->redirect(BASE_URL . 'index.php?url=admin/orders');
                return;
            }

            // Ràng buộc bảo mật: Đơn hàng khách đã hủy
            if ($curOrder && $curOrder['order_status'] === 'cancelled') {
                // NGOẠI LỆ: Đơn hủy nhưng đã thanh toán -> Admin được phép cập nhật sang "Đã hoàn tiền" (refunded)
                if ($curOrder['payment_status'] === 'paid' && $paymentStatus === 'refunded') {
                    $orderModel->updateOrderStatus($id, 'cancelled', 'refunded');
                    $msg = 'Đã cập nhật trạng thái đơn #' . ($orderCode ?: $id) . ' sang "Đã hoàn tiền" thành công!';
                    if ($isAjax) {
                        $counts = [
                            'all' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn(),
                            'pending' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'pending'")->fetchColumn(),
                            'confirmed' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'confirmed'")->fetchColumn(),
                            'shipping' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'shipping'")->fetchColumn(),
                            'completed' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'completed'")->fetchColumn(),
                            'returned' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status IN ('returned', 'return_requested')")->fetchColumn(),
                            'cancelled' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'cancelled'")->fetchColumn()
                        ];
                        if (!headers_sent()) {
                            header('Content-Type: application/json; charset=utf-8');
                        }
                        echo json_encode([
                            'success' => true,
                            'message' => $msg,
                            'order_status' => 'cancelled',
                            'payment_status' => 'refunded',
                            'counts' => $counts
                        ]);
                        exit;
                    }
                    $_SESSION['flash_order_msg'] = $msg;
                    $this->redirect(BASE_URL . 'index.php?url=admin/orders');
                    return;
                }

                $errMsg = 'Đơn hàng #' . ($curOrder['order_code'] ?? $id) . ' đã bị hủy, hệ thống đã khóa và Admin không thể chỉnh sửa trạng thái nữa!';
                if ($isAjax) {
                    if (!headers_sent()) {
                        header('Content-Type: application/json; charset=utf-8');
                        http_response_code(400);
                    }
                    echo json_encode(['success' => false, 'message' => $errMsg]);
                    exit;
                }
                $_SESSION['flash_order_err'] = $errMsg;
                $this->redirect(BASE_URL . 'index.php?url=admin/orders');
                return;
            }

            // KIỂM SOÁT QUYỀN TRẠNG THÁI THANH TOÁN:
            // Chưa TT và Đã TT được hệ thống tự động cập nhật theo giao dịch của khách hàng.
            // Admin chỉ có quyền cập nhật sang "Đã hoàn tiền" (refunded) khi đơn hàng đã thanh toán.
            $curPayStatus = $curOrder['payment_status'] ?? 'pending';
            if ($paymentStatus === 'refunded') {
                if ($curPayStatus !== 'paid' && $curPayStatus !== 'refunded') {
                    $errMsg = 'Chỉ có thể cập nhật "Đã hoàn tiền" cho đơn hàng khách đã thanh toán!';
                    if ($isAjax) {
                        if (!headers_sent()) {
                            header('Content-Type: application/json; charset=utf-8');
                            http_response_code(400);
                        }
                        echo json_encode(['success' => false, 'message' => $errMsg]);
                        exit;
                    }
                    $_SESSION['flash_order_err'] = $errMsg;
                    $this->redirect(BASE_URL . 'index.php?url=admin/orders');
                    return;
                }
                $finalPayStatus = 'refunded';
            } elseif ($orderStatus === 'completed') {
                // Tự động đồng bộ: Khi đơn giao thành công (COD hoàn tất), chuyển sang Đã TT
                $finalPayStatus = 'paid';
            } elseif ($curPayStatus === 'refunded') {
                // Đã hoàn tiền rồi thì không được đổi ngược lại
                $finalPayStatus = 'refunded';
            } else {
                // Giữ nguyên trạng thái tự động theo khách (Admin không thể can thiệp thủ công giữa pending và paid)
                $finalPayStatus = $curPayStatus;
            }

            $orderModel->updateOrderStatus($id, $orderStatus, $finalPayStatus);
            if (!empty($_POST['note'])) {
                $noteAppend = "\n" . trim((string)$_POST['note']);
                $orderModel->getDb()->prepare("UPDATE orders SET notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ?")->execute([$noteAppend, $id]);
            }
            $msg = 'Đã tự động lưu trạng thái đơn hàng ' . ($orderCode ? '#' . $orderCode : '') . ' thành công!';

            if ($isAjax) {

                // Thống kê số lượng đơn theo từng trạng thái để cập nhật lên các Tab Realtime
                $counts = [
                    'all' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn(),
                    'pending' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'pending'")->fetchColumn(),
                    'confirmed' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'confirmed'")->fetchColumn(),
                    'shipping' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'shipping'")->fetchColumn(),
                    'completed' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'completed'")->fetchColumn(),
                    'return_requested' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'return_requested'")->fetchColumn(),
                    'returned' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'returned'")->fetchColumn(),
                    'cancelled' => (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL AND order_status = 'cancelled'")->fetchColumn()
                ];

                if (!headers_sent()) {
                    header('Content-Type: application/json; charset=utf-8');
                }
                echo json_encode([
                    'success' => true,
                    'message' => $msg,
                    'order_status' => $orderStatus,
                    'payment_status' => $finalPayStatus,
                    'counts' => $counts
                ]);
                exit;
            }

            $_SESSION['flash_order_msg'] = $msg;
        }

        $this->redirect(BASE_URL . 'index.php?url=admin/orders');
    }

    /**
     * Xóa mềm đơn hàng (chuyển vào Thùng rác)
     * TUYỆT ĐỐI KHÔNG ẢNH HƯỞNG ĐẾN DOANH THU VÀ THỐNG KÊ LỊCH SỬ
     */
    public function orderDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $orderModel = new Order();
            $order = $orderModel->getById($id);
            $orderModel->softDelete($id);
            $_SESSION['flash_order_msg'] = 'Đã chuyển đơn hàng #' . ($order['order_code'] ?? $id) . ' vào Thùng rác thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/orders');
    }

    /**
     * Trang Thùng rác Đơn Hàng (Giao diện đồng bộ Sản phẩm/Danh mục/Thương hiệu)
     */
    public function orderTrash(): void {
        $this->checkAuth();
        $orderModel = new Order();
        $trashOrders = $orderModel->getTrash();
        $trashCount = count($trashOrders);
        $countAll = (int)$orderModel->getDb()->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn();

        require_once __DIR__ . '/../views/admin/orders_trash.php';
    }

    /**
     * Khôi phục đơn hàng từ Thùng rác
     */
    public function orderRestore(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $orderModel = new Order();
            $orderModel->restore($id);
            $_SESSION['flash_order_msg'] = 'Đã khôi phục đơn hàng thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/orders-trash');
    }

    /**
     * Xóa vĩnh viễn đơn hàng khỏi cơ sở dữ liệu
     */
    public function orderForceDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id > 0) {
            $orderModel = new Order();
            $orderModel->forceDelete($id);
            $_SESSION['flash_order_msg'] = 'Đã xóa vĩnh viễn đơn hàng khỏi hệ thống!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/orders-trash');
    }

    /**
     * Đối soát thủ công đơn hàng SePay qua API chi tiết đơn (Kế thừa từ Base Shop)
     */
    public function reconcileSepay(): void {
        $this->checkAuth();
        require_once __DIR__ . '/../models/Payment.php';
        $orderCode = trim($_GET['code'] ?? $_POST['code'] ?? '');
        if (empty($orderCode)) {
            $_SESSION['flash_order_msg'] = 'Thiếu mã đơn hàng cần đối soát!';
            $this->redirect(BASE_URL . 'index.php?url=admin/orders');
        }

        $paymentModel = new Payment();
        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);
        $payment = $paymentModel->findByOrderCode($orderCode);

        if (!$payment) {
            $_SESSION['flash_order_msg'] = 'Đơn hàng #' . $orderCode . ' không có bản ghi thanh toán SePay trực tuyến!';
            $this->redirect(BASE_URL . 'index.php?url=admin/orders');
        }

        try {
            require_once __DIR__ . '/../services/PaymentService.php';
            $paymentService = new PaymentService();
            $result = $paymentService->reconcileSepay($payment);

            if ($result['updated']) {
                $_SESSION['flash_order_msg'] = 'Đối soát SePay THÀNH CÔNG: Đơn hàng #' . $orderCode . ' đã được xác nhận thanh toán!';
            } else {
                $_SESSION['flash_order_msg'] = $result['message'];
            }
        } catch (Throwable $e) {
            $_SESSION['flash_order_msg'] = 'Lỗi kết nối API SePay: ' . $e->getMessage();
        }

        $this->redirect(BASE_URL . 'index.php?url=admin/orders');
    }

    public function contacts(): void {
        $this->checkAuth();
        $contactModel = new Contact();
        $contacts = $contactModel->getAll(50);

        require_once __DIR__ . '/../views/admin/contacts.php';
    }

    public function updateContactStatus(): void {
        $this->checkAuth();
        $this->requireCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'read';
        $contactModel = new Contact();
        $contactModel->updateStatus($id, $status);
        $this->redirect(BASE_URL . 'index.php?url=admin/contacts');
    }

    public function users(): void {
        $this->checkAuth();
        $userModel = new User();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrf();
            $userId = (int)($_POST['user_id'] ?? 0);
            $newStatus = trim($_POST['status'] ?? '');

            if ($userId > 0 && in_array($newStatus, ['active', 'blocked'], true)) {
                $targetUser = $userModel->getById($userId);
                if (!$targetUser) {
                    $_SESSION['flash_admin_err'] = 'Không tìm thấy tài khoản người dùng!';
                } elseif (($targetUser['role'] ?? '') === 'admin') {
                    $_SESSION['flash_admin_err'] = 'Tuyệt đối không thể khóa tài khoản Quản trị viên (Admin)!';
                } else {
                    $userModel->setStatus($userId, $newStatus);
                    $actionText = ($newStatus === 'blocked') ? 'Khóa' : 'Mở khóa';
                    $_SESSION['flash_admin_msg'] = "Đã {$actionText} tài khoản \"{$targetUser['fullname']}\" ({$targetUser['email']}) thành công!";
                }
            } else {
                $_SESSION['flash_admin_err'] = 'Dữ liệu yêu cầu không hợp lệ!';
            }
            $this->redirect(BASE_URL . 'index.php?url=admin/users');
            return;
        }

        $users = $userModel->getAll(50);

        require_once __DIR__ . '/../views/admin/users.php';
    }

    public function reviews(): void {
        $this->checkAuth();
        $reviewModel = new Review();
        $reviews = $reviewModel->getAllReviews(100);

        require_once __DIR__ . '/../views/admin/reviews.php';
    }

    public function reviewDelete(): void {
        $this->checkAuth();
        $this->checkCsrf();
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $reviewModel = new Review();
            $reviewModel->deleteReview($id);
            $_SESSION['flash_review_msg'] = 'Đã xóa đánh giá của khách hàng thành công!';
        }
        $this->redirect(BASE_URL . 'index.php?url=admin/reviews');
    }
}
