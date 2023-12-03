CREATE DATABASE IF NOT EXISTS ACADSystem;
USE ACADSystem;

CREATE TABLE IF NOT EXISTS Airport (
    AirportID INT NOT NULL AUTO_INCREMENT,
    Address VARCHAR(255) NOT NULL,
    PRIMARY KEY (AirportID)
);

CREATE TABLE IF NOT EXISTS PlaneType (
    PlaneTypeID INT NOT NULL AUTO_INCREMENT,
    Category VARCHAR(255) NOT NULL,
    Purpose VARCHAR(50) NOT NULL,
    PRIMARY KEY (PlaneTypeID)
);

CREATE TABLE IF NOT EXISTS Plane (
    PlaneID INT NOT NULL AUTO_INCREMENT,
    PlaneTypeID INT NOT NULL,
    Model VARCHAR(255) NOT NULL,
    SeatCapacity INT,
    CargoCapacity DECIMAL(10, 2),
    SourceAirportID INT,
    DestinationAirportID INT,
    PRIMARY KEY (PlaneID),
    FOREIGN KEY (PlaneTypeID) REFERENCES PlaneType (PlaneTypeID),
    FOREIGN KEY (SourceAirportID) REFERENCES Airport (AirportID),
    FOREIGN KEY (DestinationAirportID) REFERENCES Airport (AirportID)
);

INSERT INTO PlaneType (Category, Purpose) VALUES
    ('Boeing', 'Comercial'),
    ('Airbus', 'Transporte'),
    ('Bombardier', 'Militar'),
    ('Embraer', 'Jato Regional'),
    ('Cessna', 'Privado'),
    ('Lockheed Martin', 'Militar'),
    ('Gulfstream', 'Jato Executivo');
