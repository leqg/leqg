SELECT      `contact_id` AS `id`
FROM        `coordonnees`
WHERE       `coordonnee_type` = "mobile"
AND         `coordonnee_numero` IS NOT NULL
GROUP BY    `contact_id`
