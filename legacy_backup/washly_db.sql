-- phpMyAdmin SQL Dump
-- Skema Database untuk WASHLY Laundry

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin
INSERT INTO `users` (`role`, `name`, `phone`, `email`, `password`, `address`) VALUES
('admin', 'Admin Washly', '081234567890', 'admin@washly.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Outlet Utama'); 
-- password = 'password'

-- --------------------------------------------------------
-- Table: services
-- --------------------------------------------------------
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_regular` int(11) NOT NULL,
  `price_express` int(11) DEFAULT NULL,
  `unit` enum('kg','pcs') NOT NULL DEFAULT 'kg',
  `est_regular` varchar(50) NOT NULL,
  `est_express` varchar(50) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Data Layanan
INSERT INTO `services` (`name`, `description`, `price_regular`, `price_express`, `unit`, `est_regular`, `est_express`) VALUES
('Cuci + Setrika', 'Pakaian bersih, rapi dan siap dipakai langsung.', 10000, 15000, 'kg', '2-3 hari', '1 hari'),
('Cuci Kering', 'Dicuci dan dikeringkan mesin, tanpa setrika.', 7000, 12000, 'kg', '1-2 hari', '12 jam'),
('Setrika Saja', 'Pakaian disetrika rapi dan wangi.', 6000, 10000, 'kg', '1-2 hari', '12 jam'),
('Bed Cover', 'Pencucian khusus untuk bed cover berbagai ukuran.', 25000, 35000, 'pcs', '3-4 hari', '2 hari'),
('Sepatu', 'Deep cleaning untuk berbagai jenis sepatu.', 30000, 50000, 'pcs', '3-5 hari', '2 hari');

-- --------------------------------------------------------
-- Table: service_areas
-- --------------------------------------------------------
CREATE TABLE `service_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_name` varchar(100) NOT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `service_areas` (`area_name`, `distance_km`) VALUES
('Kecamatan Pancoran', 2.5),
('Kecamatan Tebet', 4.0),
('Kecamatan Mampang', 5.5),
('Kecamatan Pasar Minggu', 7.0);

-- --------------------------------------------------------
-- Table: orders
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `speed` enum('Regular','Express') NOT NULL DEFAULT 'Regular',
  
  -- Pickup Details
  `pickup_name` varchar(100) NOT NULL,
  `pickup_phone` varchar(20) NOT NULL,
  `pickup_address` text NOT NULL,
  `pickup_time` datetime NOT NULL,
  
  -- Delivery Details
  `delivery_name` varchar(100) NOT NULL,
  `delivery_phone` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_time` datetime NOT NULL,
  
  `notes` text DEFAULT NULL,
  
  -- Price & Weight Calculation (Diisi Admin)
  `weight` decimal(10,2) DEFAULT NULL,
  `total_items` int(11) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  
  -- Order Status
  `order_status` enum('Order Dibuat','Menunggu Pickup','Laundry Dijemput','Diterima Outlet','Diproses','Siap Diantar','Sedang Diantar','Selesai Diantar','Order Selesai') NOT NULL DEFAULT 'Order Dibuat',
  
  -- Payment Status
  `payment_method` enum('QRIS','Transfer') DEFAULT NULL,
  `payment_status` enum('Belum Dibayar','Menunggu Pembayaran','Lunas','Gagal') NOT NULL DEFAULT 'Belum Dibayar',
  
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `customer_id` (`customer_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: order_items (Detail jumlah barang dari customer)
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: order_photos (Dokumentasi)
-- --------------------------------------------------------
CREATE TABLE `order_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `uploader_id` int(11) NOT NULL,
  `stage` enum('Sebelum Pickup','Diterima Outlet','Siap Diantar') NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: order_tracking (Riwayat Status)
-- --------------------------------------------------------
CREATE TABLE `order_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Constraints
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_photos`
  ADD CONSTRAINT `fk_photo_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

ALTER TABLE `order_tracking`
  ADD CONSTRAINT `fk_tracking_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

COMMIT;
