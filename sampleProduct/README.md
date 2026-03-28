# Sample Product - License System Test

This is a sample project demonstrate how to integrate the **Pixvibe Studios License Verification System** into your digital products.

## How it works:
1.  **Configuration**: The `config.php` file contains your local Marketplace API URL and your unique Bearer Token.
2.  **Manager**: The `LicenseManager.php` class handles the secure communication with the marketplace.
3.  **Verification**: The `index.php` provides a user interface for customers to enter their purchase code and activate the product.

## Integration Steps:
- Copy `LicenseManager.php` into your project.
- Configure your `API_URL` and `API_TOKEN`.
- Call `LicenseManager::verify($purchase_code)` in your activation flow.

---
Created by AntiGravity for Pixvibe Studios.
