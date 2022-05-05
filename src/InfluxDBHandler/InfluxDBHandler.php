<?php

namespace App\Monolog\Handler;

use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Database\RetentionPolicy;
use InfluxDB\Point;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * This class is a handler for Monolog, which can be used
 * to write records to an Influx database.
 *
 * Class InfluxDBHandler
 */
class InfluxDBHandler extends AbstractProcessingHandler
{
    /**
     * @var bool defines wether the InfluxDB connection has been initialized
     */
    private $initialised = false;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $db;

    /**
     * @var bool
     */
    private $async;

    /**
     * @var string
     */
    private $retention_duration;

    /**
     * @var string
     */
    private $retention_replication;

    /**
     * InfluxDBHandler constructor.
     *
     * @param int    $level
     * @param string $retention_duration
     * @param int    $retention_replication
     *
     * @throws Client\Exception
     */
    public function __construct(
        string $username = 'admin',
        string $password = 'admin',
        string $protocol = 'influxdb',
        string $host = 'influxdb.internal',
        string $port = '8086',
        string $db = 'manageritalia',
        $level = Logger::DEBUG,
        bool $bubble = true,
        $retention_duration = '3M',
        $retention_replication = 1)
    {
        $this->username = $username;
        $this->password = $password;
        $this->protocol = $protocol;
        $this->host = $host;
        $this->port = $port;
        $this->db = $db;
        $this->retention_duration = $retention_duration;
        $this->retention_replication = $retention_replication;
        $this->connection = Client::fromDSN(sprintf('%s://%s:%s@%s:%s/%s', $this->protocol, $this->username, $this->password, $this->host, $this->port, $this->db));
        parent::__construct($level, $bubble);
    }

    private function initialise()
    {
        // check if a database exists then create it if it doesn't
        $database = $this->connection->getClient()->selectDB($this->db);

        if (!$database->exists()) {
            $this->connection->create(new RetentionPolicy($this->db.'_rp', $this->retention_duration, $this->retention_replication, true));
        }
        $this->initialized = true;
    }

    /**
     * @return bool|void
     *
     * @throws \InfluxDB\Database\Exception
     * @throws \InfluxDB\Exception
     * Example: $logger->info("User succesfully logged in.", array('username'  => 'Peter Doe', 'userid'  => 89));
     */
    protected function write(array $record): void
    {
        if (!$this->initialised) {
            $this->initialise();
        }

        $tags = [
            'level' => $record['level'],
            'level_name' => $record['level_name'],
        ];
        $tags = array_merge($tags, $record['context']);

        // Microseconds
        list($usec, $sec) = explode(' ', microtime());
        $timestamp = sprintf('%d%06d', $sec, $usec * 1000000);

        // working: working create an array of points
        $points = [
           new Point(
               $record['channel'], // name of the measurement
               null, // the measurement value
               ['source' => $record['channel']], // optional tags
               ['msg' => $record['message']], // optional additional fields
               $timestamp // Time precision
           ),
       ];

        //    var_dump($record['channel']);
        //    var_dump($record['message']);

        // $points = array(
        //     new Point(
        //         "test_log", // name of the measurement
        //         null, // the measurement value
        //         // $record['channel'],
        //         // $record['message'],
        //         ['step' => 'logData'], // optional tags

        //         $tags,
        //         $timestamp
        //     )
        // );

        // now just write your points like you normally would
        $result = $this->connection->writePoints($points, Database::PRECISION_MICROSECONDS);
    }
}
