<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $desc = trim($_POST['description']);
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $target = 'uploads/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $img_name;
        }
    }

    // 插入菜单
    $stmt = $conn->prepare("INSERT INTO menu_items (name, category, price, description, image, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdss", $name, $category, $price, $desc, $image);
    $stmt->execute();

    header("Location: menu_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Food Item | Menu Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a4a4a;
            --primary-dark: #333;
            --primary-light: #666;
            --accent-color: #d33;
            --background-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #333;
            --text-light: #777;
            --border-color: #e0e0e0;
            --success-color: #28a745;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            width: 100%;
            max-width: 700px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 30px;
            position: relative;
        }
        
        .card-header h2 {
            font-weight: 600;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-header h2 i {
            font-size: 1.5rem;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--primary-dark);
            font-size: 1rem;
            display: flex;
            align-items: center;
        }
        
        label i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        .required {
            color: var(--accent-color);
            margin-left: 4px;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
            background-color: #fcfcfc;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 74, 74, 0.1);
            background-color: #fff;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23555' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 40px;
        }
        
        .file-input-container {
            position: relative;
        }
        
        .file-input-container input[type="file"] {
            padding: 12px 16px;
            height: auto;
        }
        
        .file-info {
            font-size: 13px;
            color: var(--text-light);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .btn i {
            font-size: 1.1rem;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            margin-top: 25px;
            color: var(--primary-light);
            text-decoration: none;
            font-size: 15px;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .back-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
            gap: 5px;
        }
        
        .back-link i {
            transition: var(--transition);
            margin-right: 5px;
        }
        
        .back-link:hover i {
            transform: translateX(-3px);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        
        @media (max-width: 576px) {
            .form-row {
                flex-direction: column;
                gap: 25px;
            }
            
            .card-body {
                padding: 20px;
            }
            
            .container {
                padding: 0 15px;
                margin: 20px auto;
            }
        }
        
        .notification {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            gap: 10px;
        }
        
        .notification.success {
            background-color: #e6f7ee;
            color: var(--success-color);
            border: 1px solid #b8e6cd;
        }
        
        .preview-container {
            margin-top: 15px;
            text-align: center;
            display: none;
        }
        
        .preview-container img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-utensils"></i> Add New Menu Item</h2>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" id="menuForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-tag"></i> Food Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="e.g., Margherita Pizza" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category"><i class="fas fa-list"></i> Category <span class="required">*</span></label>
                            <select id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="Appetizer">Appetizer</option>
                                <option value="Main Course">Main Course</option>
                                <option value="Dessert">Dessert</option>
                                <option value="Drink">Drink</option>
                                <option value="Side">Side Dish</option>
                                <option value="Special">Special</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="price"><i class="fas fa-dollar-sign"></i> Price ($) <span class="required">*</span></label>
                            <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Description</label>
                        <textarea id="description" name="description" placeholder="Describe the ingredients, taste, and any special notes..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image"><i class="fas fa-image"></i> Food Image</label>
                        <div class="file-input-container">
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <p class="file-info"><i class="fas fa-info-circle"></i> JPEG, PNG or GIF recommended. Max size: 2MB.</p>
                        
                        <div class="preview-container" id="previewContainer">
                            <img id="imagePreview" src="#" alt="Image preview">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Add to Menu</button>
                </form>
                <a href="menu_list.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Menu List</a>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('previewContainer');
            const previewImage = document.getElementById('imagePreview');
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        });
        
        // Form validation
        document.getElementById('menuForm').addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#d33';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>