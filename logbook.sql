CREATE TABLE `logbook` (
    `Logbook_ID` INT AUTO_INCREMENT PRIMARY KEY,
    `Student_ID` VARCHAR(20) NOT NULL,
    `Logbook_Date` DATE NOT NULL,
    `Supervisor_Viewed` BOOLEAN NOT NULL DEFAULT FALSE,

    CONSTRAINT `fk_logbook_student`
        FOREIGN KEY (`Student_ID`) REFERENCES `student`(`Student_ID`)
        ON DELETE CASCADE ON UPDATE CASCADE
);