CREATE DATABASE IF NOT EXISTS itms;

USE itms;

CREATE TABLE User (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('taxpayer', 'taxprofessional', 'taxauthority') NOT NULL
);


CREATE TABLE Taxpayer (
    TaxpayerID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    TIN VARCHAR(15) UNIQUE,
    Address TEXT,
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    RegistrationDate DATE,
    Password VARCHAR(255)
);

CREATE TABLE TaxProfessional (
    ProfessionalID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    TIN VARCHAR(15),
    Certification_ID VARCHAR(20),
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    RegistrationDate DATE
    Password VARCHAR(255)
);

CREATE TABLE TaxAuthority (
    AuthorityID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100),
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(15),
    Designation VARCHAR(50),
    Department VARCHAR(100)
);

CREATE TABLE IncomeTaxReturn (
    ReturnID INT PRIMARY KEY AUTO_INCREMENT,
    TaxpayerID INT,
    FilingYear YEAR,
    IncomeDetails TEXT,
    Documents TEXT,
    FilingDate DATE,
    TaxLiability DECIMAL(10, 2),
    Status ENUM('Pending', 'Completed', 'Rejected'),
    FOREIGN KEY (TaxpayerID) REFERENCES Taxpayer(TaxpayerID)
);

CREATE TABLE Payment (
    PaymentID INT PRIMARY KEY AUTO_INCREMENT,
    ReturnID INT,
    PaymentAmount DECIMAL(10, 2),
    PaymentDate DATE,
    PaymentStatus ENUM('Pending', 'Completed'),
    ReceiptNumber VARCHAR(50),
    PaymentMethod VARCHAR(20),
    FOREIGN KEY (ReturnID) REFERENCES IncomeTaxReturn(ReturnID)
);

CREATE TABLE Refund (
    RefundID INT PRIMARY KEY AUTO_INCREMENT,
    ReturnID INT,
    RefundAmount DECIMAL(10, 2),
    RefundStatus ENUM('Pending', 'Completed'),
    RefundIssuedDate DATE,
    FOREIGN KEY (ReturnID) REFERENCES IncomeTaxReturn(ReturnID)
);

CREATE TABLE TaxReport (
    ReportID INT PRIMARY KEY AUTO_INCREMENT,
    TaxpayerID INT,
    ReturnID INT,
    ReportType VARCHAR(50),
    GeneratedDate DATE,
    Format ENUM('PDF', 'HTML', 'CSV'),
    FOREIGN KEY (TaxpayerID) REFERENCES Taxpayer(TaxpayerID),
    FOREIGN KEY (ReturnID) REFERENCES IncomeTaxReturn(ReturnID)
);

CREATE TABLE ErrorLog (
    ErrorID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ErrorMessage TEXT,
    Module VARCHAR(50),
    ResolvedStatus BOOLEAN,
    FOREIGN KEY (UserID) REFERENCES Taxpayer(TaxpayerID)
);

CREATE TABLE Notification (
    NotificationID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    Message TEXT,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Type ENUM('Info', 'Warning', 'Alert'),
    Status ENUM('Unread', 'Read'),
    FOREIGN KEY (UserID) REFERENCES Taxpayer(TaxpayerID)
);