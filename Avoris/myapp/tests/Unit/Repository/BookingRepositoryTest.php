<?php

namespace App\Tests\Unit\Repository;

use App\Manager\ConnectionManager;
use App\Repository\BookingRepository;
use DateTimeImmutable;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Test block for checking EventRepository operations
 */
class BookingRepositoryTest extends TestCase
{
    /**
     * @var $readPdo
     */
    private $readPdo;

    /**
     * @var $writePdo
     */
    private $writePdo;

    /**
     * @var $cache
     */
    private $cache;

    /**
     * @var $connectionManager
     */
    private $connectionManager;

    /**
     * @var BookingRepository
     */
    private BookingRepository $repository;

    /**
     * Set an instance of mock object BookingRepository
     * with read and write connection manager adapters
     * @return void
     */
    protected function setUp(): void
    {
        $this->readPdo = $this->createMock(PDO::class);
        $this->writePdo = $this->createMock(PDO::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->connectionManager = $this->createMock(ConnectionManager::class);
        $this->connectionManager->method('getReadConnection')->willReturn($this->readPdo);
        $this->connectionManager->method('getWriteConnection')->willReturn($this->writePdo);

        $this->repository = new BookingRepository($this->cache, $this->connectionManager);
    }

    /**
     * Test responsible for checking if booking
     * exist in repository searching by ID
     * @return void
     */
    public function testFindByIdReturnsResult(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $expected = ['event_id' => 123, 'reference' => 'ABC123'];

        $this->readPdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT * FROM bookings WHERE reference = :reference')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with(['reference' => 123]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $result = $this->repository->findById(123);

        $this->assertSame($expected, $result);
    }

    /**
     * Test responsible for checking if user has bookings in a concrete date
     * @return void
     */
    public function testFindByBuyerIdAndDateReturnsResult(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $date = new DateTimeImmutable('2025-08-07');
        $expected = ['buyer_id' => 'X123', 'event_date' => '2025-08-07'];

        $this->readPdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([
                'event_date' => $date->format('Y-m-d'),
                'buyer_id'   => 'X123',
            ]);

        $stmt->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($expected);

        $result = $this->repository->findByBuyerIdAndDate($date, 'X123');

        $this->assertSame($expected, $result);
    }

    /**
     * Test for checking cache booking results in redis
     * @return void
     */
    public function testFindByIdentificationReturnsCachedResult(): void
    {
        $identification = 'buyer-abc';
        $expected = [['reference' => 'REF123']];

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->stringContains(md5($identification)),
                $this->isInstanceOf(\Closure::class),
                300
            )
            ->willReturnCallback(function ($key, $callback) {
                $stmt = $this->createMock(PDOStatement::class);
                $stmt->method('execute')->willReturn(true);
                $stmt->method('fetchAll')->willReturn([['reference' => 'REF123']]);

                $this->readPdo->method('prepare')->willReturn($stmt);

                return $callback();
            });

        $result = $this->repository->findByIdentification($identification);

        $this->assertSame($expected, $result);
    }

    /**
     * Test responsible for checking correct insertions of bookings in DB
     * @return void
     */
    public function testSaveInsertsBookingSuccessfully(): void
    {
        $data = [
            'identification' => 'buyer-1',
            'reference'      => 'REF456',
            'event_id'       => 42,
            'event_date'     => new DateTimeImmutable('2025-09-01'),
            'attendees'      => 3,
        ];

        $stmt = $this->createMock(PDOStatement::class);

        $this->writePdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $stmt->expects($this->once())
            ->method('execute')
            ->with([
                'buyer_id'   => 'buyer-1',
                'reference'  => 'REF456',
                'event_id'   => 42,
                'event_date' => '2025-09-01',
                'attendees'  => 3,
            ])
            ->willReturn(true);

        $result = $this->repository->save($data);

        $this->assertTrue($result);
    }
}
