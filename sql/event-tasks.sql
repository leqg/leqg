SELECT      `tache_id` AS `id`,
            `createur_id` AS `createur`,
            `compte_id` AS `user`,
            `historique_id` AS `event`,
            `dossier_id` AS `folder`,
            `tache_description` AS `description`,
            `tache_deadline` AS `deadline`,
            `tache_creation` AS `time`,
            `tache_terminee` AS `ended`
FROM        `taches`
WHERE       `historique_id` = :event 
ORDER BY    `tache_creation` DESC
