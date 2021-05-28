<?php

namespace TrackTik\Interfaces;


interface ControllerInterface
{
    /**
     * @return bool
     */
    public function getWired(): bool;

    /**
     * @param bool $wired
     */
    public function setWired($wired);
}
