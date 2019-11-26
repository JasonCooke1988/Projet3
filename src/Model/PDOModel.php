<?php

namespace App\Model;

use PDO;

/**
 * PDOModel
 * Prepares Queries before execution & return
 */
class PDOModel
{
    /**
     * PDO Connection
     */
    private $pdo;

    /**
     * PDOModel constructor
     * Receive the PDO Connection & store it
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns a unique result from the Database
     */
    public function getData(string $query, array $params = [])
    {
        $PDOCall = $this->pdo->prepare($query);
        $PDOCall->execute($params);
        return $PDOCall->fetch();
    }

    /**
     * Returns many results from the Database
     */
    public function getAllData(string $query, array $params = [])
    {
        $PDOCall = $this->pdo->prepare($query);
        $PDOCall->execute($params);
        return $PDOCall->fetchAll();
    }

    /**
     * Executes an action to the Database
     */
    public function setData(string $query, array $params = [])
    {
        $PDOCall = $this->pdo->prepare($query);
        return $PDOCall->execute($params);
    }
}