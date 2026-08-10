## 2024-06-03 - [File Upload RCE and Webhook Timing Attacks]
**Vulnerability:** Found two vulnerabilities:
1. Use of `getClientOriginalExtension()` which relies on user-provided file extension instead of actual file mime-type for image uploads in `MenuAdminController.php`.
2. Timing attack vulnerability during cryptographic signature verification using `!==` in `PaymentController.php`.
**Learning:** `getClientOriginalExtension()` should never be trusted as it can be easily spoofed, allowing malicious scripts to bypass extension checks. Similarly, standard string comparison operators (`==` or `!==`) short-circuit upon finding the first difference, allowing attackers to guess signatures byte-by-byte via timing attacks.
**Prevention:** Always use `$file->extension()` (which determines extension via MIME type) instead of user-supplied extensions in Laravel. Always use `hash_equals()` for comparing cryptographic signatures to ensure constant-time comparison.

## 2026-08-10 - [Laravel Environment Variable Vulnerability]
**Vulnerability:** Found calls to `env()` outside of config files (e.g., in controllers, services, and views).
**Learning:** If a Laravel application uses configuration caching (`php artisan config:cache`), any calls to `env()` outside of configuration files will return `null`. In security contexts (like signature verification), this can cause a critical failure (e.g. comparing hashes using null keys).
**Prevention:** Always define environment variables in `config/` files and use the `config()` helper to access them throughout the application.
