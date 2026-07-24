<?php
require_once '../config/database.php';
require_once '../config/functions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get category image to delete
    $category = getSingleRecord("SELECT category_image FROM categories WHERE category_id = ?", [$id], 'i');
    if ($category && $category['category_image']) {
        $imagePath = '../uploads/categories/' . $category['category_image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $sql = "DELETE FROM categories WHERE category_id = ?";
    executeQuery($sql, [$id], 'i');
    setMessage('success', 'Category deleted successfully');
    header('Location: categories.php');
    exit();
}

// Handle add/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['category_name']);
    $description = sanitize($_POST['category_description'] ?? '');
    
    // Handle image upload
    $imageName = '';
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $filename = $_FILES['category_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = '../uploads/categories/' . $imageName;
            
            // Create directory if not exists
            if (!is_dir('../uploads/categories/')) {
                mkdir('../uploads/categories/', 0777, true);
            }
            
            move_uploaded_file($_FILES['category_image']['tmp_name'], $uploadPath);
        }
    }
    
    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update
        $id = (int)$_POST['edit_id'];
        if ($imageName) {
            // Delete old image
            $old = getSingleRecord("SELECT category_image FROM categories WHERE category_id = ?", [$id], 'i');
            if ($old && $old['category_image']) {
                $oldPath = '../uploads/categories/' . $old['category_image'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $sql = "UPDATE categories SET category_name=?, category_description=?, category_image=? WHERE category_id=?";
            executeQuery($sql, [$name, $description, $imageName, $id], 'sssi');
        } else {
            $sql = "UPDATE categories SET category_name=?, category_description=? WHERE category_id=?";
            executeQuery($sql, [$name, $description, $id], 'ssi');
        }
        setMessage('success', 'Category updated successfully');
    } else {
        // Add
        if (empty($imageName)) {
            setMessage('error', 'Please upload a category image');
            header('Location: categories.php');
            exit();
        }
        $sql = "INSERT INTO categories (category_name, category_description, category_image) VALUES (?, ?, ?)";
        executeQuery($sql, [$name, $description, $imageName], 'sss');
        setMessage('success', 'Category added successfully');
    }
    header('Location: categories.php');
    exit();
}

