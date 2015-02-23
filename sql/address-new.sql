INSERT INTO `address` (
    `people`,
    `type`,
    `building`,
    `street`,
    `zipcode`,
    `city`,
    `country`
)
VALUES (
    :people,
    :type,
    :building,
    :street,
    :zipcode,
    :city,
    ( SELECT      `country`
    FROM        `city`
    WHERE       `id` = :city )
)
ON DUPLICATE KEY UPDATE `building` = VALUES(`building`),
                        `street` = VALUES(`street`),
                        `zipcode` = VALUES(`zipcode`),
                        `city` = VALUES(`city`),
                        `country` = VALUES(`country`)