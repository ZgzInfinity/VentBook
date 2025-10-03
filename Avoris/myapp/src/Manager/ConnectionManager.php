<?php

namespace App\Manager;

/**
 * ConnectionManager provides access to database read & write PDO instances.
 * Useful to easily implement split READ/WRITE logic (master/slave or replica).
 */
class ConnectionManager
{
    /**
     *  @var \PDO 
     */
    private \PDO $writeConnection;

    /**
     * @var \PDO 
     */
    private \PDO $readConnection;

    /**
     * @param \PDO $writeConnection
     * @param \PDO $readConnection
     */
    public function __construct(\PDO $writeConnection, \PDO $readConnection)
    {
        $this->writeConnection = $writeConnection;
        $this->readConnection  = $readConnection;
    }

    /**
     * Returns the read-only PDO connection.
     */
    public function getReadConnection(): \PDO
    {
        return $this->readConnection;
    }

    /**
     * Returns the write PDO connection.
     */
    public function getWriteConnection(): \PDO
    {
        return $this->writeConnection;
    }
}

