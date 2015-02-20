INSERT INTO `tracking` (
    `campaign`,
    `id`,
    `email`,
    `status`,
    `reject_reason`
)
VALUES (
    :campaign,
    :id,
    :email,
    :status,
    :reject
)