-- ALTERNATIVE to 'php spark migrate' for hosting without terminal access.
-- Create/select an EMPTY MySQL database in phpMyAdmin before importing.
-- Import this schema only once. Then import your local database export to move
-- your staff password hashes and data (do not run both imports in the same DB).
CREATE TABLE products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 price DECIMAL(10,2) NOT NULL,
 stock_quantity INT NOT NULL DEFAULT 0,
 image VARCHAR(255) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE customers (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(100) NOT NULL,
 email VARCHAR(100) NOT NULL,
 phone VARCHAR(20) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 full_name VARCHAR(100) NOT NULL,
 password VARCHAR(255) NOT NULL,
 avatar VARCHAR(255) NULL,
 active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE sales (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 product_id INT UNSIGNED NOT NULL,
 customer_id INT UNSIGNED NULL,
 sold_by INT UNSIGNED NOT NULL,
 quantity INT NOT NULL,
 total_price DECIMAL(10,2) NOT NULL,
 created_at DATETIME NOT NULL,
 CONSTRAINT fk_sale_product FOREIGN KEY(product_id) REFERENCES products(id),
 CONSTRAINT fk_sale_customer FOREIGN KEY(customer_id) REFERENCES customers(id),
 CONSTRAINT fk_sale_staff FOREIGN KEY(sold_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
