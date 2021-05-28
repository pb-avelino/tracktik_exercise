<?php

namespace TrackTik\Models;

use TrackTik\Interfaces\ControllerInterface;
use TrackTik\Models\ElectronicItem;


class Controller extends ElectronicItem implements ControllerInterface
{
    /** @inheritDoc */
    protected $type = self::ELECTRONIC_ITEM_CONTROLLER;

    /** @var bool */
    protected $wired;

    /** @inheritDoc */
    public function getWired(): bool
    {
        return $this->wired;
    }

    /** @inheritDoc */
    public function setWired($wired)
    {
        $this->wired = $wired;
    }
}
