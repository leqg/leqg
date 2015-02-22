SELECT      `tache_id` AS `id`,
            `createur_id` AS `createur`,
            `compte_id` AS `user`,
            `historique_id` AS `event`,
            `dossier_id` AS `folder`,
            `tache_description` AS `task`,
            `tache_deadline` AS `deadline`,
            `tache_creation` AS `begin`,
            `tache_terminee` AS `end`
FROM        `taches`
WHERE       `tache_id` = :task
