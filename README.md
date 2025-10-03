
<p align="center">
  <img src="https://i.ibb.co/5xSGBs2W/Vent-Book-logo.png" alt="Logo" width=100 height=100>

  <h3 align="center">VentBook</h3>

  <p align="center">
    <b>Una API de Gestión de Eventos y Reservas
</b> <br>
  </p>
</p>

[![PHP Version](https://img.shields.io/badge/PHP-8.2-f305b2.svg)](https://www.php.net/releases/8.2/)
[![Symfony Version](https://img.shields.io/badge/Symfony-6.2-600ff9.svg)](https://symfony.com/)
[![MySQL Version](https://img.shields.io/badge/MySQL-8.0.26-orange)](https://www.mysql.com/)
[![Redis Version](https://img.shields.io/badge/Redis-7.0-red?logo=redis&logoColor=white)](https://redis.io/)
[![Docker](https://img.shields.io/badge/Docker-20.10-blue?logo=docker)](https://www.docker.com/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-9.6-%20%2329f305)](https://phpunit.de/)
[![Swagger](https://img.shields.io/badge/Swagger-5.27.1-yellow)](https://swagger.io/)

## Descripción del Proyecto
VentBook es una API diseñada para gestionar eventos y reservas, implementando buenas prácticas de arquitectura y desarrollo profesional. El proyecto está orientado a ofrecer un sistema modular, escalable y mantenible, con capas claramente separadas de arquitectura, infraestructura, persistencia, documentación y testing.

El desarrollo se ha centrado en una estructura hexagonal, con una separación clara entre lógica de negocio y acceso a datos, facilitando la reutilización, escalabilidad y mantenibilidad del código.

---

## Capa de Infraestructura

### Entidades
- **Evento (`Event`)** y **Reserva (`Booking`)** ubicadas en `src/Domain`.  
- Uso de `readonly` para inmutabilidad de atributos en los constructores.  
- Nombres de propiedades ajustados para mayor claridad:

```json
Evento:
{
  "id": "integer",
  "name": "string",
  "description": "string",
  "fromDate": "yyyy-mm-dd",
  "toDate": "yyyy-mm-dd",
  "availableSeats": "integer"
}

Reserva:
{
  "reference": "string",
  "eventId": "integer",
  "eventDate": "yyyy-mm-dd",
  "attendees": "string",
  "buyerId": "string"
}
```

# VentBook - API de Gestión de Eventos y Reservas

## Estructura del Proyecto
VentBook es una API desarrollada para la gestión integral de eventos y reservas, diseñada siguiendo buenas prácticas de arquitectura, desarrollo y testing profesional. La aplicación está basada en Symfony 6.2 y PHP 8.2, con un enfoque en **arquitectura hexagonal**, separación de capas y contenedores Docker para garantizar modularidad, escalabilidad y mantenibilidad.  

El proyecto incluye:  
- Capa de **Arquitectura**: configuración de contenedores y servicios.  
- Capa de **Infraestructura**: entidades, DTOs, ensambladores, servicios y controladores.  
- Capa de **Persistencia**: MySQL y Redis con adaptadores de lectura/escritura.  
- Capa de **Documentación**: Swagger UI y documentación interna de código y SQL.  
- Capa de **Testing**: pruebas unitarias, de integración y de aplicación con PHPUnit.

---

## Tecnologías Utilizadas
- **Backend:** PHP 8.2 + Symfony 6.2  
- **Base de datos relacional:** MySQL 8.0.26  
- **Caché en memoria:** Redis 7.0 (Alpine)  
- **Contenedores y virtualización:** Docker + Docker Compose, Ubuntu 24.04 LTS  
- **Pruebas:** PHPUnit 9.6  
- **Documentación:** Swagger UI 5.27.1  
- **Herramientas adicionales:** DBeaver, Postman, Visual Studio Code  

---

## Arquitectura y Despliegue

### Contenedores Docker
- **Backend Symfony/PHP**
  - `avoris-php84-symfony62`
  - Volumen: sincronización del código (`/appdata/www`)
  - Puertos: 1000 (host) → 8000 (contenedor)
  - Dependencias: arranca después de MySQL y Redis
  - Variables de entorno: configuración Symfony dev, Xdebug y conexión Redis

- **Base de datos MySQL**
  - `avoris-php84-symfony62-mysql`
  - Imagen: `mysql:8.0.26`
  - Volumen persistente: `/var/lib/mysql`
  - Puertos: 3336 (host) → 3306 (contenedor)
  - Charset: `utf8mb4` y colación `utf8mb4_unicode_ci`

- **Cache Redis**
  - `avoris-php84-symfony62-redis`
  - Imagen: `redis:7.0-alpine`
  - Persistencia AOF (`appendonly yes`) con fsync cada segundo
  - Puertos: 6379 (host) → 6379 (contenedor)
  - Volumen persistente: `./redis-data:/data`

- **Red Docker compartida:** `avoris-php84-symfony62-network` para comunicación entre servicios

![Diagrama arquitectural de VentBook](https://i.ibb.co/whq1Hq0d/Diagrama-Event-Book.png)

### Diagrama de Arquitectura (ASCII)

      +---------------------------+
      |     Cliente / Postman     |
      +------------+--------------+
                   |
                   v
       +-----------+------------+
       |      Controladores     | 
       |    /src/Controller     |
       +-----------+------------+
                   |
                   v
       +-----------+------------+
       |        Servicios       |
       |      /src/Service      |
       +-----+-----------+------+
             |           |
     +-------+---+   +---+--------+
     | Repositorios | Adaptadores |
     | /src/Repo    |  Lect/Escr  |
     +-------+---+   +---+--------+
             |           |
    +--------+-----------+--------+
    |        Persistencia         |
    | MySQL  <----->  Redis Cache |
    +-----------------------------+



---

## Capa de Infraestructura

### Entidades
- `Event` y `Booking` en `src/Domain` (no se usa Doctrine)
- Atributos inmutables (`readonly`)  
- Relación: **Evento 1:N Reservas**

### DTOs (Data Transfer Objects)
- **Entrada:** `BookingCreateDTO`, `BookingCancelDTO`, `BookingListingDTO`, `EventCreateDTO`, `EventDisplayDTO`, `EventOptionalFilterDisplayDTO`  
- **Salida:** `BookingInfoDataDTO`, `EventInfoDataDTO`  
- Mutables, con validaciones mediante atributos de PHP 8+

### Ensambladores
- Validan y construyen DTOs
- Clasificación: `Input` / `Output`  
- Convención: `NombreDTOAssembler`

### Servicios
- Implementan la lógica de negocio y coordinan DTOs y repositorios
- Servicios principales:  
  - `BookingCreateService`, `BookingCancelService`, `BookingListingService`  
  - `EventCreateService`, `EventDisplayService`, `EventOptionalFilterDisplayService`

### Controladores
- Definen los endpoints de la API con rutas REST (`GET`, `POST`, `DELETE`)  
- Inyección de dependencias de servicios y ensambladores  
- Documentación y pruebas con Postman y Swagger

---

## Capa de Persistencia

### MySQL
- Contenedor: `avoris-php84-symfony62-mysql`
- Scripts SQL: `schema.sql` y `seed.sql` en `/src/database`
- Adaptadores: lectura (`read`) y escritura (`write`) mediante `ConnectionManager`  
- Repositorios: `EventRepository`, `BookingRepository`  
- Operaciones atómicas para mantener integridad (transacciones en servicios)

### Redis
- Contenedor: `avoris-php84-symfony62-redis`
- Persistencia AOF, cache para resultados de consultas frecuentes  
- Integración mediante inyección de dependencias en repositorios  
- Expiración de cache: 300 segundos

---

## Capa de Documentación

- **Código PHP:** PHPDoc + PSR-12  
- **SQL:** README.md con estructura, tipos de datos y relaciones  
- **Servicios:** Archivos Markdown por servicio en `/docs/resources`  
- **API Interactiva:** Swagger UI en `http://localhost:1000/swagger/`

---

## Capa de Testing

- **Ubicación:** `/tests`  
- **Tipos de tests:** unitarios, integración, aplicación  
- Uso de **mockups** para separación de entornos  
- Entorno de pruebas: `APP_ENV=test`  
- Comandos:
```bash
composer require --dev phpunit/phpunit:^9.5
ln -s "$(pwd)/vendor/bin/phpunit" bin/phpunit
chmod +x vendor/bin/phpunit
APP_ENV=test bin/phpunit

