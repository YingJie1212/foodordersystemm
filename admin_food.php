<?php
include 'db.php';
session_start();

// Handle add, edit, delete
if (isset($_POST['add_food'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $sql = "INSERT INTO menu_items (name, price, category) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sds', $name, $price, $category);
    $stmt->execute();
    header('Location: admin_food.php'); exit;
}
if (isset($_POST['edit_food'])) {
    $id = intval($_POST['food_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $sql = "UPDATE menu_items SET name=?, price=?, category=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sdsi', $name, $price, $category, $id);
    $stmt->execute();
    header('Location: admin_food.php'); exit;
}
if (isset($_POST['delete_food'])) {
    $id = intval($_POST['food_id']);
    $conn->query("DELETE FROM menu_items WHERE id=$id");
    header('Location: admin_food.php'); exit;
}

// Get all food items
$foods = $conn->query("SELECT * FROM menu_items ORDER BY id DESC");
// Get all categories
$categories = $conn->query("SELECT DISTINCT category FROM menu_items");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Food Management</title>
    <style>
        body { background: #f5f7fa; color: #2d3748; font-family: Inter, Arial, sans-serif; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 2rem; }
        h2 { margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 2rem; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e1e5eb; text-align: left; }
        th { background: #e8ebf0; }
        tr:last-child td { border-bottom: none; }
        .actions button { margin-right: 8px; }
        .btn { padding: 7px 18px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-add { background: #38a169; color: #fff; }
        .btn-edit { background: #3182ce; color: #fff; }
        .btn-delete { background: #e53e3e; color: #fff; }
        .btn:hover { opacity: 0.85; }
        form.inline { display: inline; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 6px; font-weight: 500; }
        input, select { padding: 7px 10px; border-radius: 6px; border: 1px solid #d3d9e4; width: 100%; }
        .modal { display: none; position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.15); align-items:center; justify-content:center; z-index:9999; }
        .modal-content { background:#fff; padding:2rem; border-radius:10px; min-width:320px; max-width:95vw; box-shadow:0 8px 32px rgba(0,0,0,0.12); position:relative; }
        .modal-close { position:absolute; top:10px; right:16px; font-size:2rem; background:none; border:none; cursor:pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Food Management</h2>
    <button class="btn btn-add" onclick="openAddModal()">Add Food</button>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price (RM)</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while($food = $foods->fetch_assoc()): ?>
            <tr>
                <td><?= $food['id'] ?></td>
                <td><?= htmlspecialchars($food['name']) ?></td>
                <td><?= number_format($food['price'],2) ?></td>
                <td><?= htmlspecialchars($food['category']) ?></td>
                <td class="actions">
                    <button class="btn btn-edit" onclick="openEditModal(<?= $food['id'] ?>, <?= json_encode($food['name']) ?>, <?= $food['price'] ?>, <?= json_encode($food['category']) ?>)">Edit</button>
                    <form method="post" class="inline" onsubmit="return confirm('Delete this food?')">
                        <input type="hidden" name="food_id" value="<?= $food['id'] ?>">
                        <button type="submit" name="delete_food" class="btn btn-delete">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeAddModal()">&times;</button>
        <h3>Add Food</h3>
        <form method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Price (RM)</label>
                <input type="number" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category" required>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>"><?= htmlspecialchars($cat['category']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="add_food" class="btn btn-add">Add</button>
        </form>
    </div>
</div>
<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <button class="modal-close" onclick="closeEditModal()">&times;</button>
        <h3>Edit Food</h3>
        <form method="post">
            <input type="hidden" name="food_id" id="editFoodId">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" id="editName" required>
            </div>
            <div class="form-group">
                <label>Price (RM)</label>
                <input type="number" name="price" id="editPrice" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="editCategory" required>
            </div>
            <button type="submit" name="edit_food" class="btn btn-edit">Save</button>
        </form>
    </div>
</div>
<script>
function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
}
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}
function openEditModal(id, name, price, category) {
    document.getElementById('editModal').style.display = 'flex';
    document.getElementById('editFoodId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editPrice').value = price;
    document.getElementById('editCategory').value = category;
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
document.querySelectorAll('.modal').forEach(function(modal){
    modal.addEventListener('click',function(e){if(e.target===this)this.style.display='none';});
});
</script>
</body>
</html>
