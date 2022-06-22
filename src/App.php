<?php

class App
{
    private $filename;
    private $connection;
    private $largeFileIterator;

    /**
     * @param $filename
     * @throws Exception
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->connection = new Connection(include_once __DIR__ . "data/config/db.config.php");
        $this->largeFileIterator = new LargeFileIterator(__DIR__ . "data/files/" . $filename);
    }

    public function start($type = 'ByLine', $push = false) // 'ByLine', 'ByLength', 'Csv'
    {
        $iterator = $this->largeFileIterator->getIterator($type);

        $words = 0;
        $lines = 0;
        foreach ($iterator as $line) {
            echo $line;
            $lines++;
            $words += str_word_count($line);
        }
        echo str_repeat('-', 52) . PHP_EOL;
        printf("%-40s : %8d\n", "Total words", $words);
        printf("%-40s : %8d\n", "Average words number per line", $words / $lines);
        echo str_repeat('-', 52) . PHP_EOL;

        if ($push) $this->saveToDb($iterator);
    }

    // needs customization accordingly to input data representation/format
    public function saveToDb($iterator)
    {
        $sql = "INSERT INTO `table_name` "
            . "(`column1_name`, `column2_name`,..) "
            ." VALUES (?, ?,..)";
        $query = $this->connection->pdo->prepare($sql);

        foreach ($iterator as $row) {
            echo implode(',', $row) . PHP_EOL;
            $query->execute($row);
        }
    }
}
