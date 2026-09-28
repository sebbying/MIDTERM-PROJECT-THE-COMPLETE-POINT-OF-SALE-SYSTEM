# IT0049 Midterm: Complete Point-of-Sale System

CodeIgniter 4 + MySQL POS. Products, customers, and staff can be added, viewed, edited and archived. Staff login protects every management page. Uploaded images are checked for real image content and displayed. Sales save the staff, optional customer, product, quantity, price and date; each sale locks its product row, checks stock, then saves the sale and decreases stock in one transaction.

## What to download and where it goes

Extract the supplied ZIP so the folder becomes `C:\xampp\htdocs\pos-midterm`. **Do not nest it inside another `pos-midterm` folder.** Keep Apache and MySQL running in XAMPP Control Panel. You need internet once on your computer to download CodeIgniter and Composer. The framework and `vendor` dependencies are installed by the supplied script. Keep the resulting `app`, `public`, `vendor`, `writable`, `spark`, and Composer files together.

### 1. Install CodeIgniter on Windows

Open PowerShell in `C:\xampp\htdocs\pos-midterm` (click the File Explorer address bar, enter `powershell`, press Enter), then run:

```powershell
powershell -ExecutionPolicy Bypass -File .\setup-windows.ps1
```

This script uses `C:\xampp\php\php.exe` directly, so your PHP does **not** need to be in PATH. It downloads Composer and an official CI4 app starter, then adds its missing framework files without replacing the included POS code. If it says an extension is missing, open `C:\xampp\php\php.ini`, enable `extension=intl`, `extension=mbstring`, `extension=mysqli`, and `extension=fileinfo` (remove leading `;` where present), save, then rerun. Check the PHP version with `& 'C:\xampp\php\php.exe' -v`; current CodeIgniter releases may require PHP 8.2 or newer. Composer will report exact requirements before installation.

### 2. Create the local database

1. Open `http://localhost/phpmyadmin`.
2. Click **New**, type `pos_midterm`, choose `utf8mb4_unicode_ci` if offered, and click **Create**.
3. Open `C:\xampp\htdocs\pos-midterm\.env` in Notepad. It is made from `.env.example` by the setup script. These should be separate lines:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/pos-midterm/public/'
app.indexPage = ''
database.default.hostname = localhost
database.default.database = pos_midterm
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

The default XAMPP database user is commonly `root` with an empty password; use your actual values if you changed them. `app.baseURL` must contain the complete URL, once, inside quotes. Do not paste `app.baseURL =` inside its value.

### 3. Create tables and first staff account

In PowerShell in the project folder, run:

```powershell
& 'C:\xampp\php\php.exe' spark migrate
$env:POS_ADMIN_USER = 'admin'
$env:POS_ADMIN_NAME = 'My Name'
$env:POS_ADMIN_PASSWORD = 'Choose-A-Long-Unique-Password'
& 'C:\xampp\php\php.exe' spark db:seed AdminSeeder
Remove-Item Env:POS_ADMIN_PASSWORD
```

Change the name and password to yours. The password needs at least 12 characters. The seeder will not overwrite any existing staff account. To run the migration again on an already imported database, first check what was imported; do not import `database.sql` into a database that already has these tables.

### 4. Open and check the app

Go to **http://localhost/pos-midterm/public/** and sign in with the username and password you chose. Add a product with stock 5. Add a customer and another staff member, optionally uploading JPG/PNG/WebP images. Record a sale of 2 units: product stock should show 3 and Sales History should show the transaction. Try selling 4: the app should reject it and stock should stay 3. Log out and try opening `/pos-midterm/public/products`: it should send you to login.

If you get a 404, verify the folder name, the URL ending in `/public/`, and that Apache is running. If uploads fail, check that `public/uploads/products` and `public/uploads/avatars` exist and are writable. If the database connection fails, verify the `.env` database name and that MySQL is running. The app stores login sessions and logs in `writable`, which must also be writable.

## GitHub submission

After setup, upload the **contents** of `C:\xampp\htdocs\pos-midterm` to a new GitHub repository. GitHub Desktop can add the local project folder, publish a repository, then push. Commit `app/`, `public/`, `spark`, `composer.json`, `composer.lock`, `database.sql`, the migration, seeder, this README and other generated CI4 starter files. Keep `.gitignore`. **Never upload `.env`, `composer.phar`, `vendor/`, database exports with private data, or real upload images to a public GitHub repository.** The repository remains installable by running `composer install` (or this setup script) after cloning. Add your repository URL to your submission.

