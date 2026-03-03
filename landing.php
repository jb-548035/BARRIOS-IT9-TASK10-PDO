<?php
// Ensure these files are in the same directory
require 'insert.php';
require 'update.php';
require 'delete.php';
require 'select.php';

// Handling Edit State
$editUser = null;
if (isset($_GET['edit'])) {
    $user_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDO CRUD</title>
    
    <!-- Modern Typography: Inter -->
    <link href="https://fonts.googleapis.com" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg: #f8fafc;
            --accent: #0f172a;
            --radius: 16px;
            --success: #10b981;
            --danger: #ef4444;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--bg); 
            color: var(--accent); 
        }

        /* Enhanced Card UI (Non-Bootstrap Style) */
        .card { 
            border: none; 
            border-radius: var(--radius); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); 
            transition: transform 0.2s ease;
        }

        .form-control { 
            border-radius: 10px; 
            border: 1px solid #e2e8f0; 
            padding: 12px;
            background: #fff;
        }

        .form-control:focus { 
            border-color: var(--accent); 
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05); 
        }

        .btn-primary { 
            background: var(--accent); 
            border: none; 
            border-radius: 10px; 
            padding: 12px 24px; 
            font-weight: 600; 
        }

        /* Custom Toast Notification System */
        #toast-container {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .custom-toast {
            background: #ffffff;
            color: #1e293b;
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
            min-width: 350px;
            max-width: 400px;
            animation: slideIn 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28) forwards;
        }

        @keyframes slideIn { 
            from { transform: translateX(-110%); opacity: 0; } 
            to { transform: translateX(0); opacity: 1; } 
        }

        .toast-actions { 
            display: flex; 
            gap: 10px; 
            margin-top: 15px; 
        }

        .btn-toast-confirm { 
            background: var(--danger); 
            color: white; 
            border: none; 
            border-radius: 8px; 
            padding: 8px 16px; 
            font-size: 0.85rem; 
            font-weight: 600;
        }

        .btn-toast-cancel { 
            background: #f1f5f9; 
            color: #475569; 
            border: none; 
            border-radius: 8px; 
            padding: 8px 16px; 
            font-size: 0.85rem; 
        }
    </style>
</head>
<body class="py-5">

<!-- Notification Anchor -->
<div id="toast-container"></div>

<div class="container">
    <header class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <h1 class="fw-800 mb-0">PDO <span class="text-muted fw-normal">CRUD</span></h1>
            <p class="text-secondary mt-1">Manage your active users and recent product orders.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                Total Records: <?= count($users) ?>
            </span>
        </div>
    </header>

    <div class="row g-4">
        <!-- Input Section -->
        <div class="col-lg-4">
            <div class="card p-4">
                <h5 class="mb-4 fw-bold">
                    <?= $editUser ? '<i class="bi bi-pencil-square me-2 text-warning"></i>Update Entry' : '<i class="bi bi-plus-lg me-2 text-primary"></i>New Entry' ?>
                </h5>
                <form method="POST">
                    <?php if (!empty($editUser)): ?>
                        <input type="hidden" name="user_id" value="<?= $editUser['user_id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-800">User Name</label>
                        <input type="text" class="form-control" name="name" placeholder="John Doe" value="<?= $editUser['name'] ?? '' ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-800">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="name@email.com" value="<?= $editUser['email'] ?? '' ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-7 mb-3">
                            <label class="form-label small text-muted text-uppercase fw-800">Product</label>
                            <input type="text" class="form-control" name="product" placeholder="e.g. BYD" required>
                        </div>
                        <div class="col-5 mb-3">
                            <label class="form-label small text-muted text-uppercase fw-800">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">₱</span>
                                <input type="number" step="0.01" class="form-control border-start-0" name="amount" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-grid">
                        <?php if (!empty($editUser)): ?>
                            <button type="submit" name="update" class="btn btn-primary shadow-sm">Apply Changes</button>
                            <a href="landing.php" class="btn btn-link text-muted mt-2 text-decoration-none small">Discard Updates</a>
                        <?php else: ?>
                            <button type="submit" name="add" class="btn btn-primary shadow-sm"><i class="bi bi-plus-circle-dotted me-2"></i>Save Record</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="col-lg-8">
            <div class="card overflow-hidden h-100">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0 py-3 small text-muted text-uppercase fw-800">Client</th>
                                <th class="border-0 small text-muted text-uppercase fw-800">Product</th>
                                <th class="border-0 small text-muted text-uppercase fw-800">Amount</th>
                                <th class="text-end pe-4 border-0 small text-muted text-uppercase fw-800">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold"><?= $user['name'] ?></div>
                                    <div class="small text-muted font-monospace"><?= $user['email'] ?></div>
                                </td>
                                <td><span class="badge rounded-pill bg-light text-dark border fw-normal px-3"><?= $user['product'] ?></span></td>
                                <td class="fw-bold text-success">₱<?= number_format($user['amount'], 2) ?></td>
                                <td class="text-end pe-4">
                                    <a href="?edit=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-dark border-0 rounded-circle mx-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-- Triggering custom conversational toast instead of browser confirm -->
                                    <button type="button" 
                                            onclick="confirmDelete('delete.php?delete=<?= $user['orders_id'] ?>')" 
                                            class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Delete">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                                    No records found in the database.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
/**
 * TOAST ENGINE
 * Forces the AI/Browser to render custom components rather than default popups.
 */
function showToast(message, type = 'success', action = null) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    // UI Style Logic
    const accentColor = type === 'danger' ? 'var(--danger)' : 'var(--success)';
    toast.className = 'custom-toast';
    toast.style.borderLeft = `6px solid ${accentColor}`;
    
    let actionHtml = '';
    if (action) {
        actionHtml = `
            <div class="toast-actions">
                <button class="btn-toast-confirm" onclick="window.location.href='${action.url}'">${action.label}</button>
                <button class="btn-toast-cancel" onclick="this.parentElement.parentElement.remove()">No, keep it</button>
            </div>
        `;
    }

    toast.innerHTML = `
        <div style="font-size: 0.95rem; font-weight: 500; line-height: 1.4;">${message}</div>
        ${actionHtml}
    `;

    container.appendChild(toast);

    // Auto-dismiss for basic alerts
    if (!action) {
        setTimeout(() => {
            toast.style.transform = 'translateX(-120%)';
            toast.style.opacity = '0';
            toast.style.transition = 'all 0.5s ease-in-out';
            setTimeout(() => toast.remove(), 500);
        }, 4500);
    }
}

/**
 * CONVERSATIONAL LISTENERS
 */
function confirmDelete(deleteUrl) {
    showToast("Wait! Are you sure you want to delete this record? This action is permanent.", "danger", {
        url: deleteUrl,
        label: "Yes, delete this item"
    });
}

// Checking URL parameters for success signals from logic files
window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const msg = params.get('msg');

    if (msg === 'added') {
        showToast("Success! The new record has been created.");
    } else if (msg === 'updated') {
        showToast("Updates applied! The information is now current.");
    } else if (msg === 'deleted') {
        showToast("Done. That entry has been removed from the system.", "info");
    }

    // Modern behavior: Clear URL so refresh doesn't duplicate toast
    if (msg) window.history.replaceState({}, document.title, window.location.pathname);
});
</script>

</body>
</html>
