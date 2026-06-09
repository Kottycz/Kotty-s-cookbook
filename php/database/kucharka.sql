-- ============================================================
-- Kottyho kuchařka – MySQL databáze
-- Import: přes phpMyAdmin nebo příkazem:
--   mysql -u UZIVATEL -p JMENO_DATABAZE < kucharka.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `submission_steps`;
DROP TABLE IF EXISTS `submission_ingredients`;
DROP TABLE IF EXISTS `submissions`;
DROP TABLE IF EXISTS `authors`;
DROP TABLE IF EXISTS `recipe_steps`;
DROP TABLE IF EXISTS `recipe_ingredients`;
DROP TABLE IF EXISTS `recipe_images`;
DROP TABLE IF EXISTS `recipes`;
DROP TABLE IF EXISTS `units`;
DROP TABLE IF EXISTS `difficulties`;
DROP TABLE IF EXISTS `categories`;

-- ============================================================
-- Tabulky
-- ============================================================

CREATE TABLE `categories` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(200) NOT NULL,
  `slug`        VARCHAR(200) NOT NULL UNIQUE,
  `image`       VARCHAR(500) NOT NULL DEFAULT '',
  `description` VARCHAR(1000) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `difficulties` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL UNIQUE,
  `sort_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `units` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(100) NOT NULL UNIQUE,
  `abbreviation` VARCHAR(50)  NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipes` (
  `id`                INT AUTO_INCREMENT PRIMARY KEY,
  `category_id`       INT NOT NULL,
  `difficulty_id`     INT NOT NULL,
  `name`              VARCHAR(200) NOT NULL,
  `slug`              VARCHAR(200) NOT NULL UNIQUE,
  `description`       VARCHAR(2000) NOT NULL DEFAULT '',
  `image`             VARCHAR(500) NOT NULL DEFAULT '',
  `prep_time_minutes` INT NOT NULL DEFAULT 0,
  `cook_time_minutes` INT NOT NULL DEFAULT 0,
  `servings`          INT NOT NULL DEFAULT 1,
  `featured`          TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`)   REFERENCES `categories`(`id`),
  FOREIGN KEY (`difficulty_id`) REFERENCES `difficulties`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipe_images` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `recipe_id`  INT NOT NULL,
  `image`      VARCHAR(500) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipe_ingredients` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `recipe_id`  INT NOT NULL,
  `name`       VARCHAR(200) NOT NULL,
  `amount`     DECIMAL(10,2),
  `unit_id`    INT,
  `note`       VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order` INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unit_id`)   REFERENCES `units`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `recipe_steps` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `recipe_id`   INT NOT NULL,
  `step_number` INT NOT NULL,
  `description` TEXT NOT NULL,
  FOREIGN KEY (`recipe_id`) REFERENCES `recipes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `authors` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(200) NOT NULL,
  `email`      VARCHAR(200) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `submissions` (
  `id`                INT AUTO_INCREMENT PRIMARY KEY,
  `author_id`         INT NOT NULL,
  `category_id`       INT NOT NULL,
  `difficulty_id`     INT NOT NULL,
  `name`              VARCHAR(200) NOT NULL,
  `description`       VARCHAR(2000) NOT NULL DEFAULT '',
  `prep_time_minutes` INT NOT NULL DEFAULT 0,
  `cook_time_minutes` INT NOT NULL DEFAULT 0,
  `servings`          INT NOT NULL DEFAULT 1,
  `status`            VARCHAR(20) NOT NULL DEFAULT 'pending',
  `created_at`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`)     REFERENCES `authors`(`id`),
  FOREIGN KEY (`category_id`)   REFERENCES `categories`(`id`),
  FOREIGN KEY (`difficulty_id`) REFERENCES `difficulties`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `submission_ingredients` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `submission_id` INT NOT NULL,
  `name`          VARCHAR(200) NOT NULL,
  `amount`        DECIMAL(10,2),
  `unit_id`       INT,
  `note`          VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order`    INT NOT NULL DEFAULT 0,
  FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unit_id`)       REFERENCES `units`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `submission_steps` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `submission_id` INT NOT NULL,
  `step_number`   INT NOT NULL,
  `description`   TEXT NOT NULL,
  FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Indexy
-- ============================================================

CREATE INDEX `idx_recipes_category`          ON `recipes`(`category_id`);
CREATE INDEX `idx_recipes_slug`              ON `recipes`(`slug`);
CREATE INDEX `idx_recipes_featured`          ON `recipes`(`featured`);
CREATE INDEX `idx_categories_slug`           ON `categories`(`slug`);
CREATE INDEX `idx_recipe_ingredients_recipe` ON `recipe_ingredients`(`recipe_id`);
CREATE INDEX `idx_recipe_steps_recipe`       ON `recipe_steps`(`recipe_id`);
CREATE INDEX `idx_recipe_images_recipe`      ON `recipe_images`(`recipe_id`);
CREATE INDEX `idx_submissions_author`        ON `submissions`(`author_id`);

-- ============================================================
-- Data
-- ============================================================

INSERT INTO `difficulties` (`name`, `sort_order`) VALUES
('Snadné',  1),
('Střední', 2),
('Náročné', 3);

-- unit_id: 1=gram 2=kilogram 3=mililitr 4=litr 5=kus 6=lžíce 7=lžička 8=špetka 9=hrnek 10=plátek 11=stroužek 12=svazek
INSERT INTO `units` (`name`, `abbreviation`) VALUES
('gram',     'g'),
('kilogram', 'kg'),
('mililitr', 'ml'),
('litr',     'l'),
('kus',      'ks'),
('lžíce',   'lž.'),
('lžička',  'lžič.'),
('špetka',  'špet.'),
('hrnek',    'hrn.'),
('plátek',  'pl.'),
('stroužek','str.'),
('svazek',   'sv.');

INSERT INTO `categories` (`name`, `slug`, `image`, `description`) VALUES
('Polévky',     'polevky',     'assets/images/polevky.jpeg',     'Klasické české i mezinárodní polévky pro každou příležitost.'),
('Hlavní jídla','hlavni-jidla','assets/images/hlavni-jidla.jpeg','Vydatná hlavní jídla z masa, ryb i zeleniny.'),
('Přílohy',        'prilohy',        'assets/images/prilohy.jpg',       'Přílohy k hlavním jídlům – rýže, brambory, knedlíky a další.'),
('Vegetariánské', 'vegetarianske', 'assets/images/vegetarianske.jpeg','Chutné recepty bez masa pro každý den.'),
('Dezerty',     'dezerty',     'assets/images/dezerty.jpg',      'Dorty, koláče, buchty a další sladké pokušení.'),
('Nápoje',      'napoje',      'assets/images/snidane.jpg',      'Domácí limonády, smoothie a teplé nápoje.');

INSERT INTO `recipes` (`category_id`, `difficulty_id`, `name`, `slug`, `description`, `image`, `prep_time_minutes`, `cook_time_minutes`, `servings`, `featured`) VALUES
(1, 1, 'Česnečka',         'cesnecka',         'Tradiční česká česneková polévka s krutony a sýrem. Rychlá, voňavá a zahřeje vás v každém počasí.',  'assets/images/polevky.jpeg',    10, 20, 4, 1),
(2, 1, 'Kuřecí řízek',    'kureci-rizek',     'Křupavý smažený kuřecí řízek v trojobalu. Klasika, kterou má rád každý.',                              'assets/images/hlavni-jidla.jpeg',15, 20, 4, 1),
(3, 2, 'Špenátové gnocchi','spenatove-gnocchi','Domácí bramborové gnocchi se špenátovým pestem a smetanou.',                                           'assets/images/testoviny.jpg',   30, 20, 4, 1),
(4, 1, 'Řecký salát',     'recky-salat',      'Svěží salát s rajčaty, okurkou, olivami, červenou cibulí a sýrem feta.',                               'assets/images/recky-salat.jpg', 15,  0, 4, 1),
(5, 2, 'Jablečný štrúdl', 'jablecny-strudl',  'Křehké tažené těsto plněné jablky, rozinkami a skořicí. Voňavý jako u babičky.',                       'assets/images/dezerty.jpg',     30, 40, 8, 1),
(6, 1, 'Domácí limonáda', 'domaci-limonada',  'Osvěžující limonáda z citronu, máty a medu. Hotová za pět minut.',                                     'assets/images/snidane.jpg',      5,  0, 4, 1);

INSERT INTO `recipe_ingredients` (`recipe_id`, `name`, `amount`, `unit_id`, `note`, `sort_order`) VALUES
-- Česnečka (1)
(1, 'česnek',    4,    11,   '',              1),
(1, 'brambory',  300,   1,   'na kostky',     2),
(1, 'cibule',    1,     5,   'najemno',       3),
(1, 'majoránka', 1,     7,   'sušená',        4),
(1, 'voda',      1.5,   4,   '',              5),
(1, 'olej',      2,     6,   '',              6),
(1, 'sůl',       NULL, NULL, 'podle chuti',   7),
(1, 'tvrdý sýr', 50,    1,   'nastrouhaný',   8),
-- Kuřecí řízek (2)
(2, 'kuřecí prsa',     4,   5, '',          1),
(2, 'mouka hladká',    100, 1, '',          2),
(2, 'vejce',           2,   5, '',          3),
(2, 'strouhanka',      150, 1, '',          4),
(2, 'olej na smažení', 500, 3, '',          5),
(2, 'citron',          1,   5, 'na ozdobu', 6),
-- Špenátové gnocchi (3)
(3, 'brambory',        800, 1, 'varné typu B', 1),
(3, 'mouka hladká',    200, 1, '',             2),
(3, 'vejce',           1,   5, '',             3),
(3, 'čerstvý špenát', 200, 1, '',             4),
(3, 'piniové oříšky', 30,  1, '',             5),
(3, 'parmazán',        50,  1, '',             6),
(3, 'smetana',         200, 3, '',             7),
-- Řecký salát (4)
(4, 'rajčata',        4,   5, '',                 1),
(4, 'okurka salátová',1,   5, '',                 2),
(4, 'cibule červená', 1,   5, '',                 3),
(4, 'olivy',          100, 1, 'kalamata',         4),
(4, 'sýr feta',       200, 1, '',                 5),
(4, 'olivový olej',   4,   6, 'extra panenský',   6),
(4, 'oregano',        1,   7, 'sušené',            7),
-- Jablečný štrúdl (5)
(5, 'listové těsto',  500, 1, '',                  1),
(5, 'jablka',         1,   2, 'kyselejší odrůdy',  2),
(5, 'rozinky',        100, 1, '',                  3),
(5, 'vlašské ořechy', 80,  1, 'sekané',            4),
(5, 'cukr',           100, 1, '',                  5),
(5, 'skořice',        2,   7, 'mletá',             6),
(5, 'máslo',          80,  1, 'rozpuštěné',        7),
(5, 'strouhanka',     4,   6, '',                  8),
-- Domácí limonáda (6)
(6, 'citron', 3,    5,   '',              1),
(6, 'voda',   1,    4,   'studená',       2),
(6, 'med',    4,    6,   '',              3),
(6, 'máta',   1,    12,  'čerstvá',       4),
(6, 'led',    NULL, NULL,'na podávání',   5);

INSERT INTO `recipe_steps` (`recipe_id`, `step_number`, `description`) VALUES
-- Česnečka (1)
(1, 1, 'Brambory oloupejte, nakrájejte na malé kostky a uvařte ve vodě doměkka (asi 15 minut).'),
(1, 2, 'Cibuli najemno nakrájejte a osmahněte na oleji do zlatova.'),
(1, 3, 'Přidejte cibuli do polévky, vmačkejte česnek, ochuťte solí a majoránkou.'),
(1, 4, 'Servírujte s opečenými krutony a posypte strouhaným sýrem.'),
-- Kuřecí řízek (2)
(2, 1, 'Kuřecí prsa naklepejte na tloušťku 1 cm.'),
(2, 2, 'Osolte a opepřete, obalte v mouce, rozšlehaných vejcích a strouhance.'),
(2, 3, 'Smažte na rozpáleném oleji asi 4 minuty z každé strany do zlatova.'),
(2, 4, 'Servírujte s plátkem citronu, vařenými bramborami nebo bramborovým salátem.'),
-- Špenátové gnocchi (3)
(3, 1, 'Brambory uvařte ve slupce, oloupejte a prolisujte.'),
(3, 2, 'Smíchejte s moukou a vejcem na hladké těsto. Vyválejte válečky a krájejte na noky.'),
(3, 3, 'Špenát blanšírujte, rozmixujte s piniovými oříšky a parmazánem na pesto.'),
(3, 4, 'Gnocchi uvařte v osolené vodě – jsou hotové, když vyplavou na hladinu.'),
(3, 5, 'Smíchejte pesto se smetanou, přidejte gnocchi a krátce prohřejte.'),
-- Řecký salát (4)
(4, 1, 'Rajčata a okurku nakrájejte na velké kostky.'),
(4, 2, 'Cibuli nakrájejte na tenká kolečka.'),
(4, 3, 'Vše smíchejte v míse, přidejte olivy a kostku fety.'),
(4, 4, 'Pokapejte olivovým olejem, posypte oreganem a osolte.'),
-- Jablečný štrúdl (5)
(5, 1, 'Jablka oloupejte, zbavte jádřinců a nakrájejte na drobné plátky.'),
(5, 2, 'Smíchejte je s rozinkami, ořechy, cukrem a skořicí.'),
(5, 3, 'Listové těsto rozválejte, potřete máslem a posypte strouhankou.'),
(5, 4, 'Doprostřed vyskládejte jablkovou náplň a stočte do rolády.'),
(5, 5, 'Potřete máslem a pečte v troubě při 180 °C asi 40 minut.'),
(5, 6, 'Posypte moučkovým cukrem a podávejte mírně teplé.'),
-- Domácí limonáda (6)
(6, 1, 'Z citronů vymačkejte šťávu.'),
(6, 2, 'Smíchejte s vodou a medem, dobře rozmíchejte.'),
(6, 3, 'Přidejte čerstvé lístky máty a kostky ledu. Podávejte ihned.');

SET FOREIGN_KEY_CHECKS = 1;
