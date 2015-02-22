INSERT INTO `fichiers` (
    `contact_id`,
    `compte_id`,
    `interaction_id`,
    `fichier_nom`,
    `fichier_description`,
    `fichier_url`
) 
VALUES (
    :people,
    :user,
    :event,
    :name,
    :desc,
    :url
)