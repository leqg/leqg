INSERT INTO `tracking` (
    `campaign`,
    `id`,
    `email`,
    `status`
)
VALUES (
    :campaign,
    :id,
    :numero,
    "queued"
)
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`),
                        `status` = VALUES(`status`)