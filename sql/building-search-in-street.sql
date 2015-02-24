SELECT      `building`.`id`,
            `building`.`street`,
            `street`.`street` AS `street_name`
FROM        `building`
LEFT JOIN   `street`
ON          `street`.`id` = `building`.`street`
WHERE       `building`.`building` LIKE :search
AND         `building`.`street` = :street
ORDER BY    `building`.`building` ASC
