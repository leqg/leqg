SELECT      `contact_id` AS `contact`
FROM        `coordonnees`
WHERE       `coordonnee_email` = :email
ORDER BY    `coordonnee_email` ASC