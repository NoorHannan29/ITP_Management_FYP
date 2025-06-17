CREATE TABLE evaluations (
    Evaluation_ID INT AUTO_INCREMENT PRIMARY KEY,
    Student_ID VARCHAR(20) NOT NULL,
    Supervisor_ID INT(11) NOT NULL,
    Company_Name VARCHAR(255),
    Report_Submission_Date DATE,

    -- Section A: Report and Professionalism (15 marks total)
    Report_Quality INT CHECK (Report_Quality BETWEEN 1 AND 5),
    Skill_Application INT CHECK (Skill_Application BETWEEN 1 AND 5),
    Timely_Reporting INT CHECK (Timely_Reporting BETWEEN 1 AND 5),

    -- Section B: Company Supervisor Assessment (20 marks)
    Company_Supervisor_Score INT CHECK (Company_Supervisor_Score BETWEEN 0 AND 20),

    -- Section C: Presentation Assessment (10 marks)
    Company_Presentation_Score INT CHECK (Company_Presentation_Score BETWEEN 0 AND 5),
    Faculty_Presentation_Score INT CHECK (Faculty_Presentation_Score BETWEEN 0 AND 5),

    -- Comments and Final Status
    Comments TEXT,
    Status ENUM('PASS', 'FAIL') DEFAULT 'PASS',

    -- Timestamp
    Created_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (Student_ID) REFERENCES student(Student_ID),
    FOREIGN KEY (Supervisor_ID) REFERENCES supervisor(Supervisor_ID)
);