$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-sidebar { background: #1a1a2e; min-height: 100vh; border-right: 1px solid #3a3a5a; padding: 0; }
        .admin-sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-radius: 0; transition: all 0.3s; }
        .admin-sidebar .nav-link:hover { color: #fff; background: rgba(108, 92, 231, 0.15); }
        .admin-sidebar .nav-link.active { color: #fff; background: #6c5ce7; }
        .admin-sidebar .nav-link i { width: 20px; margin-right: 10px; }
        .admin-content { background: #0f0f1a; min-height: 100vh; padding: 20px; }
        .card-dark { background: #1e1e3e; border: 1px solid #2a2a5a; border-radius: 12px; }
        .table-dark { color: #e0e0e0; }
        .table-dark th { color: #b8b8d4; font-weight: 600; border-bottom: 2px solid #2a2a5a; }
        .table-dark td { vertical-align: middle; border-color: #2a2a5a; }
        .category-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #2a2a5a; background: #0f0f1a; }
        .category-img-placeholder { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #0f0f1a; border-radius: 8px; border: 1px solid #2a2a5a; color: #3a3a5a; }
        .modal-content { background: #1a1a2e; border: 1px solid #2a2a5a; border-radius: 12px; color: #e0e0e0; }
        .modal-header { border-bottom: 1px solid #2a2a5a; padding: 15px 20px; }
        .modal-body { padding: 20px; }
        .modal-footer { border-top: 1px solid #2a2a5a; padding: 15px 20px; }
        .form-label { color: #e0e0e0; font-weight: 500; }
        .form-control, .form-select { background: #0f0f1a; border: 1px solid #2a2a5a; color: #e0e0e0; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background: #0f0f1a; border-color: #6c5ce7; box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25); color: #e0e0e0; }
        .form-control::placeholder { color: #8888aa; }
        .section-title { color: #ffffff; font-family: 'Orbitron', sans-serif; font-weight: 700; }
        .btn-close { filter: invert(1); }
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; color: #3a3a5a; margin-bottom: 20px; }
        .empty-state h5 { color: #e0e0e0; }
        .empty-state p { color: #8888aa; }
        .gaming-logo { font-family: 'Orbitron', sans-serif; font-weight: 900; letter-spacing: 2px; background: linear-gradient(135deg, #6c5ce7 0%, #00d2ff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        #imagePreviewContainer img { max-width: 200px; max-height: 150px; border-radius: 8px; border: 1px solid #2a2a5a; }
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
                    <li class="nav-item"><a class="nav-link active" href="categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="fas fa-box"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="banners.php"><i class="fas fa-image"></i> Banners</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="inventory.php"><i class="fas fa-warehouse"></i> Inventory</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li class="nav-item mt-3"><a class="nav-link text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 admin-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title">Manage Categories</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="fas fa-plus me-2"></i>Add Category
                    </button>
                </div>
                
                <?php displayMessage(); ?>
                
                <div class="card-dark">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-white">Total Categories: <?php echo count($categories); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($categories)): ?>
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <h5>No Categories Found</h5>
                                <p>Click the "Add Category" button to create your first category.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="width: 80px;">Image</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th style="width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td>#<?php echo $cat['category_id']; ?></td>
                                            <td>
                                                <?php if (!empty($cat['category_image'])): ?>
                                                    <?php 
                                                    $imagePath = '../uploads/categories/' . $cat['category_image'];
                                                    if (file_exists($imagePath)): 
                                                    ?>
                                                        <img src="<?php echo $imagePath; ?>" 
                                                             class="category-img" alt="<?php echo htmlspecialchars($cat['category_name']); ?>">
                                                    <?php else: ?>
                                                        <div class="category-img-placeholder">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <div class="category-img-placeholder">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($cat['category_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($cat['category_description'] ?? ''); ?></td>
                                            <td>
                                                <button class="btn btn-info btn-sm edit-category" 
                                                        data-id="<?php echo $cat['category_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($cat['category_name']); ?>"
                                                        data-description="<?php echo htmlspecialchars($cat['category_description'] ?? ''); ?>"
                                                        data-image="<?php echo $cat['category_image']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="?delete=<?php echo $cat['category_id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Delete this category?')">
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
    
    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="categoryForm">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="editId">
                        
                        <div class="mb-3">
                            <label class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="category_name" id="categoryName" placeholder="Enter category name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="category_description" id="categoryDescription" rows="3" placeholder="Enter category description"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="category_image" id="categoryImage" accept="image/*">
                            <small class="text-muted">Recommended size: 200x200px. Leave empty to keep current image.</small>
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <img id="imagePreview" src="" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit category - fill modal with data
        document.querySelectorAll('.edit-category').forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('modalTitle').textContent = 'Edit Category';
                document.getElementById('editId').value = this.dataset.id;
                document.getElementById('categoryName').value = this.dataset.name;
                document.getElementById('categoryDescription').value = this.dataset.description;
                document.getElementById('categoryImage').required = false;
                
                // Show current image if exists
                const preview = document.getElementById('imagePreview');
                const container = document.getElementById('imagePreviewContainer');
                if (this.dataset.image) {
                    preview.src = '../uploads/categories/' + this.dataset.image;
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
                
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            });
        });
        
        // Reset modal on close
        document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('modalTitle').textContent = 'Add Category';
            document.getElementById('editId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categoryImage').value = '';
            document.getElementById('categoryImage').required = true;
            document.getElementById('imagePreviewContainer').style.display = 'none';
        });
        
        // Image preview
        document.getElementById('categoryImage').addEventListener('change', function(e) {
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