<?php

namespace App\Repository;

use PDO;
use App\Manager\ConnectionManager;
use DateTimeImmutable;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Repository for handling CRUD operations on the `bookings` table.
 */
class BookingRepository
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
     * @var CacheInteface
     */
    private CacheInterface $cache;

    /**
     * @param CacheInterface     $cache
     * @param ConnectionManager  $connectionManager
     */
    public function __construct(
        CacheInterface $cache,
        ConnectionManager $connectionManager
    ) {
        $this->cache           = $cache;
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
     * Commits the current transaction.
     */
    public function commit(): void
    {
        $this->writeConnection->commit();
    }

    /**
     * Rollbacks current transaction.
     */
    public function rollBack(): void
    {
        $this->writeConnection->rollBack();
    }

    /**
     * Find a booking by its (event) ID.
     * @param string $reference
     * @return array|null
     */
    public function findById(string $reference): ?array
    {
        $stmt = $this->readConnection->prepare('SELECT * FROM bookings WHERE reference = :reference');
        $stmt->execute(['reference' => $reference]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Delete a booking by ID.
     * @param string $reference
     * @return bool
     */
    public function deleteById(string $reference): bool
    {
        $stmt = $this->writeConnection->prepare('DELETE FROM bookings WHERE reference = :reference');
        return $stmt->execute(['reference' => $reference]);
    }

    /**
     * Check if a user already has a booking on a given date.
     * @param DateTimeImmutable $date
     * @param string            $buyerId
     * @return array|null
     */
    public function findByBuyerIdAndDate(DateTimeImmutable $date, string $buyerId): ?array
    {
        $stmt = $this->readConnection->prepare(
            'SELECT * FROM bookings WHERE event_date = :event_date AND buyer_id = :buyer_id'
        );
        $stmt->execute([
            'event_date' => $date->format('Y-m-d'),
            'buyer_id'   => $buyerId
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * List bookings by user identification (cached for 5 minutes).
     * @param string $identification
     * @return array
     */
    public function findByIdentification(string $identification): array
    {
        $cacheKey = 'bookings_by_identification_' . md5($identification);

        return $this->cache->get($cacheKey, function () use ($identification) {
            $stmt = $this->readConnection->prepare('SELECT * FROM bookings WHERE buyer_id = :identification');
            $stmt->execute(['identification' => $identification]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }, 300);
    }

    /**
     * Insert a new booking.
     * @param array $data
     * @return bool
     */
    public function save(array $data): bool
    {
        $sql = 'INSERT INTO bookings (buyer_id, reference, event_id, event_date, attendees)
                VALUES (:buyer_id, :reference, :event_id, :event_date, :attendees)';

        $stmt = $this->writeConnection->prepare($sql);

        return $stmt->execute([
            'buyer_id'  => $data['identification'],
            'reference' => $data['reference'],
            'event_id'  => $data['event_id'],
            'event_date' => $data['event_date']->format('Y-m-d'),
            'attendees' => $data['attendees'],
        ]);
    }
}
