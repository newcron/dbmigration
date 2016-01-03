<?php


namespace dbmigrate\application\schema;


class Migration
{
    private $id;
    private $installedAt;
    private $filename;
    private $checksum;
    private $success;

    /**
     * Migration constructor.
     * @param int $id
     * @param $installedAt
     * @param string $filename
     * @param string $checksum
     * @param bool $success
     */
    public function __construct($id, \DateTime $installedAt, $filename, $checksum, $success)
    {
        $this->id = $id;
        $this->installedAt = $installedAt;
        $this->filename = $filename;
        $this->checksum = $checksum;
        $this->success = $success;
    }


    /** @return string */
    public function getFilename()
    {
        return $this->filename;
    }

    /** @return string */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /** @return bool */
    public function getSuccess()
    {
        return $this->success;
    }

    /** @return int */
    public function getId()
    {
        return $this->id;
    }

    /** @return \DateTime */
    public function getInstalledAt()
    {
        return $this->installedAt;
    }






}