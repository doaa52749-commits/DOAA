CREATE DATABASE IF NOT EXISTS nanotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nanotech;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS deliveries;
DROP TABLE IF EXISTS promotions;
DROP TABLE IF EXISTS additions;
DROP TABLE IF EXISTS parts;
DROP TABLE IF EXISTS washers;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS admin_users;

CREATE TABLE admin_users (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_sha256 CHAR(64) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE customers (
  customer_id INT AUTO_INCREMENT PRIMARY KEY,
  national_id VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(100) NULL,
  phone VARCHAR(20) NOT NULL,
  address VARCHAR(255) NULL,
  password_hash VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE washers (
  washer_id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(100) NOT NULL,
  version VARCHAR(100) NOT NULL,
  size VARCHAR(50) NOT NULL,
  color VARCHAR(50) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  quantity INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE parts (
  part_id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(100) NOT NULL,
  model VARCHAR(100) NOT NULL,
  version VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  quantity INT NOT NULL DEFAULT 0,
  image_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE additions (
  add_id INT AUTO_INCREMENT PRIMARY KEY,
  description VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE deliveries (
  delivery_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NULL,
  phone VARCHAR(20) NOT NULL,
  location VARCHAR(255) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_deliveries_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE promotions (
  promo_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  content TEXT NOT NULL,
  image_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
  order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(30) NOT NULL DEFAULT 'new',
  delivery_id INT NULL,
  CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_orders_delivery FOREIGN KEY (delivery_id) REFERENCES deliveries(delivery_id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  item_type ENUM('washer','part','addition','custom') NOT NULL,
  ref_id INT NULL,
  description VARCHAR(255) NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO admin_users (username, password_sha256) VALUES
('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9');

INSERT INTO washers (type, version, size, color, price, quantity, image_url) VALUES
('مغسلة', 'V1', 'كبير', 'أبيض', 1200.00, 10, 'assets/img/washer1.jpg'),
('مغسلة', 'V2', 'متوسط', 'رمادي', 900.00, 5, 'assets/img/washer2.jpg');

INSERT INTO parts (type, model, version, price, quantity, image_url) VALUES
('فلتر', 'F-100', '2025', 50.00, 20, 'assets/img/part1.jpg'),
('خرطوم', 'H-200', '2024', 30.00, 15, 'assets/img/part2.jpg');

INSERT INTO additions (description, price) VALUES
('تركيب', 100.00),
('صيانة', 80.00);

INSERT INTO promotions (title, content, image_url, is_active) VALUES
('عرض افتتاح', 'خصم خاص لفترة محدودة على بعض المنتجات.', NULL, 1),
('خدمة جديدة', 'تمت إضافة خدمات توصيل أسرع داخل المدينة.', NULL, 1);
