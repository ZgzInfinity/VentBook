<?php

namespace App\Repository;

use PDO;
use App\DTO\Input\EventCreateDTO;
use App\Manager\ConnectionManager;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Repository used to handle reads/writes for the "events" table.
 */
class EventRepository
{
    /**
     * @var PDO
     */
    private PDO $readConnection;

    /**
     * @var PDO
     */
    private PDO $writeConnection;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @param CacheInterface
     * @param ConnectionManager
     */
    public function __construct(
        CacheInterface $cache,
        ConnectionManager $connectionManager
    ) {
        $this->cache      = $cache;
        $this->readConnection  = $connectionManager->getReadConnection();
        $this->writeConnection = $connectionManager->getWriteConnection();
    }

    /**
     * Starts SQL transaction on the write connection.
     */
    public function beginTransaction(): void
    {
        $this->writeConnection->beginTransaction();
    }

    /**
     * Commits transaction on the write connection.
     */
    public function commit(): void
    {
        $this->writeConnection->commit();
    }

    /**
     * Rollbacks transaction on the write connection.
     */
    public function rollBack(): void
    {
        $this->writeConnection->rollBack();
    }

    /**
     * Updates available seats for an event.
     * @param int   $eventId
     * @param int   $seats
     * @param bool  $add
     * @return bool
     */
    public function updateAvailableSeats(int $eventId, int $seats, bool $add = false): bool
    {
        $operator = $add ? '+' : '-';

        $sql  = "UPDATE events SET available_seats = available_seats {$operator} :seats WHERE id = :event_id";
        $stmt = $this->writeConnection->prepare($sql);

        return $stmt->execute([
            'seats'    => $seats,
            'event_id' => $eventId,
        ]);
    }

    /**
     * Inserts a new event.
     * @param EventCreateDTO $dto
     * @return int
     */
    public function insert(EventCreateDTO $dto): int
    {
        $sql  = 'INSERT INTO events (name, description, from_date, to_date, available_seats)
                 VALUES (:name, :description, :from_date, :to_date, :available_seats)';
        $stmt = $this->writeConnection->prepare($sql);
        $stmt->execute([
            'name'            => $dto->getName(),
            'description'     => $dto->getDescription(),
            'from_date'       => $dto->getFromDate()->format('Y-m-d'),
            'to_date'         => $dto->getToDate()->format('Y-m-d'),
            'available_seats' => $dto->getAvailableSeats(),
        ]);

        return (int) $this->writeConnection->lastInsertId();
    }

    /**
     * Returns events filtered with WHERE conditions + Redis cache (5min).
     * @param array $conditions
     * @param array $params
     * @return array|null
     */
    public function findFilteredEvents(array $conditions, array $params): ?array
    {
        $cacheKey = 'events_filtered_' . md5(json_encode($conditions) . json_encode($params));

        return $this->cache->get($cacheKey, function () use ($conditions, $params) {
            $sql = "SELECT id, name, from_date, to_date, available_seats FROM events";
            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }

            $stmt = $this->readConnection->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }, 300);
    }

    /**
     * Find one event by its ID.
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->readConnection->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        return $event ?: null;
    }
}
