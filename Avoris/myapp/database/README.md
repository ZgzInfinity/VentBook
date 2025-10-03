# 📚 Documentación de la Base de Datos: Sistema de Eventos y Reservas

## 📄 Descripción General

Este sistema de base de datos está diseñado para gestionar **eventos** y sus correspondientes **reservas**. Consta de dos tablas principales:

- `events`: representa los eventos disponibles.
- `bookings`: gestiona las reservas realizadas por los usuarios para cada evento.

Las tablas están relacionadas mediante una clave foránea (`event_id`) que conecta las reservas con sus eventos correspondientes.

---

## 🧱 Esquema de Base de Datos (`schema.sql`)

### 1. Tabla `events`

Almacena información sobre cada evento publicado.

| Campo            | Tipo        | Descripción                                      |
|------------------|-------------|--------------------------------------------------|
| `id`             | INT         | Clave primaria auto-incremental                 |
| `name`           | VARCHAR(255)| Nombre del evento                               |
| `description`    | TEXT        | Descripción del evento                          |
| `from_date`      | DATE        | Fecha de inicio del evento                      |
| `to_date`        | DATE        | Fecha de finalización del evento                |
| `available_seats`| INT         | Número de plazas disponibles (≥ 0)              |

🔒 **Restricción:** `available_seats` no puede ser negativo.

---

### 2. Tabla `bookings`

Registra cada reserva hecha por un usuario para un evento determinado.

| Campo         | Tipo        | Descripción                                          |
|---------------|-------------|------------------------------------------------------|
| `reference`   | VARCHAR(50) | Clave primaria (código único de reserva)            |
| `event_id`    | INT         | ID del evento reservado (clave foránea a `events`)  |
| `event_date`  | DATE        | Fecha específica de asistencia                      |
| `attendees`   | INT         | Número de asistentes (debe ser mayor que 0)         |
| `buyer_id`    | VARCHAR(16) | Identificador del comprador (DNI, pasaporte, etc.)  |

🔒 **Restricciones aplicadas:**

- `attendees > 0`
- `buyer_id` debe tener entre 12 y 16 caracteres.
- `buyer_id` debe contener **solo caracteres alfanuméricos** (`a-zA-Z0-9`).
- `event_id` debe existir previamente en la tabla `events`.

---

## 🌱 Carga de Datos Inicial (`seed.sql`)

Se incluyen varios eventos y reservas predefinidas para propósitos de desarrollo o pruebas.

### Eventos predefinidos:

1. **Concierto de Rock** (100 plazas)
2. **Feria del Libro** (200 plazas)
3. **Taller de Fotografía** (30 plazas)
4. **Seminario de Tecnología** (150 plazas)
5. **Festival de Cine** (80 plazas)

Cada evento cuenta con:
- Título
- Descripción
- Rango de fechas
- Plazas disponibles

### Reservas incluidas:

- 5 reservas con referencias únicas (`BK001`, `BK002`, etc.)
- Cada una está vinculada a un `event_id`.
- Incluye datos como el número de asistentes y el `buyer_id`.

---

## 🔗 Relaciones entre tablas

- Cada registro en `bookings` **debe** estar asociado a un evento existente en `events`.
- La integridad referencial está protegida por la **clave foránea** `fk_event`.

```
events (1) ──────────▶ (∞) bookings
```

---

## 🛡️ Validaciones implementadas

### En `events`:
- Las plazas disponibles (`available_seats`) no pueden ser negativas.

### En `bookings`:
- `attendees` debe ser mayor a cero.
- `buyer_id` debe ser alfanumérico y tener entre 12 y 16 caracteres.

---

## ✅ Recomendaciones de uso

- Cargar primero el archivo `schema.sql` para crear las tablas.
- Ejecutar luego `seed.sql` para poblarlas con los datos de prueba.
- Asegurarse de que el motor de base de datos soporte `CHECK` y claves foráneas (se usa **InnoDB** en MySQL/MariaDB).