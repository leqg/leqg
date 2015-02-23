SELECT      `poll`.`number`,
            `poll`.`name`,
            `poll`.`id`,
            `city`.`city` AS `city`
FROM        `poll`
LEFT JOIN   `city`
ON          `city`.`id` = `poll`.`city`
WHERE       `poll`.`id` = :poll