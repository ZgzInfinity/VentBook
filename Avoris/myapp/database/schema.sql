-- Eliminar tablas si existen para evitar errores al crear nuevas tablas
-- Primero eliminamos bookings porque tiene clave foránea a events
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS events;

-- Crear tabla events (tabla padre)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    available_seats INT NOT NULL CHECK (available_seats >= 0)
) ENGINE=InnoDB;

-- Crear tabla bookings (tabla hija, depende de events)
CREATE TABLE IF NOT EXISTS bookings (
    reference VARCHAR(50) PRIMARY KEY,
    event_id INT NOT NULL,
    event_date DATE NOT NULL,
    attendees INT NOT NULL CHECK (attendees > 0),
    buyer_id VARCHAR(16) NOT NULL,
    -- Restricción longitud DNI (entre 12 y 16 caracteres)
    CONSTRAINT chk_buyer_id_length CHECK (CHAR_LENGTH(buyer_id) >= 12 AND CHAR_LENGTH(buyer_id) <= 16),
    -- Restricción caracteres alfanuméricos para buyer_id
    CONSTRAINT chk_buyer_id_alnum CHECK (buyer_id REGEXP '^[a-zA-Z0-9]+$'),
    -- Clave foránea que referencia la tabla events
    CONSTRAINT fk_event FOREIGN KEY (event_id) REFERENCES events(id)
) ENGINE=InnoDB;