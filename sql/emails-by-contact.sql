SELECT      `coordonnee_email` AS `email`
FROM        `coordonnees`
WHERE       `contact_id` = :contact
AND         `coordonnee_type` = "email"