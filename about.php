<?php
// about.php
include 'includes/header.php';
?>

<section class="page-header" style="background: var(--dark); color: white; padding: 5rem 0; text-align: center; margin-bottom: 4rem;">
    <div class="container">
        <h1 style="font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem;">About PixVibe Studios</h1>
        <p style="font-size: 1.25rem; opacity: 0.8; max-width: 700px; margin: 0 auto;">Innovating the digital marketplace with premium assets and first-class support.</p>
    </div>
</section>

<div class="container" style="margin-bottom: 8rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
        <div>
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 2rem;">Our Journey</h2>
            <p style="color: var(--gray); font-size: 1.125rem; line-height: 1.8; margin-bottom: 1.5rem;">
                PixVibe Studios was founded with a single mission: to empower developers and creators with high-quality digital assets that accelerate their workflow. We believe that great design and robust code should be accessible to everyone.
            </p>
            <p style="color: var(--gray); font-size: 1.125rem; line-height: 1.8;">
                From premium PHP scripts to state-of-the-art HTML templates, our marketplace is curated to ensure that every item meets the highest standards of quality and performance.
            </p>
        </div>
        <div style="background: var(--light); border-radius: 2rem; overflow: hidden; box-shadow: var(--shadow-lg);">
             <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1000" alt="About Us" style="width: 100%; height: 500px; object-fit: cover;">
        </div>
    </div>

    <div style="margin-top: 8rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem;">
        <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
            <div style="width: 70px; height: 70px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2rem;">
                <i class="bi bi-gem"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Premium Quality</h3>
            <p style="color: var(--gray);">We hand-pick every item on our marketplace to ensure it meets our rigorous quality standards.</p>
        </div>

        <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
            <div style="width: 70px; height: 70px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2rem;">
                <i class="bi bi-headset"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Expert Support</h3>
            <p style="color: var(--gray);">Our team and authors are here to help you succeed with every purchase you make.</p>
        </div>

        <div style="background: white; padding: 3rem; border-radius: var(--radius); box-shadow: var(--shadow); text-align: center;">
            <div style="width: 70px; height: 70px; background: rgba(37, 99, 235, 0.1); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1rem;">Secure Payments</h3>
            <p style="color: var(--gray);">Your security is our priority. We use industry-standard encryption for all transactions.</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
