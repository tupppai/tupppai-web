<?php namespace App\Exceptions;

use RuntimeException;

class ServiceException extends RuntimeException
{
    public function __construct($info) {
        parent::__construct($info);

        header('Content-type: application/json; charset=utf-8');
        $this->setInfo($info);
    }

    /**
     * Name of the affected Info info.
     *
     * @var string
     */
    protected $info;

    /**
     * Set the affected Info info.
     *
     * @param  string   $info
     * @return $this
     */
    public function setinfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get the affected Info info.
     *
     * @return string
     */
    public function getinfo()
    {
        return $this->info;
    }
}
