SELECT      `city`.`city`,
            `city`.`id`,
            `city`.`country`,
            `country`.`country` AS `country_name`
FROM        `city`
LEFT JOIN   `country`
ON          `country`.`id` = `city`.`country`
WHERE       `city`.`id` = :city