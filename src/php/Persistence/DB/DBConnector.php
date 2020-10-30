<?php


namespace WPPluginCore\Persistence\DB;

defined('ABSPATH') || exit;

use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;
use WPPluginCore\Abstraction\IBaseFactory;
use WPPluginCore\Exception\QueryException;
use WPPluginCore\Logger;

class DBConnector 
{

    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, string $dbName, string $dbHost, string $dbUser, string $dbPassword)
    {
        $this->connection = new PDO("mysql:dbname=$dbName;host=$dbHost",
            $dbUser,
            $dbPassword
        );
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        $this->logger = $logger;
    }
    /**
     * Returns the PDO
     *
     * @return PDO
     */
    public function getConnection() : PDO
    {
        return $this->connection;
    }

    /**
     * Executing PDO Query (Should only used if run one SELECT) and returns an array of results, where each result is an assoc array
     *
     * @param string $sql MYSQL SELECT Statement
     *
     * @return array result of Query (false if it doesent return anything)
     * @throws QueryException if something on executing query gone wrong
     */
    final public function queryMultiple(string $sql) : array
    {
        $return = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($return === false) {
            throw new QueryException('on fetchAll something went wrong');
        }
        return $return;
    }

    /**
     * Executing PDO Query (Should only used if run one SELECT) returns a single object (fetch) and returns it as assoc array
     *
     * @param string $sql MYSQL SELECT Statement
     *
     * @return scalar[] result of Query
     *
     * @throws QueryException if something on executing query gone wrong
     *
     * @psalm-return array<string, scalar>
     */
    final public function querySingle(string $sql) : array
    {
        $return = $this->query($sql)->fetch(PDO::FETCH_ASSOC);
        if ($return === false) {
            if ($this->getConnection()->errorCode() === "00000") { //todo workarround
                return array();
            } else {
                $this->logger->error("On executing query something went wrong", $this->getConnection()->errorInfo()   );
                throw new QueryException('on fetch something went wrong');
            }
        }
        return $return;
    }

    final private function query(string $sql) : PDOStatement
    {
        $con = $this->getConnection();
        $result = $con->query($sql);

        if (! isset($result) || !$result) {
            throw new QueryException("On executing query something went wrong: \n ".var_export($con->errorInfo(), true));
        }

        return $result;
    }

    /**
     * Execute an SQL Statement and Returns the Affected Rows
     *
     * @param string $sql the SQL Statement that u want to execute
     * @param bool $nonZero if the affected Rows should not be Zero
     *
     * @return int the affected Rows
     *
     * @throws QueryException if Zero rows affected
     */
    final public function exec(string $sql, bool $nonZero = true) :int
    {
        $result =  $this->getConnection()->exec($sql);
        if ($nonZero && $result <= 0) {
            throw new QueryException("Zero Rows affected");
        }
        return $result;
    }
}
