<?php
session_start();
include 'db.php';

$filter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT o.id AS order_id, 
               COALESCE(u.name, '访客') AS user_name, 
               o.total_price, o.created_at, o.status,
               oi.id AS order_item_id, oi.quantity, m.name AS menu_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN menu_items m ON oi.menu_item_id = m.id";

if ($filter) {
    $sql .= " WHERE o.status = '".$conn->real_escape_string($filter)."'";
}

$sql .= " ORDER BY o.created_at DESC";

$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['user'] = $row['user_name'];
    $orders[$row['order_id']]['total'] = $row['total_price'];
    $orders[$row['order_id']]['created'] = $row['created_at'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['items'][] = [
        'menu' => $row['menu_name'],
        'qty' => $row['quantity'],
        'order_item_id' => $row['order_item_id']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>
    <style>
        :root {
            --primary-bg: #f8f9fa;
            --secondary-bg: #e9ecef;
            --card-bg: #ffffff;
            --text-primary: #212529;
            --text-secondary: #495057;
            --border-color: #dee2e6;
            --accent-color: #6c757d;
            --hover-color: #e9ecef;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --border-radius: 0.5rem;
            --box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
            --box-shadow-hover: 0 1rem 2.5rem rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            --gradient-primary: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            --gradient-success: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            --gradient-warning: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            --gradient-danger: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            --gradient-info: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            background-color: var(--primary-bg);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h2 {
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
        }
        
        .btn {
            display: inline-block;
            padding: 0.6rem 1.2rem;
            background: var(--gradient-primary);
            color: white;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            box-shadow: var(--box-shadow);
            border: none;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .filter-form {
            margin-bottom: 2.5rem;
            background-color: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: var(--transition);
        }
        
        .filter-form:hover {
            box-shadow: var(--box-shadow-hover);
        }
        
        .form-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        label {
            font-weight: 500;
            color: var(--text-secondary);
            white-space: nowrap;
            font-size: 0.95rem;
        }
        
        select, button, input {
            padding: 0.7rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--card-bg);
            color: var(--text-primary);
            transition: var(--transition);
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        select {
            min-width: 160px;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }
        
        select:focus, button:focus, input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
            transform: translateY(-1px);
        }
        
        button {
            background: var(--gradient-primary);
            color: white;
            cursor: pointer;
            font-weight: 500;
            border: none;
            padding: 0.7rem 1.5rem;
            box-shadow: 0 4px 6px rgba(108, 117, 125, 0.3);
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(108, 117, 125, 0.4);
        }
        
        .order-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.75rem;
            margin-bottom: 1.75rem;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border-left: 4px solid var(--accent-color);
            position: relative;
            overflow: hidden;
        }
        
        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: var(--transition);
        }
        
        .order-card:hover {
            box-shadow: var(--box-shadow-hover);
            transform: translateY(-4px);
        }
        
        .order-card:hover::before {
            opacity: 1;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        
        .order-info {
            flex: 1;
            min-width: 250px;
        }
        
        .order-info p {
            margin-bottom: 0.75rem;
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        
        .order-info strong {
            font-weight: 600;
            min-width: 100px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .status-form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.85rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status-pending {
            background: var(--gradient-warning);
            color: #000;
        }
        
        .status-preparing {
            background: var(--gradient-info);
            color: white;
        }
        
        .status-completed {
            background: var(--gradient-success);
            color: white;
        }
        
        .status-cancelled {
            background: var(--gradient-danger);
            color: white;
        }
        
        .order-items {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .order-items h4 {
            margin-bottom: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .order-items h4::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 2px;
            background: var(--gradient-primary);
        }
        
        .order-items ul {
            list-style-type: none;
        }
        
        .order-items li {
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }
        
        .order-items li:hover {
            background-color: rgba(108, 117, 125, 0.05);
            padding-left: 0.5rem;
            padding-right: 0.5rem;
            border-radius: var(--border-radius);
        }
        
        .order-items li:last-child {
            border-bottom: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            color: var(--text-secondary);
            transition: var(--transition);
        }
        
        .empty-state:hover {
            box-shadow: var(--box-shadow-hover);
            transform: translateY(-2px);
        }
        
        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }
        
        .empty-state p {
            margin-top: 1rem;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1.25rem;
            }
            
            .filter-form {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.25rem;
            }
            
            .form-group {
                width: 100%;
            }
            
            select {
                flex-grow: 1;
                width: 100%;
            }
            
            .order-header {
                flex-direction: column;
            }
            
            .status-form {
                width: 100%;
            }
            
            .status-form select {
                flex-grow: 1;
                width: 100%;
            }
            
            .status-form button {
                width: 100%;
            }
            
            .order-info p {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
            
            .order-info strong {
                min-width: auto;
            }
        }
        
        /* 添加一些微妙的动画效果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .order-card {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        /* 为不同状态的订单添加不同的左边框颜色 */
        .order-card[data-status="pending"] {
            border-left-color: var(--warning-color);
        }
        
        .order-card[data-status="preparing"] {
            border-left-color: var(--info-color);
        }
        
        .order-card[data-status="completed"] {
            border-left-color: var(--success-color);
        }
        
        .order-card[data-status="cancelled"] {
            border-left-color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="container">
    <h2>Order Management System</h2>
    <a href="menu_list.php" class="btn">Menu List</a>
        <form method="get" class="filter-form">
            <div class="form-group">
                <label for="status">Filter by Status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">All Orders</option>
                    <option value="pending" <?= $filter=='pending'?'selected':'' ?>>Pending</option>
                    <option value="preparing" <?= $filter=='preparing'?'selected':'' ?>>Preparing</option>
                    <option value="completed" <?= $filter=='completed'?'selected':'' ?>>Completed</option>
                    <option value="cancelled" <?= $filter=='cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
            </div>
        </form>
        
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <h3>No orders found</h3>
                <p>There are currently no orders matching your criteria.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $id => $order): ?>
                <div class="order-card" data-status="<?= $order['status'] ?>">
                    <div class="order-header">
                        <div class="order-info">
                            <p><strong>Order ID:</strong> <span>#<?= $id ?></span></p>
                            <p><strong>Total:</strong> <span>RM<?= number_format($order['total'], 2) ?></span></p>
                            <p><strong>Order Time:</strong> <span><?= date('Y-m-d H:i', strtotime($order['created'])) ?></span></p>
                            <p><strong>Status:</strong> 
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?php 
                                        switch($order['status']) {
                                            case 'pending': echo 'Pending'; break;
                                            case 'preparing': echo 'Preparing'; break;
                                            case 'completed': echo 'Completed'; break;
                                            case 'cancelled': echo 'Cancelled'; break;
                                            default: echo $order['status'];
                                        }
                                    ?>
                                </span>
                            </p>
                        </div>
                        
                        <form method="post" action="update_status.php" class="status-form">
                            <input type="hidden" name="order_id" value="<?= $id ?>">
                            <select name="status">
                                <option value="pending" <?= $order['status']=='pending'?'selected':'' ?>>Pending</option>
                                <option value="preparing" <?= $order['status']=='preparing'?'selected':'' ?>>Preparing</option>
                                <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                            <button type="submit">Update Status</button>
                        </form>
                    </div>
                    
                    <div class="order-items">
                        <h4>Order Items</h4>
                        <ul>
                            <?php foreach ($order['items'] as $item): ?>
                                <li>
                                    <span><?= $item['menu'] ?></span>
                                    <span>× <?= $item['qty'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>