SELECT      `poll`.`id`,
            `poll`.`number`,
            `poll`.`name`,
            `city`.`city` AS `city_name`
FROM        `poll`
LEFT JOIN   `city`
ON          `city`.`id` = `poll`.`city`
WHERE       `poll`.`name` LIKE :search
OR          `poll`.`number` LIKE :search
ORDER BY    `poll`.`number`, `poll`.`name`, `poll`.`id` ASC
