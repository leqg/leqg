SELECT      `coordonnee_id`,
            `coordonnee_type`,
            `coordonnee_numero`
FROM        `coordonnees`
WHERE       `coordonnee_type` != "email" 
AND         `coordonnee_numero` IS NOT NULL
AND         `contact_id` = :contact
ORDER BY    `coordonnee_numero` ASC
