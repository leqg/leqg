INSERT INTO `tracking` (
    `campaign`,
    `id`,
    `email`,
    `status`
)
VALUES (
    :campaign,
    :id,
    :email,
    :status
)
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`),
                        `status` = VALUES(`status`)
