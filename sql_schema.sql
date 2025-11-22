-- Create the database (optional if not already created)
CREATE DATABASE IF NOT EXISTS motiv_motors;
USE motiv_motors;

-- Create the cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    mileage INT NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    status ENUM('Available', 'Pending', 'Sold') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Dummy Data (Classic, Unique, Modern)
INSERT INTO cars (make, model, year, price, mileage, description, image_url, status) VALUES
('Ford', 'Mustang Shelby GT500', 1967, 225000.00, 4500, 'A pristine example of American muscle history. Eleanor grey with black stripes, fully restored engine block, and original leather interior.', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?auto=format&fit=crop&w=800&q=80', 'Available'),

('Porsche', '911 Carrera RS', 1973, 650000.00, 12000, 'The quintessential drivers car. Lightweight construction, ducktail spoiler, and matching numbers engine. A true collectors item.', 'https://images.unsplash.com/photo-1503376763036-066120622c74?auto=format&fit=crop&w=800&q=80', 'Available'),

('Ferrari', '488 Pista', 2021, 430000.00, 1200, 'Track-bred performance for the road. Finished in Rosso Corsa with NART stripes. Carbon fiber package and Alcantara interior.', 'https://images.unsplash.com/photo-1592198084033-aade902d1aae?auto=format&fit=crop&w=800&q=80', 'Available');