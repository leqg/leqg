SELECT      `email`,
            `firstname`, 
            `lastname`,
            `auth_level`,
            `client`,
            `last_reinit`,
            `id`,
            `phone`,
            `last_login`
FROM        `user` 
WHERE       `client` = :client
AND         `id` NOT IN (:exclude)
