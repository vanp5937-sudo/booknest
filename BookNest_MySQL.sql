CREATE DATABASE IF NOT EXISTS `booknest` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `booknest`;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `books`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE `users` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(255) NOT NULL, `role` VARCHAR(20) NOT NULL DEFAULT 'customer', `email` VARCHAR(255) NOT NULL, `email_verified_at` TIMESTAMP NULL DEFAULT NULL, `password` VARCHAR(255) NOT NULL, `remember_token` VARCHAR(100) DEFAULT NULL, `created_at` TIMESTAMP NULL DEFAULT NULL, `updated_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `users_email_unique` (`email`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `password_reset_tokens` (`email` VARCHAR(255) NOT NULL, `token` VARCHAR(255) NOT NULL, `created_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`email`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `sessions` (`id` VARCHAR(255) NOT NULL, `user_id` BIGINT UNSIGNED DEFAULT NULL, `ip_address` VARCHAR(45) DEFAULT NULL, `user_agent` TEXT, `payload` LONGTEXT NOT NULL, `last_activity` INT NOT NULL, PRIMARY KEY (`id`), KEY `sessions_user_id_index` (`user_id`), KEY `sessions_last_activity_index` (`last_activity`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `cache` (`key` VARCHAR(255) NOT NULL, `value` MEDIUMTEXT NOT NULL, `expiration` BIGINT NOT NULL, PRIMARY KEY (`key`), KEY `cache_expiration_index` (`expiration`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `cache_locks` (`key` VARCHAR(255) NOT NULL, `owner` VARCHAR(255) NOT NULL, `expiration` BIGINT NOT NULL, PRIMARY KEY (`key`), KEY `cache_locks_expiration_index` (`expiration`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `jobs` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `queue` VARCHAR(255) NOT NULL, `payload` LONGTEXT NOT NULL, `attempts` TINYINT UNSIGNED NOT NULL, `reserved_at` INT UNSIGNED DEFAULT NULL, `available_at` INT UNSIGNED NOT NULL, `created_at` INT UNSIGNED NOT NULL, PRIMARY KEY (`id`), KEY `jobs_queue_index` (`queue`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `job_batches` (`id` VARCHAR(255) NOT NULL, `name` VARCHAR(255) NOT NULL, `total_jobs` INT NOT NULL, `pending_jobs` INT NOT NULL, `failed_jobs` INT NOT NULL, `failed_job_ids` LONGTEXT NOT NULL, `options` MEDIUMTEXT, `cancelled_at` INT DEFAULT NULL, `created_at` INT NOT NULL, `finished_at` INT DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `failed_jobs` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `uuid` VARCHAR(255) NOT NULL, `connection` TEXT NOT NULL, `queue` TEXT NOT NULL, `payload` LONGTEXT NOT NULL, `exception` LONGTEXT NOT NULL, `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `migrations` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `migration` VARCHAR(255) NOT NULL, `batch` INT NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `categories` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(150) NOT NULL, `slug` VARCHAR(180) NOT NULL, `description` TEXT, `is_active` TINYINT(1) NOT NULL DEFAULT 1, `created_at` TIMESTAMP NULL DEFAULT NULL, `updated_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `categories_slug_unique` (`slug`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `books` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `category_id` BIGINT UNSIGNED NOT NULL, `sku` VARCHAR(50) NOT NULL, `title` VARCHAR(255) NOT NULL, `slug` VARCHAR(280) NOT NULL, `author` VARCHAR(180) NOT NULL, `publisher` VARCHAR(180) DEFAULT NULL, `isbn` VARCHAR(20) DEFAULT NULL, `description` TEXT, `price` DECIMAL(12,2) NOT NULL, `original_price` DECIMAL(12,2) DEFAULT NULL, `stock` INT UNSIGNED NOT NULL DEFAULT 0, `image_path` VARCHAR(255) DEFAULT NULL, `is_featured` TINYINT(1) NOT NULL DEFAULT 0, `status` VARCHAR(20) NOT NULL DEFAULT 'active', `created_at` TIMESTAMP NULL DEFAULT NULL, `updated_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `books_sku_unique` (`sku`), UNIQUE KEY `books_slug_unique` (`slug`), UNIQUE KEY `books_isbn_unique` (`isbn`), KEY `books_category_id_status_index` (`category_id`,`status`), KEY `books_title_index` (`title`), CONSTRAINT `books_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `orders` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `user_id` BIGINT UNSIGNED DEFAULT NULL, `order_code` VARCHAR(30) NOT NULL, `customer_name` VARCHAR(255) NOT NULL, `email` VARCHAR(255) NOT NULL, `phone` VARCHAR(30) NOT NULL, `address` TEXT NOT NULL, `subtotal` DECIMAL(12,2) NOT NULL, `total` DECIMAL(12,2) NOT NULL, `status` VARCHAR(30) NOT NULL DEFAULT 'pending', `payment_method` VARCHAR(30) NOT NULL DEFAULT 'cod', `note` TEXT, `created_at` TIMESTAMP NULL DEFAULT NULL, `updated_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`id`), UNIQUE KEY `orders_order_code_unique` (`order_code`), CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `order_items` (`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `order_id` BIGINT UNSIGNED NOT NULL, `book_id` BIGINT UNSIGNED DEFAULT NULL, `book_title` VARCHAR(255) NOT NULL, `sku` VARCHAR(50) NOT NULL, `quantity` INT UNSIGNED NOT NULL, `unit_price` DECIMAL(12,2) NOT NULL, `line_total` DECIMAL(12,2) NOT NULL, `created_at` TIMESTAMP NULL DEFAULT NULL, `updated_at` TIMESTAMP NULL DEFAULT NULL, PRIMARY KEY (`id`), CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE, CONSTRAINT `order_items_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`,`batch`) VALUES
('0001_01_01_000000_create_users_table',1),('0001_01_01_000001_create_cache_table',1),('0001_01_01_000002_create_jobs_table',1),
('2026_10_19_000001_create_categories_table',2),('2026_10_19_000002_create_books_table',2),('2026_10_19_000003_create_orders_table',2),('2026_10_19_000004_create_order_items_table',2),('2026_10_21_000001_repair_missing_categories_table',3);

INSERT INTO `categories` (`id`,`name`,`slug`,`description`,`is_active`,`created_at`,`updated_at`) VALUES
(1,'Văn học - Tiểu thuyết','van-hoc-tieu-thuyet','Văn học Việt Nam và nước ngoài.',1,NOW(),NOW()),
(2,'Kỹ năng sống - Phát triển bản thân','ky-nang-phat-trien-ban-than','Tư duy, thói quen và phát triển cá nhân.',1,NOW(),NOW()),
(3,'Kinh tế - Kinh doanh','kinh-te-kinh-doanh','Marketing, quản trị và tài chính.',1,NOW(),NOW()),
(4,'Công nghệ - Lập trình','cong-nghe-lap-trinh','Lập trình web, Python và cơ sở dữ liệu.',1,NOW(),NOW()),
(5,'Ngoại ngữ','ngoai-ngu','Sách học tiếng Anh và ngoại ngữ.',1,NOW(),NOW()),
(6,'Sách giáo khoa - Tham khảo','sach-giao-khoa-tham-khao','Sách học tập và tài liệu tham khảo.',1,NOW(),NOW()),
(7,'Truyện tranh - Manga','truyen-tranh-manga','Truyện tranh và manga.',1,NOW(),NOW()),
(8,'Sách thiếu nhi','sach-thieu-nhi','Sách tranh và truyện dành cho trẻ em.',1,NOW(),NOW());


INSERT INTO `books`
(`category_id`,`sku`,`title`,`slug`,`author`,`publisher`,`isbn`,`description`,`price`,`original_price`,`stock`,`image_path`,`is_featured`,`status`,`created_at`,`updated_at`) VALUES
(1,'BN-VH-001','Nhà giả kim','nha-gia-kim','Paulo Coelho',NULL,NULL,'Sách mẫu trong danh mục BookNest.',79000,NULL,12,'images/BN-VH/BN-VH-001-Nha_gia_kim.jpg',1,'active',NOW(),NOW()),
(1,'BN-VH-002','Dế Mèn phiêu lưu ký','de-men-phieu-luu-ky','Tô Hoài',NULL,NULL,'Sách mẫu trong danh mục BookNest.',65000,NULL,15,'images/BN-VH/BN-VH-002-De_men_phieu_luu_ky.webp',1,'active',NOW(),NOW()),
(1,'BN-VH-003','Cho tôi xin một vé đi tuổi thơ','cho-toi-xin-mot-ve-di-tuoi-tho','Nguyễn Nhật Ánh',NULL,NULL,'Sách mẫu trong danh mục BookNest.',85000,NULL,10,'images/BN-VH/BN-VH-003-Cho_toi_xin_mot_ve_di_tuoi_tho.webp',1,'active',NOW(),NOW()),
(2,'BN-KN-001','Đắc nhân tâm','dac-nhan-tam','Dale Carnegie',NULL,NULL,'Sách mẫu trong danh mục BookNest.',86000,NULL,14,'images/BN-KN/BN-KN-001-Dac_Nhan_Tam.webp',1,'active',NOW(),NOW()),
(2,'BN-KN-002','Thay đổi tí hon, hiệu quả bất ngờ','thay-doi-ti-hon-hieu-qua-bat-ngo','James Clear',NULL,NULL,'Sách mẫu trong danh mục BookNest.',119000,NULL,10,'images/BN-KN/BN-KN-002-Thay_doi_ti_hon_hieu_qua_bat_ngo.jpeg',0,'active',NOW(),NOW()),
(2,'BN-KN-003','Tuổi trẻ đáng giá bao nhiêu','tuoi-tre-dang-gia-bao-nhieu','Rosie Nguyễn',NULL,NULL,'Sách mẫu trong danh mục BookNest.',80000,NULL,12,'images/BN-KN/BN-KN-003-Tuoi_tre_dang_gia_bao_nhieu.webp',0,'active',NOW(),NOW()),
(3,'BN-KD-001','Marketing căn bản','marketing-can-ban','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',135000,NULL,8,'images/BN-KD/BN-KD-001_Marketing_can_ban.webp',0,'active',NOW(),NOW()),
(3,'BN-KD-002','Từ tốt đến vĩ đại','tu-tot-den-vi-dai','Jim Collins',NULL,NULL,'Sách mẫu trong danh mục BookNest.',150000,NULL,7,'images/BN-KD/BN-KD-002_Tu_tot_den_vi_dai.jpg',0,'active',NOW(),NOW()),
(4,'BN-CN-001','Nhập môn lập trình Python','nhap-mon-lap-trinh-python','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',165000,NULL,10,'images/BN-CN/BN-CN-001-Nhap_mon_lap_trinh_Python.webp',0,'active',NOW(),NOW()),
(4,'BN-CN-002','Lập trình web với HTML, CSS và JavaScript','lap-trinh-web-html-css-javascript','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',189000,NULL,9,'images/BN-CN/BN-CN-002-Lap_trinh_web_voi_HTML_CSS_va_ JavaScript.jpg',0,'active',NOW(),NOW()),
(4,'BN-CN-003','Cơ sở dữ liệu và SQL thực hành','co-so-du-lieu-va-sql-thuc-hanh','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',175000,NULL,8,'images/BN-CN/BN-CN-003-Co_so_du_lieu_SQL.jpg',0,'active',NOW(),NOW()),
(5,'BN-NN-001','English Grammar in Use - Basic','english-grammar-in-use-basic','Raymond Murphy',NULL,NULL,'Sách mẫu trong danh mục BookNest.',145000,NULL,8,'images/BN-NN/BN-NN-001-English_Grammar_in_Use_Basic.jpg',0,'active',NOW(),NOW()),
(5,'BN-NN-002','3000 từ vựng tiếng Anh thông dụng','3000-tu-vung-tieng-anh-thong-dung','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',95000,NULL,15,'images/BN-NN/BN-NN-002-3000_tu_vung_tieng_Anh_thong_dung.webp',0,'active',NOW(),NOW()),
(6,'BN-TH-001','Toán tư duy và bài tập thực hành','toan-tu-duy-bai-tap-thuc-hanh','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',75000,NULL,12,'images/BN-TH/BN-TH-001_Toan_tu_duy_va_bai_tap_thuc_hanh.webp',0,'active',NOW(),NOW()),
(6,'BN-TH-002','Phương pháp học tập hiệu quả','phuong-phap-hoc-tap-hieu-qua','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',89000,NULL,10,'images/BN-TH/BN-TH-002-Phuong_phap_hoc_tap_hieu_qua.jpg',0,'active',NOW(),NOW()),
(7,'BN-TT-001','Doraemon - Tập 1','doraemon-tap-1','Fujiko F. Fujio',NULL,NULL,'Sách mẫu trong danh mục BookNest.',22000,NULL,20,'images/BN-TT/BN-TT-001-Doraemon _tap1.webp',0,'active',NOW(),NOW()),
(7,'BN-TT-002','Thám tử lừng danh Conan - Tập 1','tham-tu-lung-danh-conan-tap-1','Gosho Aoyama',NULL,NULL,'Sách mẫu trong danh mục BookNest.',25000,NULL,18,'images/BN-TT/BN-TT-002-Tham_tu_lung_danh_Conan _tap1.jpg',0,'active',NOW(),NOW()),
(7,'BN-TT-003','One Piece - Tập 1','one-piece-tap-1','Eiichiro Oda',NULL,NULL,'Sách mẫu trong danh mục BookNest.',25000,NULL,18,'images/BN-TT/BN-TT-003-OnePiece_tap1.webp',0,'active',NOW(),NOW()),
(8,'BN-TN-001','Truyện cổ tích Việt Nam','truyen-co-tich-viet-nam','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',69000,NULL,12,'images/BN-TN/BN-TN-001-Truyen_co_tich_Viet_Nam.webp',0,'active',NOW(),NOW()),
(8,'BN-TN-002','Khám phá thế giới động vật','kham-pha-the-gioi-dong-vat','Nhóm tác giả minh họa',NULL,NULL,'Sách mẫu trong danh mục BookNest.',79000,NULL,10,'images/BN-TN/BN-TN-002-Kham_pha_the_gioi_dong_vat.webp',0,'active',NOW(),NOW());
