SELECT      COUNT(*) AS `nb`
FROM        `coordonnees`
WHERE       `contact_id` = :contact
AND         `coordonnee_type` = "email"