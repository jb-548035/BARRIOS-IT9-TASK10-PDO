<?php
require 'insert.php';
require 'update.php';
require 'delete.php';
require 'select.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple PDO CRUD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold">Simple PDO CRUD</h2>
        <p class="text-muted">User & Orders Management</p>
    </div>

    <?php
    $editUser = null;

    if (isset($_GET['edit'])) {
        $user_id = $_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>

    <!-- FORM CARD -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-black">
            <?= $editUser ? 'Update User and Product' : 'Add New User and Product' ?>
        </div>

        <div class="card-body">
            <form method="POST">

                <?php if (!empty($editUser)): ?>
                    <input type="hidden" name="user_id" value="<?= $editUser['user_id'] ?>">
                <?php endif; ?>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" 
                               class="form-control" 
                               name="name"
                               placeholder="John Doe"
                               value="<?= !empty($editUser) ? $editUser['name'] : '' ?>" 
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               name="email"
                               placeholder="email@example.com"
                               value="<?= !empty($editUser) ? $editUser['email'] : '' ?>" 
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Product</label>
                        <input type="text" 
                               class="form-control" 
                               name="product" 
                               placeholder="e.g. BYD Sealion"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Amount</label>
                        <input type="number" 
                               step="0.01"
                               class="form-control" 
                               name="amount" 
                               placeholder="₱0.00"
                               required>
                    </div>

                </div>

                <div class="mt-4">

                    <?php if (!empty($editUser)): ?>
                        <button type="submit" name="update" class="btn btn-success">
                            Update
                        </button>
                        <a href="landing.php" class="btn btn-secondary">
                            Cancel
                        </a>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add User and Product
                        </button>
                    <?php endif; ?>

                </div>

            </form>
        </div>
    </div>


    <!-- USER and PRODUCTS TABLE -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            User and Order List
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['user_id'] ?></td>
                            <td><?= $user['name'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['product'] ?></td>
                            <td>₱<?= number_format($user['amount'], 2) ?></td>
                            <td>
                                <a href="?edit=<?= $user['user_id'] ?>" 
                                   class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <a href="?delete=<?= $user['orders_id'] ?>" 
                                  class="btn btn-sm btn-outline-danger"
                                  onclick="return confirm('Are you sure you want to delete this order?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="text-muted py-3">
                                No users found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('amount').addEventListener('input', function (e) {
    let value = e.target.value.replace(/,/g, '');

    if (!isNaN(value) && value !== '') {
        e.target.value = Number(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});
</script>

</body>
</html>