<?php
require_once 'includes/session_check.php';
require_once 'includes/session_check.php';
$page_title = "Account Settings";
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>Account Settings</h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <div class="card">
        <h3>Change Username or Password</h3>
        <form action="actions/update_account.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
            </div>
            <hr>
            <p>Leave password fields blank to keep your current password.</p>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password">
            </div>
            <button type="submit">Update Account</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>