SELECT      `coordonnee_numero` AS `numero`
FROM        `coordonnees`
WHERE       `contact_id` = :contact
AND         `coordonnee_type` = "mobile"
