CREATE TABLE funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    balance DECIMAL(10, 2) DEFAULT '0.00',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    mime_type VARCHAR(50) NOT NULL,
    image_data LONGBLOB NOT NULL
);


CREATE TABLE link_clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    link_url VARCHAR(255),
    click_count INT DEFAULT 0,
    last_click_time DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


To update a user to be an admin, you need to set the is_admin column to TRUE for that specific user. You can do this using an UPDATE SQL statement. Here’s how you can do it:


UPDATE users
SET is_admin = TRUE
WHERE id = ?;
Replace the ? with the id of the user you want to make an admin. For example, if you want to update the user with id 5, the SQL statement would be:



UPDATE users
SET is_admin = TRUE
WHERE id = 5;

Made with ❤ by peter Mutitu
