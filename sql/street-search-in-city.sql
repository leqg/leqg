SELECT      `street`.`id`,
            `street`.`street`,
            `city`.`city` AS `city_name`
FROM        `street`
LEFT JOIN   `city`
ON          `city`.`id` = `street`.`city`
WHERE       `street` LIKE :search
AND         `street`.`city` = :city
ORDER BY    `street` ASC
