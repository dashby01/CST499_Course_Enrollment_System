CREATE DATABASE course_enrollmentdb;

USE course_enrollmentdb;

CREATE TABLE student(
    student_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(50) NOT NULL,
    firstName VARCHAR(30) NOT NULL,
    lastName VARCHAR (30) NOT NULL,
    address VARCHAR (1000) NOT NULL,
    address2 VARCHAR (1000),
    city VARCHAR (1000) NOT NULL,
    state VARCHAR (2) NOT NULL,
    zip INT (5) NOT NULL,
    phone VARCHAR (12) NOT NULL
) ;

CREATE TABLE course ( 
    course_id INT(10) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
    courseName VARCHAR(250) NOT NULL, 
    maxStudents INT NOT NULL
);

CREATE TABLE available ( 
    available_id INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    course_id INT(10) NOT NULL, 
    year YEAR NOT NULL, 
    semester VARCHAR(250) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES course (course_id)
) ;

CREATE TABLE enrollment ( 
    enrollment_id INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    student_id INT(10) NOT NULL, 
    available_id INT(10) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student (student_id),
    FOREIGN KEY (available_id) REFERENCES available (available_id)
) ;

CREATE TABLE waitlist ( 
    waitlist_id INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    student_id INT(10) NOT NULL, 
    available_id INT(10) NOT NULL, 
    dateTimeAdded DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (available_id) REFERENCES available (available_id)
) ;

CREATE TABLE notification ( 
    notification_id INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    student_id INT(10) NOT NULL, 
    available_id INT(10) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (available_id) REFERENCES available (available_id)  
) ;