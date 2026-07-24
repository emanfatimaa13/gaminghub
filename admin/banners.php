<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $bannerId = (int)$_GET['delete'];
    
    // Get banner image to delete
    $banner = getSingleRecord("SELECT banner_image FROM banners WHERE banner_id = ?", [$bannerId], 'i');
    if ($banner && $banner['banner_image']) {
        $imagePath = '../uploads/banners/' . $banner['banner_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $sql = "DELETE FROM banners WHERE banner_id = ?";
    executeQuery($sql, [$bannerId], 'i');
    setMessage('success', 'Banner deleted successfully');
    header('Location: banners.php');
    exit();
}

// Handle add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle'] ?? '');
    $buttonText = sanitize($_POST['button_text'] ?? '');
    $buttonLink = sanitize($_POST['button_link'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $displayOrder = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['banner_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = '../uploads/banners/' . $imageName;
            
            // Create directory if not exists
            if (!is_dir('../uploads/banners/')) {
                mkdir('../uploads/banners/', 0777, true);
            }
            
            move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadPath);
        }
    }
    
    if (isset($_POST['banner_id']) && !empty($_POST['banner_id'])) {
        // Update
        $bannerId = (int)$_POST['banner_id'];
        
        if ($imageName) {
            // Delete old image
            $old = getSingleRecord("SELECT banner_image FROM banners WHERE banner_id = ?", [$bannerId], 'i');
            if ($old && $old['banner_image']) {
                $oldPath = '../uploads/banners/' . $old['banner_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $sql = "UPDATE banners SET banner_title=?, banner_subtitle=?, banner_image=?, button_text=?, button_link=?, is_active=?, display_order=? WHERE banner_id=?";
            executeQuery($sql, [$title, $subtitle, $imageName, $buttonText, $buttonLink, $isActive, $displayOrder, $bannerId], 'sssssiii');
        } else {
            $sql = "UPDATE banners SET banner_title=?, banner_subtitle=?, button_text=?, button_link=?, is_active=?, display_order=? WHERE banner_id=?";
            executeQuery($sql, [$title, $subtitle, $buttonText, $buttonLink, $isActive, $displayOrder, $bannerId], 'ssssiii');
        }
        setMessage('success', 'Banner updated successfully');
    } else {
        // Add
        if (empty($imageName)) {
            setMessage('error', 'Please upload a banner image');
            header('Location: banners.php');
            exit();
        }
        $sql = "INSERT INTO banners (banner_title, banner_subtitle, banner_image, button_text, button_link, is_active, display_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        executeQuery($sql, [$title, $subtitle, $imageName, $buttonText, $buttonLink, $isActive, $displayOrder], 'sssssii');
        setMessage('success', 'Banner added successfully');
    }
    header('Location: banners.php');
    exit();
}

// Get all banners
$banners = executeQuery("SELECT * FROM banners ORDER BY display_order ASC, banner_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #0a0a1a; }
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; border-right: 1px solid #3a3a5a; padding: 0; }
        .admin-sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s; }
        .admin-sidebar .nav-link:hover { color: #fff; background: rgba(108, 92, 231, 0.15); }
        .admin-sidebar .nav-link.active { color: #fff; background: #6c5ce7; }
        .admin-sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .admin-content { background: #0f0f1a; min-height: 100vh; padding: 20px; }
        .card-dark { background: #1e1e3e; border: 1px solid #2a2a5a; border-radius: 12px; }
        .card-dark .card-header { background: transparent; border-bottom: 1px solid #2a2a5a; padding: 15px 20px; }
        .table-dark { color: #e0e0e0; }
        .table-dark th { color: #b8b8d4; font-weight: 600; border-bottom: 2px solid #2a2a5a; }
        .table-dark td { vertical-align: middle; border-color: #2a2a5a; }
        .table-dark tbody tr:hover { background: rgba(108, 92, 231, 0.08); }
        
        /* Banner Image Styles */
        .banner-img {
            width: 120px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #2a2a5a;
            background: #0f0f1a;
        }
        .banner-img-placeholder {
            width: 120px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f0f1a;
            border-radius: 8px;
            border: 1px solid #2a2a5a;
            color: #3a3a5a;
            font-size: 1.5rem;
        }
        
        .modal-content { background: #1a1a2e; border: 1px solid #2a2a5a; border-radius: 12px; color: #e0e0e0; }
        .modal-header { border-bottom: 1px solid #2a2a5a; padding: 15px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { border-top: 1px solid #2a2a5a; padding: 15px 20px; }
        .form-label { color: #e0e0e0; font-weight: 500; }
        .form-control, .form-select { background: #0f0f1a; border: 1px solid #2a2a5a; color: #e0e0e0; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background: #0f0f1a; border-color: #6c5ce7; box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25); color: #e0e0e0; }
        .form-control::placeholder { color: #8888aa; }
        .form-check-input { background-color: #0f0f1a; border-color: #2a2a5a; }
        .form-check-input:checked { background-color: #6c5ce7; border-color: #6c5ce7; }
        .section-title { color: #ffffff; font-family: 'Orbitron', sans-serif; font-weight: 700; }
        .btn-close { filter: invert(1); }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; color: #3a3a5a; margin-bottom: 20px; }
        .empty-state h5 { color: #e0e0e0; }
        .empty-state p { color: #8888aa; }
        .badge-status { padding: 4px 12px; border-radius: 20px; font-weight: 500; font-size: 0.75rem; }
        .badge-status.active { background: rgba(0, 212, 126, 0.2); color: #00d47e; }
        .badge-status.inactive { background: rgba(255, 71, 87, 0.2); color: #ff4757; }
        .gaming-logo { font-family: 'Orbitron', sans-serif; font-weight: 900; letter-spacing: 2px; background: linear-gradient(135deg, #6c5ce7 0%, #00d2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        #imagePreviewContainer img { max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #2a2a5a; background: #0f0f1a; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 admin-sidebar">
                <div class="p-3 border-bottom border-secondary">
                    <h5 class="gaming-logo" style="font-size: 1.2rem;">
                        <i class="fas fa-gamepad me-2" style="-webkit-text-fill-color: #6c5ce7;"></i>Admin
                    </h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-dashboard"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link active" href="banners.php"><i class="fas fa-image"></i> Banners</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">Manage Banners</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal">
                        <i class="fas fa-plus me-2"></i>Add Banner
                    </button>
                </div>
                
                <?php displayMessage(); ?>
                
                <div class="card-dark">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white">Total Banners: <?php echo count($banners); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($banners)): ?>
                            <div class="empty-state">
                                <i class="fas fa-image"></i>
                                <h5>No Banners Found</h5>
                                <p>Click the "Add Banner" button to create your first banner.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="width: 140px;">Image</th>
                                            <th>Title</th>
                                            <th>Button Text</th>
                                            <th>Order</th>
                                            <th>Status</th>
                                            <th style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($banners as $banner): ?>
                                        <tr>
                                            <td>#<?php echo $banner['banner_id']; ?></td>
                                            <td>
                                                <?php 
                                                $imagePath = '../uploads/banners/' . ($banner['banner_image'] ?? '');
                                                if (!empty($banner['banner_image']) && file_exists($imagePath)): 
                                                ?>
                                                    <img src="<?php echo $imagePath; ?>" 
                                                         class="banner-img" 
                                                         alt="<?php echo htmlspecialchars($banner['banner_title']); ?>">
                                                <?php else: ?>
                                                    <div class="banner-img-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($banner['banner_title']); ?></strong>
                                                <?php if (!empty($banner['banner_subtitle'])): ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($banner['banner_subtitle']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($banner['button_text'] ?? 'N/A'); ?></td>
                                            <td><?php echo $banner['display_order']; ?></td>
                                            <td>
                                                <?php if ($banner['is_active']): ?>
                                                    <span class="badge-status active">Active</span>
                                                <?php else: ?>
                                                    <span class="badge-status inactive">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm edit-banner" 
                                                        data-id="<?php echo $banner['banner_id']; ?>"
                                                        data-title="<?php echo htmlspecialchars($banner['banner_title']); ?>"
                                                        data-subtitle="<?php echo htmlspecialchars($banner['banner_subtitle'] ?? ''); ?>"
                                                        data-button-text="<?php echo htmlspecialchars($banner['button_text'] ?? ''); ?>"
                                                        data-button-link="<?php echo htmlspecialchars($banner['button_link'] ?? ''); ?>"
                                                        data-active="<?php echo $banner['is_active']; ?>"
                                                        data-order="<?php echo $banner['display_order']; ?>"
                                                        data-image="<?php echo $banner['banner_image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?php echo $banner['banner_id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Are you sure you want to delete this banner?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Banner Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="banner_id" id="bannerId">
                        
                        <div class="mb-3">
                            <label class="form-label">Banner Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" id="bannerTitle" placeholder="Enter banner title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" id="bannerSubtitle" placeholder="Enter subtitle">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Banner Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="banner_image" id="bannerImage" accept="image/*">
                            <small class="text-muted">Recommended size: 1920x600px. Leave empty to keep current image.</small>
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <img id="imagePreview" src="" alt="Preview">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" class="form-control" name="button_text" id="buttonText" placeholder="Shop Now">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Button Link</label>
                                <input type="text" class="form-control" name="button_link" id="buttonLink" placeholder="products.php">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" class="form-control" name="display_order" id="displayOrder" value="0" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit banner - fill modal with data
        document.querySelectorAll('.edit-banner').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalTitle').textContent = 'Edit Banner';
                document.getElementById('bannerId').value = this.dataset.id;
                document.getElementById('bannerTitle').value = this.dataset.title;
                document.getElementById('bannerSubtitle').value = this.dataset.subtitle;
                document.getElementById('buttonText').value = this.dataset.buttonText;
                document.getElementById('buttonLink').value = this.dataset.buttonLink;
                document.getElementById('displayOrder').value = this.dataset.order || 0;
                document.getElementById('isActive').checked = this.dataset.active == 1;
                document.getElementById('bannerImage').required = false;
                
                // Show current image
                const preview = document.getElementById('imagePreview');
                const container = document.getElementById('imagePreviewContainer');
                if (this.dataset.image) {
                    preview.src = '../uploads/banners/' + this.dataset.image;
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
                
                new bootstrap.Modal(document.getElementById('bannerModal')).show();
            });
        });
        
        // Reset modal on close
        document.getElementById('bannerModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Add New Banner';
            document.getElementById('bannerId').value = '';
            document.getElementById('bannerTitle').value = '';
            document.getElementById('bannerSubtitle').value = '';
            document.getElementById('buttonText').value = '';
            document.getElementById('buttonLink').value = '';
            document.getElementById('displayOrder').value = 0;
            document.getElementById('isActive').checked = true;
            document.getElementById('bannerImage').value = '';
            document.getElementById('bannerImage').required = true;
            document.getElementById('imagePreviewContainer').style.display = 'none';
        });
        
        // Image preview
        document.getElementById('bannerImage').addEventListener('change', function(e) {
            const file = this.files[0];
            const preview = document.getElementById('imagePreview');
            const container = document.getElementById('imagePreviewContainer');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    container.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                container.style.display = 'none';
            }
        });
    </script>
</body>
</html>