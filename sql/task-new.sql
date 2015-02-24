INSERT INTO `taches` (
    `createur_id`,
    `compte_id`,
    `historique_id`,
    `tache_description`,
    `tache_deadline`
)
VALUES (
    :createur,
    :user,
    :event,
    :task,
    :deadline
)