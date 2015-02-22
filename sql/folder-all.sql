SELECT      `dossier_id` AS `id` 
FROM        `dossiers` 
WHERE       `dossier_statut` = :statut 
ORDER BY    `dossier_nom` ASC
