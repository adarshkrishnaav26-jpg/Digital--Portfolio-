DIGITAL PORTFOLIO - STARTER PROJECT

Requirements:
- PHP with PDO SQLite enabled.
- A PHP-enabled web server.

Run in Termux:
1. Extract this folder into ~/mywebsite/DigitalPortfolio
2. Open Termux and run:
   cd ~/mywebsite/DigitalPortfolio
3. Start the server (use this exact command on your current Termux setup):
   php -d opcache.enable=0 -d opcache.enable_cli=0 -d opcache.lockfile_path="$HOME" -S 127.0.0.1:8080 -t "$HOME/mywebsite/DigitalPortfolio"
4. Keep Termux open.
5. Open Chrome: http://127.0.0.1:8080

Pages:
- index.php: homepage
- register.php: student registration
- login.php: student login
- dashboard.php: add and view achievements
- logout.php: sign out

Database:
- portfolio.db is created automatically when db.php is first loaded.
- Passwords are stored using PHP password_hash().
- Achievement status is Pending by default.

Important:
This is a starter/demo project. It does not yet include admin verification,
certificate file uploads, QR generation, or blockchain integration.
Before public deployment, add CSRF protection, stronger validation, HTTPS,
upload security, backups, and an admin-only verification workflow.
