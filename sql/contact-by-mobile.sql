SELECT      `contact_id` AS `contact`
FROM        `coordonnees`
WHERE       `coordonnee_numero` = :coord
ORDER BY    `coordonnee_numero` ASC
