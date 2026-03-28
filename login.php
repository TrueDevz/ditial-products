<?php
// login.php
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                header('Location: ' . BASE_URL . '/admin/index.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<section class="container" style="max-width: 500px; margin-top: 5rem; margin-bottom: 5rem;">
    <div style="background-color: var(--white); padding: 3rem; border-radius: 1rem; box-shadow: var(--shadow-lg);">
        <h2 style="font-weight: 800; margin-bottom: 0.5rem; text-align: center;">Welcome Back</h2>
        <p style="color: var(--gray); text-align: center; margin-bottom: 2rem;">Sign in to your account to continue.</p>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: var(--radius); margin-bottom: 1.5rem; font-size: 0.875rem;">
                <i class="bi bi-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Email Address</label>
                <input type="email" name="email" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Password</label>
                <input type="password" name="password" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">Sign In</button>
        </form>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.875rem; color: var(--gray);">
            Don't have an account? <a href="<?php echo BASE_URL; ?>/register.php" style="color: var(--primary); font-weight: 600;">Create Account</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
