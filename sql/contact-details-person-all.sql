SELECT      `coordonnee_id`,
            `coordonnee_type`,
            `coordonnee_email`,
            `coordonnee_numero`
FROM        `coordonnees`
WHERE       `contact_id` = :contact
ORDER BY    `coordonnee_id` ASC
