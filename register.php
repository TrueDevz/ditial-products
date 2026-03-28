<?php
// register.php
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = 'Email or Username already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $success = 'Registration successful! You can now <a href="/digitalProducts/login.php">login</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<section class="container" style="max-width: 500px; margin-top: 5rem; margin-bottom: 5rem;">
    <div style="background-color: var(--white); padding: 3rem; border-radius: 1rem; box-shadow: var(--shadow-lg);">
        <h2 style="font-weight: 800; margin-bottom: 0.5rem; text-align: center;">Join DigitalMarket</h2>
        <p style="color: var(--gray); text-align: center; margin-bottom: 2rem;">Create your account to start buying and selling.</p>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="bi bi-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="bi bi-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Username</label>
                <input type="text" name="username" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Email Address</label>
                <input type="email" name="email" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Password</label>
                <input type="password" name="password" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Confirm Password</label>
                <input type="password" name="confirm_password" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Account Type</label>
                <select name="role" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);">
                    <option value="user">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">Create Account</button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.875rem; color: var(--gray);">
            Already have an account? <a href="/digitalProducts/login.php" style="color: var(--primary); font-weight: 600;">Sign In</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
