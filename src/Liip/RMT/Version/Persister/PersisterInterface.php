<?php

namespace Liip\RMT\Version\Persister;

use Liip\RMT\Exception\NoReleaseFoundException;

interface PersisterInterface
{
    /**
     * Constructor can take options.
     * 
     * @param array $options
     */
    public function __construct(array $options = array());

    /**
     * Return the current release name
     *
     * @return mixed The current release number
     * @throws NoReleaseFoundException
     * */
    public function getCurrentVersion();

    public function save($versionNumber);

    public function getInformationRequests();

    // Use the very first time to init this persistence
    public function init();
}

