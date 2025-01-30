
-- Volcando datos para la tabla abprod.users: ~0 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'daril diaz', 'darildiaz29@gmail.com', NULL, '$2y$12$jTiVApGQp1l42JW6YtCea.ErO4rIXgLxc199h4cgwIFMoG9WamWG6', NULL, '2025-01-27 17:14:15', '2025-01-27 17:14:15');

INSERT INTO `categories` (`id`, `name`, `description`, `order`, `is_important`, `created_at`, `updated_at`) VALUES
	(1, 'Camiseta', NULL, 1, 1, '2025-01-27 17:17:12', '2025-01-27 17:17:12'),
	(2, 'Short', NULL, 3, 1, '2025-01-28 16:34:38', '2025-01-28 16:36:14'),
	(3, 'Camisilla', NULL, 2, 1, '2025-01-28 16:35:01', '2025-01-28 16:35:01'),
	(4, 'Media', NULL, 4, 1, '2025-01-28 16:35:19', '2025-01-28 16:36:06'),
	(5, 'Camiseta Manga largas', NULL, 5, 1, '2025-01-28 16:35:54', '2025-01-28 16:35:54'),
	(6, 'Botinera', NULL, 6, 1, '2025-01-28 13:43:19', '2025-01-28 16:44:55'),
	(7, 'Blusa Allegra', NULL, 8, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(8, 'Camisa', NULL, 9, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(9, 'Remera', NULL, 10, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(10, 'Remera Polo', NULL, 11, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(11, 'Buzo Saco', NULL, 12, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(12, 'Buzo Pantalón', NULL, 13, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(13, 'Chaleco', NULL, 14, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(14, 'Canguro', NULL, 15, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(15, 'Bandera', NULL, 16, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(16, 'Kepis', NULL, 17, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(17, 'Canguro', NULL, 18, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19'),
	(18, 'Calza', NULL, 19, 0, '2025-01-28 13:43:19', '2025-01-28 13:43:19');

INSERT INTO `centers` (`id`, `name`, `level`, `created_at`, `updated_at`) VALUES
	(1, 'Diagramacion ', 1, '2025-01-27 17:17:33', '2025-01-27 17:17:33'),
	(2, 'Impresion', 2, '2025-01-27 17:17:49', '2025-01-27 17:17:49'),
	(3, 'Sublimacion', 3, '2025-01-27 17:18:01', '2025-01-27 17:18:01'),
	(4, 'Corte', 4, '2025-01-27 17:18:07', '2025-01-27 17:18:07'),
	(5, 'Taller', 5, '2025-01-27 17:18:36', '2025-01-27 17:18:36'),
	(6, 'Plancha', 6, '2025-01-27 17:18:46', '2025-01-27 17:18:46'),
	(7, 'Vinilo y terminado', 7, '2025-01-27 17:18:56', '2025-01-27 17:18:56'),
	(8, 'Bordado', 8, '2025-01-27 17:19:03', '2025-01-27 17:19:03'),
	(9, 'Empaque', 9, '2025-01-27 17:19:10', '2025-01-27 17:19:10');

-- Volcando datos para la tabla abprod.customers: ~0 rows (aproximadamente)
INSERT INTO `customers` (`id`, `nif`, `name`, `address`, `phone`, `user_id`, `created_at`, `updated_at`) VALUES
	(1, '5192306', 'Daril Diaz', 'Horqueta', '0972813605', 1, '2025-01-27 17:19:51', '2025-01-27 17:19:51');

-- Volcando datos para la tabla abprod.failed_jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla abprod.migrations: ~19 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(16, '2025_01_27_133809_create_categories_table', 2),
	(17, '2025_01_27_133809_create_sizes_table', 2),
	(18, '2025_01_27_133810_create_products_table', 2),
	(19, '2025_01_27_133811_create_centers_table', 2),
	(20, '2025_01_27_133811_create_productcenters_table', 2),
	(21, '2025_01_27_133812_create_operators_table', 2),
	(22, '2025_01_27_133813_create_questioncategories_table', 2),
	(23, '2025_01_27_133813_create_questions_table', 2),
	(24, '2025_01_27_133814_create_customers_table', 2),
	(26, '2025_01_27_133814_create_orders_table', 2),
	(28, '2025_01_27_133815_create_productions_table', 2),
	(30, '2025_01_27_133817_create_orderitemproducts_table', 4),
	(31, '2025_01_27_133817_create_orderquestionanswers_table', 4),
	(32, '2025_01_27_133814_create_ordermolds_table', 5),
	(33, '2025_01_27_133816_create_order_references_table', 6),
	(34, '2025_01_27_133815_create_orderitems_table', 7);

-- Volcando datos para la tabla abprod.operators: ~0 rows (aproximadamente)
INSERT INTO `operators` (`id`, `name`, `position`, `user_id`, `center_id`, `created_at`, `updated_at`) VALUES
	(1, 'Tobias', 'Diagramador', 1, 1, '2025-01-27 17:20:24', '2025-01-27 17:20:24');

-- Volcando datos para la tabla abprod.products: ~0 rows (aproximadamente)
INSERT INTO `products` (`id`, `code`, `description`, `category_id`, `price`, `created_at`, `updated_at`) VALUES
	(1, 'CAM-S01', 'CAMISETA ESTANDAR MANGA RECTA CUELLO RENDONDO', 1, 75000, '2025-01-27 17:33:13', '2025-01-27 17:33:13'),
	(2, 'SH-S01', 'DFASDF', 2, 55000, '2025-01-28 14:18:57', '2025-01-28 14:18:57');

-- Volcando datos para la tabla abprod.questions: ~4 rows (aproximadamente)
INSERT INTO `questions` (`id`, `text`, `type`, `options`, `is_required`, `category_id`, `created_at`, `updated_at`) VALUES
	(1, 'Color base', 'string', NULL, 1, 1, '2025-01-27 19:15:08', '2025-01-27 19:15:08'),
	(2, 'Va tener Nombre?', 'list', 'Si\nno', 0, 1, '2025-01-27 19:16:11', '2025-01-29 19:20:30'),
	(3, 'Auspicio delantero', 'integer', NULL, 1, 1, '2025-01-29 16:00:17', '2025-01-29 16:00:17'),
	(4, 'Tipo de logo', 'list', 'Bordado\nuv\nsublimado', 1, 2, '2025-01-29 16:00:48', '2025-01-29 19:20:54');

-- Volcando datos para la tabla abprod.question_categories: ~2 rows (aproximadamente)
INSERT INTO `question_categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
	(1, 'Deportivo', NULL, '2025-01-27 17:25:30', '2025-01-27 17:25:30'),
	(2, 'Escolar', NULL, '2025-01-27 17:57:24', '2025-01-27 17:57:24'),
	(3, 'Empresarial', NULL, '2025-01-27 17:57:40', '2025-01-27 17:57:40');

-- Volcando datos para la tabla abprod.sizes: ~36 rows (aproximadamente)
INSERT INTO `sizes` (`id`, `name`, `created_at`, `updated_at`) VALUES
	(1, '1A-CAB', '2025-01-27 14:30:24', '2025-01-27 14:30:24'),
	(2, '1A-DAM', '2025-01-27 14:30:25', '2025-01-27 14:30:26'),
	(3, '2A-CAB', '2025-01-27 14:30:26', '2025-01-27 14:30:27'),
	(4, '2A-DAM', '2025-01-27 14:30:28', '2025-01-27 14:30:28'),
	(5, '4A-CAB', '2025-01-27 14:30:29', '2025-01-27 14:30:29'),
	(6, '4A-DAM', '2025-01-27 14:30:30', '2025-01-27 14:30:30'),
	(7, '6A-CAB', '2025-01-27 14:30:31', '2025-01-27 14:30:31'),
	(45, '6A-DAM', '2025-01-27 14:30:32', '2025-01-27 14:30:33'),
	(46, '8A-CAB', '2025-01-27 14:30:33', '2025-01-27 14:30:34'),
	(47, '8A-DAM', '2025-01-27 14:30:35', '2025-01-27 14:30:35'),
	(48, '10A-CAB', '2025-01-27 14:30:36', '2025-01-27 14:30:36'),
	(49, '10A-DAM', '2025-01-27 14:30:37', '2025-01-27 14:30:38'),
	(50, '14A-CAB', '2025-01-27 14:30:39', '2025-01-27 14:30:39'),
	(51, '14A-DAM', '2025-01-27 14:30:40', '2025-01-27 14:30:41'),
	(52, '16A-CAB', '2025-01-27 14:30:41', '2025-01-27 14:30:42'),
	(53, '16A-DAM', '2025-01-27 14:30:42', '2025-01-27 14:30:43'),
	(54, 'XP-CAB', '2025-01-27 14:30:44', '2025-01-27 14:30:44'),
	(55, 'XP-DAM', '2025-01-27 14:30:45', '2025-01-27 14:30:45'),
	(56, 'P-CAB', '2025-01-27 14:30:46', '2025-01-27 14:30:47'),
	(57, 'P-DAM', '2025-01-27 14:30:47', '2025-01-27 14:30:48'),
	(58, 'M-CAB', '2025-01-27 14:30:49', '2025-01-27 14:30:49'),
	(59, 'M-DAM', '2025-01-27 14:30:50', '2025-01-27 14:30:51'),
	(60, 'G-CAB', '2025-01-27 14:30:51', '2025-01-27 14:30:52'),
	(61, 'G-DAM', '2025-01-27 14:30:52', '2025-01-27 14:30:53'),
	(62, 'XG-CAB', '2025-01-27 14:30:54', '2025-01-27 14:30:54'),
	(63, 'XG-DAM', '2025-01-27 14:30:55', '2025-01-27 14:30:56'),
	(64, '2XG-CAB', '2025-01-27 14:30:56', '2025-01-27 14:30:57'),
	(65, '2XG-DAM', '2025-01-27 14:30:58', '2025-01-27 14:30:58'),
	(66, '3XG-CAB', '2025-01-27 14:30:59', '2025-01-27 14:31:00'),
	(67, '3XG-DAM', '2025-01-27 14:31:00', '2025-01-27 14:31:01'),
	(68, '4XG-CAB', '2025-01-27 14:31:02', '2025-01-27 14:31:02'),
	(69, '4XG-DAM', '2025-01-27 14:31:03', '2025-01-27 14:31:04'),
	(70, '5XG-CAB', '2025-01-27 14:31:04', '2025-01-27 14:31:05'),
	(71, '5XG-DAM', '2025-01-27 14:31:06', '2025-01-27 14:31:06'),
	(72, '6XG-CAB', '2025-01-27 14:31:07', '2025-01-27 14:31:08'),
	(73, '6XG-DAM', '2025-01-27 14:31:08', '2025-01-27 14:31:09');
