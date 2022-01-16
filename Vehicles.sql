
CREATE TABLE `Vehicles` (
`id` int(6) NOT NULL AUTO_INCREMENT,
`fullName` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`make` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
`color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
`licensePlate` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
`state` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
`year` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
`posted` timestamp NOT NULL DEFAULT current_timestamp(),
PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;