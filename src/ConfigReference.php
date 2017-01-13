<?php
namespace ObjectivePHP\Config;

class ConfigReference
{

    /**
     * @var int Id of the config
     */
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string) $this->id;
    }
}
