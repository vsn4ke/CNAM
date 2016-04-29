DROP TABLE IF EXISTS tCategory;
DROP TABLE IF EXISTS tPost;
DROP TABLE IF EXISTS tComment;
DROP TABLE IF EXISTS tUser;

CREATE TABLE tCategory(
    Cat_ID INT PRIMARY KEY AUTO_INCREMENT,
    Cat_Slug VARCHAR(255) NOT NULL UNIQUE,
    Cat_Name VARCHAR(100) NOT NULL
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE TABLE tPost(
    Post_ID INT PRIMARY KEY AUTO_INCREMENT,
    Post_Name VARCHAR(100) NOT NULL,
    Post_Content TEXT NOT NULL,
    Post_Date TIMESTAMP NOT NULL,
    Post_Slug VARCHAR(255) NOT NULL UNIQUE,
    User_ID INT NOT NULL,
    User_ID_Edit INT,
    Post_Date_Edit TIMESTAMP
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE TABLE tComment(
    Com_ID INT PRIMARY KEY AUTO_INCREMENT,
    Com_Content TEXT NOT NULL,
    Com_Date TIMESTAMP NOT NULL,
    Post_ID INT NOT NULL,
    User_ID INT NOT NULL,
    Com_Date_Edit TIMESTAMP,
    User_ID_Edit INT
)ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE TABLE tUser(
    User_ID INT PRIMARY KEY AUTO_INCREMENT,
    User_Name VARCHAR(100) NOT NULL UNIQUE,
    User_Hash TEXT NOT NULL,
    User_Right TINYINT NOT NULL
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE linkCatPost(
    Cat_ID INT,
    Post_ID INT
)ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;



INSERT INTO tCategory VALUES('', 'news', 'News');
INSERT INTO tCategory VALUES('', 'reseau', 'Réseau');
INSERT INTO tCategory VALUES('', 'developpement', 'Developpement');
INSERT INTO tUser VALUES('', 'User01', '$2y$10$cHrsMFVP96aViI8Swbi7GuvfVymU0NzzwT8d8d3Je1FHSndP5drvS', 50);
INSERT INTO tUser VALUES('', 'User02', '$2y$10$MjaoUmXGAk1.Sobt1xSj/Ox7hkdtEtGgUOlOyxXCnemzCn1ZwH4uu', 1);
INSERT INTO tPost VALUES('', 'Bienvenue!', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus vitae tincidunt nunc, quis maximus orci. Morbi porta lobortis dolor, at iaculis enim. Mauris ut convallis urna, vel lacinia tellus. Nullam interdum arcu sit amet erat scelerisque, quis lacinia diam commodo. Fusce volutpat cursus augue, vel aliquam ligula elementum ut. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean quis leo ut dolor luctus mollis.\n\nFusce volutpat tellus nec purus vestibulum, at dictum nunc tristique. Duis ac magna efficitur, accumsan nunc sit amet, pharetra nunc. Donec sodales ut sem in sagittis. Nulla rhoncus mauris vitae justo rhoncus, ac maximus nibh molestie. Duis ac egestas ante, id vestibulum magna. Aenean et facilisis sem. Nam blandit tincidunt dapibus.\n\nInteger vel metus sollicitudin urna malesuada aliquam. Quisque fringilla dolor a tortor consectetur, sed aliquet turpis venenatis. Mauris imperdiet nunc at porta eleifend. Etiam sodales viverra diam, vel porta felis venenatis eu. Nam iaculis, tellus non lacinia facilisis, lacus nisl consectetur metus, vel blandit sem nisl nec dui. Aliquam aliquet finibus eleifend. Etiam vulputate tempor fermentum.\n\nSed nec tristique eros, vel pharetra lectus. Aenean tincidunt nec lorem interdum volutpat. Sed non risus eu metus eleifend tempor nec at ex. Mauris ac urna molestie dui pharetra tincidunt. Maecenas ac tempus enim. Nunc lacinia ante at odio suscipit, et viverra leo finibus. Praesent sit amet justo nec orci pellentesque pulvinar. Quisque in leo eu neque scelerisque rutrum. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Maecenas ultrices elit id fermentum scelerisque. Duis non rutrum nisi, id dictum mauris. Morbi consectetur vehicula fermentum.\n\nPraesent in diam lacus. Mauris consequat ipsum non augue ornare porttitor. Ut tempor, augue eget fringilla pharetra, mauris mi viverra velit, ut tempor leo arcu sit amet libero. Aenean dignissim, ligula quis dignissim elementum, odio mi consectetur diam, facilisis mollis ex massa eu quam. Fusce dolor dolor, suscipit non gravida quis, consequat quis augue. Sed vulputate lacus dolor, ut gravida ligula posuere ac. Nulla facilisi. Nam id suscipit enim. Proin luctus elementum urna, tincidunt vulputate nisl sollicitudin non. Cras neque libero, ullamcorper et dolor nec, semper sagittis justo. Fusce et nisi sit amet nulla mattis eleifend eget ut mauris. Morbi sed lacus metus. Cras placerat, lacus ac accumsan volutpat, justo sapien congue neque, eu commodo mauris lacus in nisl. Duis venenatis placerat sodales. Nunc vel pharetra est.', NOW(), 'bienvenue', 1, '', '');
INSERT INTO tComment VALUES('', 'Un bon début. Continue comme cela!', NOW(), 1, 2, '' , '' );
INSERT INTO linkCatPost VALUES(1, 1);
INSERT INTO linkCatPost VALUES(2, 1);


