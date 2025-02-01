# Karaoke Songbook Manager

A simple self-hosted PHP-based karaoke songbook management system with a public song list and an admin panel for managing songs.

## Origins

A local venue near me has karaoke every week, and the song list is a printed book.  I gave the dj a pro-bono upgrade hosted on my homelab webserver.  ...and I wanted to share the code in case anyone else wants to set up their own for themself or someone else.
*enjoy*

## Features

- **Public Song List**: Viewable by all, with search and sorting functionality.
- **Admin Panel**: Allows admins to log in, add, edit, and delete songs.
- **User Authentication**: Admin authentication with password hashing and salting.
- **Password Management**: Initial password setup and password change functionality.
- **Dark Mode UI**: Optimized for use in dimly lit environments (e.g., bars) with a charcoal gray theme.

## Dependendies

- Nginx (or apache)
- MariaDB (or MySQL)
- PHP with PHP-mysql

## Project Structure

```
/path_to/web_content/
│── karaoke/          # Public song list (visitor page)
│── karaoke-admin/    # Admin panel (requires login)
│── karaoke-cfg/      # Secure storage for shared config (not publicly accessible)
```

## Setup Instructions

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

## Security Recommendations

- Enable HTTPS using Let's Encrypt or another SSL provider.
- Regularly update the server and dependencies.
- Restrict database access to localhost.
- Use strong passwords for database and admin login.

## License

This project is open-source and free to use/copy/alter/dissect/pull snippets from/whatever at your own discression.  I hold no liability for what you do with this or any derivitives thereof.
