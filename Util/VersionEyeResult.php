<?php

namespace Mattsches\VersionEyeBundle\Util;

/**
 * Class VersionEyeResult
 * @package Mattsches\VersionEyeBundle\Util
 */
class VersionEyeResult
{
    /**
     *
     */
    const GREEN = '#0e0';

    /**
     *
     */
    const RED = '#f66';

    /**
     *
     */
    const GREY = '#999';

    /**
     *
     */
    const YELLOW = '#fc0';

    /**
     *
     */
    const STATUS_OK = 1;
    /**
     *
     */
    const STATUS_ERR = 0;

    /**
     * @var
     */
    protected $status;
    /**
     * @var
     */
    protected $depNumber;
    /**
     * @var
     */
    protected $outNumber;
    /**
     * @var array
     */
    protected $dependencies = array();

    /**
     * @param int $status
     * @param array $json
     */
    public function __construct($status, array $json = array())
    {
        $this->setStatus($status);
        if (isset($json['dep_number'])) {
            $this->setDepNumber($json['dep_number']);
        }
        if (isset($json['out_number'])) {
            $this->setOutNumber($json['out_number']);
        }
        if (isset($json['dependencies'])) {
            $this->setDependencies($json['dependencies']);
        }
    }

    /**
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies = array())
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param $depNumber
     */
    public function setDepNumber($depNumber)
    {
        $this->depNumber = $depNumber;
    }

    /**
     * @return mixed
     */
    public function getDepNumber()
    {
        return $this->depNumber;
    }

    /**
     * @param $outNumber
     */
    public function setOutNumber($outNumber)
    {
        $this->outNumber = $outNumber;
    }

    /**
     * @return mixed
     */
    public function getOutNumber()
    {
        return $this->outNumber;
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param array $dependency
     * @return string
     */
    public function getStabilityColor(array $dependency = array())
    {
        if (!array_key_exists('outdated', $dependency)
            || !array_key_exists('stable', $dependency)
            || !array_key_exists('unknown', $dependency)
            || $dependency['unknown'] === true
        ) {
            return self::GREY;
        } elseif ($dependency['outdated'] === true && $dependency['stable'] === true) {
            return  self::RED;
        } elseif ($dependency['outdated'] === true && $dependency['stable'] === false) {
            return self::YELLOW;
        }
        return self::GREEN;
    }
}
