## 2024-06-03 - [File Upload RCE and Webhook Timing Attacks]
**Vulnerability:** Found two vulnerabilities:
1. Use of `getClientOriginalExtension()` which relies on user-provided file extension instead of actual file mime-type for image uploads in `MenuAdminController.php`.
2. Timing attack vulnerability during cryptographic signature verification using `!==` in `PaymentController.php`.
**Learning:** `getClientOriginalExtension()` should never be trusted as it can be easily spoofed, allowing malicious scripts to bypass extension checks. Similarly, standard string comparison operators (`==` or `!==`) short-circuit upon finding the first difference, allowing attackers to guess signatures byte-by-byte via timing attacks.
**Prevention:** Always use `$file->extension()` (which determines extension via MIME type) instead of user-supplied extensions in Laravel. Always use `hash_equals()` for comparing cryptographic signatures to ensure constant-time comparison.
