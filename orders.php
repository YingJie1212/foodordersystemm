<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

// 修复SQL语法错误，支持未登录时显示全部订单
$where = '';
if (!empty($user_id)) {
    $where = "WHERE user_id = " . intval($user_id);
}
$result = $conn->query("SELECT * FROM orders $where ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>历史订单</title>
    <style>
        :root {
            --primary: #2c3e50;
            --primary-light: #34495e;
            --secondary: #e74c3c;
            --accent: #3498db;
            --light: #ecf0f1;
            --grey-light: #f5f7fa;
            --grey: #a0aec0;
            --grey-dark: #718096;
            --text: #2d3748;
            --text-light: #4a5568;
            --success: #48bb78;
            --warning: #ecc94b;
            --error: #f56565;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            color: var(--text);
            background: linear-gradient(135deg, var(--grey-light) 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: -0.025em;
            position: relative;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -0.5rem;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: var(--radius);
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            text-decoration: none;
        }

        .btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn i {
            margin-right: 0.5rem;
        }

        .orders-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .order-card {
            background: white;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-left: 4px solid var(--primary);
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            background: var(--grey-light);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .order-id {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .order-date {
            color: var(--grey-dark);
            font-size: 0.9rem;
        }

        .order-body {
            padding: 1.5rem;
        }

        .order-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        .order-price span {
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: 600;
        }

        .order-details {
            border-top: 1px dashed #e2e8f0;
            padding-top: 1rem;
        }

        .order-items-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--grey-dark);
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 500;
        }

        .item-quantity {
            color: var(--grey-dark);
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--grey);
            margin-bottom: 1rem;
        }

        .empty-text {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }

        /* 动画效果 */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .order-card {
            animation: fadeIn 0.5s ease-out;
        }

        .order-card:nth-child(2) { animation-delay: 0.1s; }
        .order-card:nth-child(3) { animation-delay: 0.2s; }
        .order-card:nth-child(4) { animation-delay: 0.3s; }
        .order-card:nth-child(5) { animation-delay: 0.4s; }

        /* 媒体查询 - 平板设备 */
        @media (max-width: 992px) {
            .container {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.9rem;
            }
            
            .order-header {
                padding: 1rem 1.25rem;
            }
            
            .order-body {
                padding: 1.25rem;
            }
        }

        /* 媒体查询 - 大屏手机 */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                margin-bottom: 2rem;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .order-price {
                font-size: 1.1rem;
            }
            
            .empty-state {
                padding: 2rem;
            }
        }

        /* 媒体查询 - 小屏手机 */
        @media (max-width: 576px) {
            .container {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.7rem;
            }
            
            .btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .order-card {
                border-left-width: 3px;
            }
            
            .order-body {
                padding: 1rem;
            }
            
            .order-item {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .empty-icon {
                font-size: 2.5rem;
            }
            
            .empty-text {
                font-size: 1rem;
            }
        }

        /* 媒体查询 - 超小屏手机 */
        @media (max-width: 400px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .page-title::after {
                width: 40px;
                height: 3px;
            }
            
            .order-id {
                font-size: 1rem;
            }
            
            .order-date {
                font-size: 0.8rem;
            }
            
            .order-items-title {
                font-size: 0.85rem;
            }
        }

        /* 高对比度模式支持 */
        @media (prefers-contrast: high) {
            .order-card {
                border: 2px solid var(--primary);
            }
            
            .order-header {
                border-bottom: 2px solid var(--primary);
            }
            
            .order-details {
                border-top: 2px dashed var(--primary);
            }
        }

        /* 减少动画偏好 */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            
            .btn:hover {
                transform: none;
            }
            
            .order-card:hover {
                transform: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">历史订单</h1>
            <a href="menu.php" class="btn">
                <i class="fas fa-arrow-left"></i>返回菜单
            </a>
        </div>
        
        <?php if ($result->num_rows > 0): ?>
            <div class="orders-container">
                <?php while ($order = $result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">订单号: <?= $order['id'] ?></div>
                            <div class="order-date"><?= $order['created_at'] ?></div>
                        </div>
                        <div class="order-body">
                            <div class="order-price">
                                <span>总价:</span> RM<?= number_format($order['total_price'], 2) ?>
                            </div>
                            
                            <div class="order-details">
                                <div class="order-items-title">订单内容</div>
                                <?php
                                $order_id = $order['id'];
                                $items = $conn->query("
                                    SELECT m.name, oi.quantity FROM order_items oi 
                                    JOIN menu_items m ON oi.menu_item_id = m.id 
                                    WHERE oi.order_id = $order_id
                                ");
                                while ($item = $items->fetch_assoc()): ?>
                                    <div class="order-item">
                                        <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                        <span class="item-quantity">× <?= $item['quantity'] ?></span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="empty-text">暂无订单记录</div>
                <a href="menu.php" class="btn">
                    <i class="fas fa-utensils"></i>浏览菜单
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>