## Hosting on InfinityFree (after the local test passes)

Hosting accounts vary; verify your account's PHP version and extensions in its control panel against the PHP version/requirements of your generated `composer.lock`. You cannot assume that the hosting account has Composer. The steps below copy the locally installed `vendor` folder to the host.

1. Register/login at [InfinityFree](https://www.infinityfree.com/), create a hosting account, and select a free domain. Wait until the domain is active.
2. Open the hosting control panel → **MySQL Databases**. Create a database. Record the **full database name**, **MySQL hostname**, **database username**, and **password** shown for that hosting account. The hostname is generally different from `localhost`.
3. From local `http://localhost/phpmyadmin`, select `pos_midterm` → **Export** → **Quick** → **SQL** → **Go**. Store this export privately: it includes your staff password hashes and application data. In the InfinityFree control panel, open **phpMyAdmin** for the new hosting database → **Import** → choose that SQL export → **Go**. If you instead want an empty database, import the included `database.sql`, but first create a staff account locally and export its `users` table; the SQL schema alone has no login account. Do not put the export in the website folder or public GitHub.
4. Use FTP (for large numbers of files, FileZilla is easier than the web file manager) to upload the **contents** of your installed local `pos-midterm` folder to your domain's `htdocs` folder, including `app`, `vendor`, `public`, `writable`, `.htaccess` and `spark`. Skip local `.env`, `composer.phar`, `.git`, local log/cache/session files, and `setup-windows.ps1`. Keep empty writable subfolders and both upload directories. For a public demonstration you may omit actual local uploaded images; if you do, their old images will show as broken. Upload the matching image files if you need them on the live copy.
5. Inside hosted `htdocs`, create `.env` by copying `.env.example` (or edit a local copy and upload it via FTP). Set `CI_ENVIRONMENT = production`; set `app.baseURL = 'https://YOUR-DOMAIN/public/'` with **your actual HTTPS domain**, keep `app.indexPage = ''`, and replace all five database connection values with the hosting values. `database.default.DBDriver = MySQLi` and `database.default.port = 3306` are normally appropriate; use values supplied by your host. Do not use the local `root` credentials online.
6. Open `https://YOUR-DOMAIN/public/` and sign in with the staff account from the exported database. Check products, sale, stock, sales history and uploads on the live site. Add your actual working URL to your submission. You cannot have a real hosted URL until you create the hosting account and upload the files.

**Hosting checks:** If you see a 500 error, check the host PHP version and required extensions, the `vendor` upload, writable `htdocs/writable`, `.env` syntax, and logs under `writable/logs`. If routes fail, confirm that the starter's `public/.htaccess` transferred and Apache rewrite works. InfinityFree may set PHP limitations; verify on your own account. The project root `.htaccess` blocks direct access to private directories; use the `/public/` URL supplied above. Treat your hosted `.env` and SQL export as private.

## Architecture / marking guide

| Rubric item | Implementation |
| --- | --- |
| Routing and MVC | Explicit routes in `app/Config/Routes.php`; controllers, models, views in their standard folders |
| Relational design | Migration in `app/Database/Migrations`, alternative `database.sql`, foreign keys across four tables |
| Product / customer / staff management | List, create, edit, and archive routes; archived records remain for sale history |
| Security | Auth route filter, CSRF form checks, hashed passwords, escaped view output, validated uploads, no auto-routing |
| Sales | Product row lock, stock check, insert and decrement in one DB transaction |
| History | Join on products, customers, staff, including walk-in customers |

**Project decisions:** Archiving is used instead of physical deletion so past transactions remain understandable. A new product may start with zero stock. Only JPG, PNG and WebP files no larger than 2 MB are accepted; server-side image content and MIME type are checked and filenames are randomized. Uploaded images remain under `public/uploads`; `.htaccess` blocks executable file types in those folders. Staff accounts are peers, so any logged-in staff member can manage other staff accounts. This is a coursework POS, not a payment processor. Use a strong first account password and change it after demonstrating if you shared credentials.
