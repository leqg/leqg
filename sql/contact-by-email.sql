SELECT      `contact_id` AS `contact`
FROM        `coordonnees`
WHERE       `coordonnee_email` = :coord
ORDER BY    `coordonnee_email` ASC