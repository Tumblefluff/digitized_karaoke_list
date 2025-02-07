# 🎤 Karaoke Songbook Manager
A simple self-hosted PHP-based karaoke songbook management system with a public song list and an admin panel for managing songs.

[Demo](https://karaoke.demo.tumblefluff.link/) - [Admin-Demo](https://admin.karaoke.demo.tumblefluff.link/login.php)※

*(※ This demo is in **read-only mode** adding, editing, and deleting songs are disabled, and password changes are locked out. Feel free to explore, but no actual changes can be made.)*

## ▶️ Origins

A local venue near me has karaoke every week, and the song list is a printed book.  I gave the dj a pro-bono upgrade hosted on my homelab webserver.  ...and I wanted to share the code in case anyone else wants to set up their own for themself or someone else.
*enjoy*

## 📋 Features

- **Public Song List**: Viewable by all, with search and sorting functionality.
- **Admin Panel**: Allows admins to log in, add, edit, and delete songs.
- **User Authentication**: Admin authentication with password hashing and salting.
- **Password Management**: Initial password setup and password change functionality.
- **Dark Mode UI**: Optimized for use in dimly lit environments (e.g., bars) with a charcoal gray theme.

## 🔩 Dependendies

- Nginx (or apache)
- MariaDB (or MySQL)
- PHP with PHP-mysql

## 🏗️ Project Structure

```
/path_to/web_content/
│── karaoke/          # Public song list (visitor page)
│── karaoke-admin/    # Admin panel (requires login)
│── karaoke-cfg/      # Secure storage for shared config (not publicly accessible)
```

## ⏩ Setup Instructions

### **1. Install Dependencies**

Ensure you have a LAMP/LEMP stack installed:

*Pick one **Nginx** or Apache2*
```sh
sudo apt update && sudo apt install nginx
```
```sh
sudo apt update && sudo apt install apache2
```

***MariaDB** or MySQL*

```
sudo apt install mariadb-server
```
```
sudo apt install mysql-server mysql-client
```

Enable PHP and necessary PHP extensions:
```sh
sudo apt install php php-mysql php-curl php-mbstring php-xml php-cli
```

### **2. Database Setup**

Create a MariaDB database, tables and user:

```sql
CREATE DATABASE karaoke_db;
CREATE USER 'karaoke'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE VIEW, SHOW VIEW ON karaoke_db.* TO 'karaoke'@'localhost';
FLUSH PRIVILEGES;

USE karaoke_db;

-- Create songs table
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL
);

-- Create users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);
```

Import the database schema:

```sh
mysql -u karaoke -p karaoke_db < schema.sql
```

### **3. Configure Nginx (or Apache)**

Ensure you have the necessary Nginx or Apache server blocks:

- Using **Nginx**: `admin.karaoke.nginx.conf` *and* `karaoke.nginx.conf`
- Using **Apache**: `admin.karaoke.apache.conf` *and* `karaoke.apache.conf`

Restart Nginx/Apache:

```sh
sudo systemctl restart nginx
```
```sh
sudo systemctl restart apache2
```

### **4. Configure PHP Database Connection**

Edit `db.php` located in `/mnt/web/karaoke-cfg/`:

```php
<?php
$host = 'localhost';
$dbname = 'songlist_db';
$username = 'karaoke';
$password = 'your_secure_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

### **5. Modify files to reflect your credentials**

- **/karaoke-cfg/db.php**: `update line 5`  (set database user's password)
- **/karaoke-cfg/veri.php**: `update line 2`  (set verification code)
- optional **/karaoke-cfg/db.php**: `update lines 3 and/or 4`  (change these if you used different names during setup)

### **6. Add Admin User(s)**

- **Add an entry to the users table**:  (repeat for each user)
```
INSERT INTO users (username, password_hash) 
VALUES ('new_user_name', '');
```
*Be sure to leave the password_hash BLANK - the system will recognize this as a "new user" and on initial login it will prompt the user to create their password and require a verification code.*

### **7. Access the System**

- **Public Song List**: `http://karaoke.domain.tld`
- **Admin Panel**: `http://admin.karaoke.domain.tld`

Log in with the credentials set up in the database.

## 🔐 Security Recommendations

- Enable HTTPS using Let's Encrypt or another SSL provider.
- Regularly update the server and dependencies.
- Restrict database access to localhost.
- Use strong passwords for database and admin login.

## 📄 License

This project is open-source and free to use/copy/alter/dissect/pull snippets from/whatever at your own discression.  I hold no liability for what you do with this or any derivitives thereof.

# 📌 System Requirements

This project is lightweight and can run on low-power hardware. Below are the minimum and recommended system requirements.

## 🛠️ Hardware Requirements

| Component      | Minimum Requirement | Recommended for Better Performance |
|---------------|---------------------|------------------------------------|
| **CPU**       | 1GHz ARM/x86 (Single-core) | 1.5GHz Quad-core (e.g., Raspberry Pi 4) |
| **RAM**       | 512MB                | 2GB or more |
| **Storage**   | 1GB free space       | 8GB or more (for logs and future expansion) |
| **Networking**| Ethernet / Wi-Fi     | Wired Ethernet preferred |

## 💾 Software Requirements

| Software            | Minimum Version | Recommended |
|---------------------|----------------|-------------|
| **OS**             | Debian-based (Raspberry Pi OS, Ubuntu, Armbian) | Debian 12 |
| **Web Server**     | Apache 2.4 / Nginx 1.18 | Nginx |
| **PHP**           | 7.4+ | 8.1+ (Better performance & security) |
| **Database**      | MariaDB 10+ | Latest stable (10.5+) |
| **SSL Support**   | Let's Encrypt or Self-Signed | Let's Encrypt |

---

## 🔍 Suitable Hosting & Hardware Examples

### ✅ Self-Hosting Options

- **Low Traffic / Personal Use (1-5 users at a time)**
  - Raspberry Pi Zero 2W
  - Orange Pi Zero 2 or 2W
  - Any old laptop or mini PC with 1GB RAM

- **Medium Traffic (5-50 users at a time)**
  - Raspberry Pi 3B+ or 4
  - Orange Pi 4
  - Libre Computer Le Potato or Sweet Potato
  - Intel N100 / N5105 mini PC
  - Old desktop with 2GB+ RAM

- **Higher Traffic (100+ users at a time)**
  - Intel N100 / i3 Mini PC (4GB+ RAM)
  - Virtual Private Server (VPS)
  - Old desktop with **SSD storage** for speed

### ✅ Paid Hosting Options

For those who prefer managed hosting, choose a plan that **supports PHP, MySQL/MariaDB, and SSL**.

| Provider  | Minimum Plan Required | Notes |
|-----------|----------------------|-------|
| **HostGator** | "Hatchling Plan" | Shared hosting, supports MySQL, cPanel-based |
| **Bluehost** | "Basic Shared" | Supports PHP/MySQL, free SSL included |
| **SiteGround** | "StartUp" | Faster than most shared hosting, MySQL/MariaDB support |
| **Vultr** | "Cloud Compute ($5/mo)" | Full root access, great for running optimized setups |
| **Linode** | "Nanode ($5/mo)" | Good for self-managed hosting with MariaDB |

---

## 🚀 Optimizations for SBCs

- Use **PHP-FPM** instead of mod_php for better performance.
- Optimize MariaDB by reducing buffer sizes for low-memory devices.
- Enable **basic caching** (APCu, Redis, or simple file-based caching).
- Use a **lightweight OS** like Debian Minimal or Alpine.
- **Disable unnecessary services** to free up RAM.
