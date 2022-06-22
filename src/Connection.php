<?php

class Connection
{
    const ERROR_UNABLE = "ERROR: Unable to create database connection";
    public $pdo;

    /**
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config)
    {
        if (!isset($config['driver'])) {
            $message = __METHOD__ . " : " . self::ERROR_UNABLE . PHP_EOL;
            throw new Exception($message);
        }

        $dsn = $config['driver']
            . ":host=" . $config['host']
            . ";dbname=" . $config['dbname'];

        try {
            $this->pdo = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                [PDO::ATTR_ERRMODE => $config['errmode']]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
