SELECT      `street`.`street`,
            `street`.`id`,
            `street`.`city`,
            `city`.`city` AS `city_name`
FROM        `street`
LEFT JOIN   `city`
ON          `city`.`id` = `street`.`city`
WHERE       `street`.`id` = :street
