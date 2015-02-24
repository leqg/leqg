SELECT      `building`.`id`,
            `building`.`street`,
            `street`.`street` AS `street_name`
FROM        `building`
LEFT JOIN   `street`
ON          `street`.`id` = `building`.`street`
WHERE       `building`.`building` LIKE :search
ORDER BY    `building`.`building` ASC
