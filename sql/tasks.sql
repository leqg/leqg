SELECT      `tache_id` AS `id`,
            `createur_id` AS `createur`,
            `compte_id` AS `user`,
            `historique_id` AS `event`,
            `tache_description` AS `task`,
            `tache_deadline` AS `deadline`,
            `tache_creation` AS `time`,
            `tache_terminee` AS `ended`
FROM        `taches`
WHERE       `tache_terminee` = 0 
AND         `compte_id` = :user
ORDER BY    `tache_deadline` DESC 
LIMIT       0, 5