<?php

class LargeFileIterator
{
    const ERROR_UNABLE = "ERROR: Unable to open file";
    const ERROR_TYPE = "ERROR: Type must be 'ByLength', 'ByLine' or 'CSV'";

    protected $file;
    protected $allowedTypes = ['ByLine', 'ByLength', 'Csv'];

    /**
     * @param $filename
     * @param string $mode
     * @throws Exception
     */
    public function __construct($filename, $mode = 'r')
    {
        if (!file_exists($filename)) {
            $message = __METHOD__ . " : " . self::ERROR_UNABLE . PHP_EOL;
            $message .= strip_tags($filename) . PHP_EOL;
            throw new Exception($message);
        }

        $this->file = new SplFileObject($filename, $mode);
    }

    protected function fileIteratorByLine()
    {
        $count = 0;
        while (!$this->file->eof()) {
            yield $this->file->fgets();
            $count++;
        }

        return $count;
    }

    protected function fileIteratorByLength($numBytes = 1024)
    {
        $count = 0;
        while (!$this->file->eof()) {
            yield $this->file->fread($numBytes);
            $count++;
        }

        return $count;
    }

    protected function fileIteratorCsv()
    {
        $count = 0;
        while (!$this->file->eof()) {
            yield $this->file->fgetcsv();
            $count++;
        }

        return $count;
    }

    /**
     * @param string $type
     * @param null $numBytes
     * @return NoRewindIterator
     */
    public function getIterator(string $type = 'ByLine', $numBytes = null): NoRewindIterator
    {
        if (!in_array($type, $this->allowedTypes)) {
            $message = __METHOD__ . " : " . self::ERROR_TYPE . PHP_EOL;
            throw new InvalidArgumentException($message);
        }

        $iterator = "fileIterator" . $type;

        return new NoRewindIterator($this->$iterator($numBytes));
    }
}
