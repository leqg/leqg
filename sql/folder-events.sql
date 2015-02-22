SELECT      `historique_id` AS `id`
FROM        `historique`
WHERE       `dossier_id` = :folder
ORDER BY    `historique_date` DESC
