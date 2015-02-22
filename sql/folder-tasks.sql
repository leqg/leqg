SELECT      `tache_id`
FROM        `taches`
LEFT JOIN   `historique`
ON          `taches`.`historique_id` = `historique`.`historique_id`
WHERE       `historique`.`dossier_id` = :folder
ORDER BY    `tache_deadline` DESC
