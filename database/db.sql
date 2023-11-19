DROP DATABASE lib;

CREATE DATABASE lib;

USE lib;

CREATE TABLE users (
	user_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(255) UNIQUE NOT NULL,
	f_name VARCHAR(255) NOT NULL,
	l_name VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
	birthdate INT(11) NOT NULL
);

CREATE TABLE libraries (
	lib_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	user_id INTEGER,
	FOREIGN KEY(user_id) REFERENCES users(user_id),
	lib_title VARCHAR(255) NOT NULL,
	lib_pic VARCHAR(255) default 'default.png',
	lib_cover VARCHAR(255) default 'default.png',
	about_lib VARCHAR(1024),
	readers_count INTEGER NOT NULL,
	reading_count INTEGER NOT NULL,
	main bit NOT NULL default 0
);

CREATE TABLE readers (
	reader INTEGER NOT NULL,
	reading INTEGER NOT NULL,
	FOREIGN KEY(reader) REFERENCES libraries(lib_id),
	FOREIGN KEY(reading) REFERENCES libraries(lib_id),
	PRIMARY KEY (reader, reading)
);

CREATE TABLE book_lists (
	list_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	lib_id INTEGER NOT NULL,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	title VARCHAR(255)
);

CREATE TABLE books (
	book_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	lib_id INTEGER NOT NULL,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	title VARCHAR(255) NOT NULL,
	author VARCHAR(255),
	pages INTEGER,
	cover VARCHAR(255) NOT NULL,
	_description VARCHAR(255),
	publisher VARCHAR(255),
	_language VARCHAR(255),
	book_location VARCHAR(255)
);

CREATE TABLE stores (
	store_id BIGINT AUTO_INCREMENT PRIMARY KEY,
	book_id INTEGER NOT NULL,
	FOREIGN KEY(book_id) REFERENCES books(book_id),
	store VARCHAR(255) NOT NULL,
	link VARCHAR(1024)
);

CREATE TABLE listed_books (
	list_id INTEGER NOT NULL,
	FOREIGN KEY(list_id) REFERENCES book_lists(list_id),
	book_id INTEGER NOT NULL,
	FOREIGN KEY(book_id) REFERENCES books(book_id)
);

CREATE TABLE posts (
	post_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	book_id INTEGER,
	FOREIGN KEY(book_id) REFERENCES books(book_id),
	content VARCHAR(3072) NOT NULL,
	likes_count INTEGER NOT NULL DEFAULT 0,
	_date INT(11) NOT NULL
);

CREATE TABLE post_likes (
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	post_id INTEGER,
	FOREIGN KEY(post_id) REFERENCES posts(post_id)
);

CREATE TABLE comments (
	comment_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	post_id INTEGER,
	FOREIGN KEY(post_id) REFERENCES posts(post_id),
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	comment VARCHAR(1024) NOT NULL,
	_date INT(11) NOT NULL
);

CREATE TABLE notifications(
	noti_id INTEGER AUTO_INCREMENT PRIMARY KEY,
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	_action VARCHAR(255) NOT NULL,
	to_lib_id INTEGER,
	FOREIGN KEY(to_lib_id) REFERENCES libraries(lib_id),
	comment_id INTEGER,
	FOREIGN KEY(comment_id) REFERENCES comments(comment_id),
	post_id INTEGER,
	FOREIGN KEY(post_id) REFERENCES posts(post_id),
	book_id INTEGER,
	FOREIGN KEY(book_id) REFERENCES books(book_id),
	_date INT(11) NOT NULL
);

CREATE TABLE chat(
	msg_id BIGINT AUTO_INCREMENT PRIMARY KEY,
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id),
	to_lib_id INTEGER,
	FOREIGN KEY(to_lib_id) REFERENCES libraries(lib_id),
	_message VARCHAR(3072) NOT NULL,
	_date INT(11) NOT NULL
);
CREATE TABLE available_categories(
	category VARCHAR(255) PRIMARY KEY
);
CREATE TABLE list_categories(
	category VARCHAR(255),
	FOREIGN KEY(category) REFERENCES available_categories(category),
	list_id INTEGER,
	FOREIGN KEY(list_id) REFERENCES book_lists(list_id)
);
CREATE TABLE unseen_notifications(
	lib_id INTEGER,
	FOREIGN KEY(lib_id) REFERENCES libraries(lib_id)
);