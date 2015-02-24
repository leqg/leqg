SELECT      `street`.`id`,
            `street`.`street`,
            `city`.`city` AS `city_name`
FROM        `street`
LEFT JOIN   `city`
ON          `city`.`id` = `street`.`city`
WHERE       `street` LIKE :search
ORDER BY    `street` ASC
