-- Insertar eventos
INSERT INTO events (name, description, from_date, to_date, available_seats) VALUES
('Concierto de Rock', 'Concierto en vivo con bandas locales.', '2030-01-01', '2030-01-05', 100),
('Feria del Libro', 'Feria anual con editoriales independientes.', '2030-02-10', '2030-02-12', 200),
('Taller de Fotografía', 'Aprende los fundamentos de la fotografía digital.', '2030-03-15', '2030-03-15', 30),
('Seminario de Tecnología', 'Charlas sobre innovación tecnológica.', '2030-04-01', '2030-04-03', 150),
('Festival de Cine', 'Proyección de películas independientes.', '2030-05-20', '2030-05-25', 80);

-- Insertar reservas
INSERT INTO bookings (reference, event_id, event_date, attendees, buyer_id) VALUES
('BK001', 1, '2030-01-02', 2, '123456789012'),
('BK002', 1, '2030-01-03', 1, 'ABC123456789'),
('BK003', 2, '2030-02-10', 3, 'ZXCVBNMASD12'),
('BK004', 4, '2030-04-01', 1, 'QWERTY098765'),
('BK005', 5, '2030-05-25', 2, 'MNBVCXZLKJ12');