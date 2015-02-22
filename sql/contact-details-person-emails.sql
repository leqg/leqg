SELECT      `coordonnee_id`,
            `coordonnee_type`,
            `coordonnee_email`
FROM        `coordonnees`
WHERE       `coordonnee_type` = "email" 
AND         `coordonnee_email` IS NOT NULL
AND         `contact_id` = :contact
ORDER BY    `coordonnee_email` ASC
