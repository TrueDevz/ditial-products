<?php
// contact.php
include 'includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real application, you would send an email here.
        // For now, we'll just simulate success.
        $success = 'Thank you for reaching out! We have received your message and will get back to you shortly.';
    }
}
?>

<section class="page-header" style="background: var(--dark); color: white; padding: 5rem 0; text-align: center; margin-bottom: 4rem;">
    <div class="container">
        <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem;">Contact Us</h1>
        <p style="font-size: 1.25rem; opacity: 0.8; max-width: 700px; margin: 0 auto;">Have questions? We're here to help you build something amazing.</p>
    </div>
</section>

<div class="container" style="margin-bottom: 8rem;">
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 4rem;">
        <!-- Contact Info -->
        <div>
            <h2 style="font-weight: 800; margin-bottom: 2rem;">Get in Touch</h2>
            <div style="margin-bottom: 2.5rem;">
                <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="width: 50px; height: 50px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Our Office</h4>
                        <p style="color: var(--gray); font-size: 0.875rem;">PixVibe Studios HQ, Hyderabad, India</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="width: 50px; height: 50px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Email Us</h4>
                        <p style="color: var(--gray); font-size: 0.875rem;">support@pixvibestudios.in</p>
                    </div>
                </div>

                <div style="display: flex; gap: 1.5rem;">
                    <div style="width: 50px; height: 50px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div>
                        <h4 style="font-weight: 700; margin-bottom: 0.25rem;">Live Chat</h4>
                        <p style="color: var(--gray); font-size: 0.875rem;">Available Mon-Fri, 9am-6pm IST</p>
                    </div>
                </div>
            </div>

            <div style="background: var(--light); padding: 2rem; border-radius: var(--radius);">
                <h4 style="font-weight: 700; margin-bottom: 1rem;">Social Media</h4>
                <div style="display: flex; gap: 1rem;">
                    <a href="#" style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--dark); box-shadow: var(--shadow-sm);"><i class="bi bi-facebook"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--dark); box-shadow: var(--shadow-sm);"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--dark); box-shadow: var(--shadow-sm);"><i class="bi bi-instagram"></i></a>
                    <a href="#" style="width: 40px; height: 40px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--dark); box-shadow: var(--shadow-sm);"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div style="background: white; padding: 4rem; border-radius: var(--radius); box-shadow: var(--shadow-lg);">
            <?php if ($success): ?>
                <div style="background: #dcfce7; color: #15803d; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 1.5rem; border-radius: var(--radius); margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="bi bi-exclamation-circle-fill" style="font-size: 1.5rem;"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Full Name</label>
                        <input type="text" name="name" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Email Address</label>
                        <input type="email" name="email" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" placeholder="john@example.com" required>
                    </div>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Subject</label>
                    <input type="text" name="subject" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" placeholder="How can we help?" required>
                </div>
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Message</label>
                    <textarea name="message" rows="6" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: var(--radius);" placeholder="Tell us more about your inquiry..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem; font-size: 1rem;">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
