INSERT INTO `historique` (
    `contact_id`,
    `compte_id`,
    `historique_date`
)
VALUES (
    :person,
    :user,
    NOW()
);