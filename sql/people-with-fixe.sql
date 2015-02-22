SELECT      `contact_id` AS `id`
FROM        `coordonnees`
WHERE       `coordonnee_type` = "fixe"
AND         `coordonnee_numero` IS NOT NULL
GROUP BY    `contact_id`
