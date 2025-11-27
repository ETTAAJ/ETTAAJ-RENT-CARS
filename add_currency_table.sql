-- Create currencies table
CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `rate_to_mad` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default currencies
INSERT INTO `currencies` (`code`, `name`, `symbol`, `rate_to_mad`, `is_active`) VALUES
('MAD', 'Moroccan Dirham', 'MAD', 1.0000, 1),
('USD', 'US Dollar', '$', 10.0000, 1),
('EUR', 'Euro', '€', 11.0000, 1)
ON DUPLICATE KEY UPDATE `rate_to_mad` = VALUES(`rate_to_mad`);

