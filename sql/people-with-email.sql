SELECT      `contact_id` AS `id`
FROM        `coordonnees`
WHERE       `coordonnee_type` = "email"
AND         `coordonnee_email` IS NOT NULL
GROUP BY    `contact_id`
