<?php
session_start();
include 'db.php';

// 查询所有菜单
$result = $conn->query("SELECT * FROM menu_items ORDER BY created_at DESC");
$menu_items = [];
while ($row = $result->fetch_assoc()) {
    $menu_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap">
    <style>
        :root {
            --gray-50: #fafafa;
            --gray-100: #f5f5f5;
            --gray-200: #eeeeee;
            --gray-300: #e0e0e0;
            --gray-400: #bdbdbd;
            --gray-500: #9e9e9e;
            --gray-600: #757575;
            --gray-700: #616161;
            --gray-800: #424242;
            --gray-900: #212121;
            --primary: #616161;
            --primary-hover: #424242;
            --danger: #d32f2f;
            --danger-hover: #b71c1c;
            --text-primary: #212121;
            --text-secondary: #616161;
            --border: #e0e0e0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--gray-100);
            color: var(--text-primary);
            line-height: 1.6;
            font-weight: 400;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background-color: white;
            color: var(--text-primary);
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .app-title {
            font-weight: 500;
            font-size: 1.4rem;
            letter-spacing: -0.5px;
        }
        
        .main-content {
            padding: 2.5rem 0;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.6rem;
            font-weight: 500;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.65rem 1.4rem;
            background-color: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
        }
        
        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        .card {
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1.1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-size: 0.95rem;
        }
        
        th {
            background-color: var(--gray-50);
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: var(--gray-50);
        }
        
        .action-link {
            color: var(--danger);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.9rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            border: 1px solid transparent;
        }
        
        .action-link:hover {
            background-color: rgba(211, 47, 47, 0.05);
            border: 1px solid rgba(211, 47, 47, 0.1);
        }
        
        .price {
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .description {
            color: var(--text-secondary);
            max-width: 300px;
            line-height: 1.5;
            font-size: 0.95rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3.5rem;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 3.2rem;
            margin-bottom: 1.2rem;
            color: var(--gray-400);
            opacity: 0.7;
        }
        
        .empty-state h3 {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        .empty-state p {
            margin-top: 0.5rem;
            color: var(--text-secondary);
        }
        
        footer {
            text-align: center;
            padding: 2rem 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            border-top: 1px solid var(--border);
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-title {
                margin-bottom: 1.2rem;
            }
            
            th, td {
                padding: 0.9rem;
            }
            
            .description {
                max-width: 200px;
            }
            
            .container {
                padding: 0 16px;
            }
        }
    </style>
</head>
<body>
    
    
    <div class="container main-content">
        <div class="page-header">
            <h2 class="page-title">Menu Items</h2>
            <div>
                <a href="admin_orders.php" class="btn" style="margin-right:10px;">
                    <i class="fas fa-list"></i> Orders
                </a>
                <a href="add_food.php" class="btn">
                    <i>+</i> Add New Food Item
                </a>
            </div>
        </div>
        
        <div class="card">
            <?php if (count($menu_items) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu_items as $item): ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="food image" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                                <?php else: ?>
                                    <span style="color:#bbb;">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td class="price">RM<?= number_format($item['price'], 2) ?></td>
                            <td class="description"><?= htmlspecialchars($item['description']) ?></td>
                            <td>
                                <a href="delete_food.php?id=<?= $item['id'] ?>" class="action-link" onclick="return confirm('Are you sure you want to delete this item?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i>🍽️</i>
                <h3>No menu items found</h3>
                <p>Get started by adding your first food item to the menu.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>