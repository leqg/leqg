SELECT      `historique_id` AS `id`
FROM        `historique`
WHERE       `contact_id` = :person
ORDER BY    `historique_date` DESC
