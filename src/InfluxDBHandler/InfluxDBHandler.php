<?php
namespace InfluxDBHandler;

use InfluxDB\Point;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use InfluxDB\Client;
use InfluxDB\Database\RetentionPolicy;


/**
 * This class is a handler for Monolog, which can be used
 * to write records to an Influx database
 *
 * Class InfluxDBHandler
 * @package elardvermeulen\monolog-influxdb\InfluxDBHandler
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
     * @param string $username
     * @param string $password
     * @param string $protocol
     * @param string $host
     * @param string $port
     * @param string $db
     * @param bool|int $level
     * @param bool $bubble
     */
    public function __construct(
        string $username,
        string $password,
        string $protocol = "influxdb",
        string $host,
        string $port,
        string $db,
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
        $this->connection = Client::fromDSN($this->protocol.'://'.$this->username.':'.$this->password.'@'.$this->host.':'.$this->port.'/'.$this->db);
        parent::__construct($level, $bubble);
    }

    /**
     *
     */
    private function initialise() {

        // check if a database exists then create it if it doesn't
        $database = $this->connection->getClient()->selectDB($this->db);

        if (!$database->exists()) {
            $this->connection->create(new RetentionPolicy($this->db.'_rp', $this->retention_duration, $this->retention_replication, true));
        }
        $this->initialized = true;
    }

    /**
     * @param array $record
     */
    protected function write(array $record)
    {

        if (!$this->initialised) {
            $this->initialise();
        }

        $points = array();
        $tags = array(
            'level'         => $record['level'],
            'level_name'    => $record['level_name']
        );
        $tags = array_merge($tags, $record['context']);

        $points = array(
            new Point(
                $record['channel'],
                $record['message'],
                $tags
            )
        );

// now just write your points like you normally would
        $result = $this->connection->writePoints($points);
        return $result;

    }


}

