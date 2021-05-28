<?php

namespace TrackTik\Models;

use TrackTik\Models\ElectronicItem;

class Console extends ElectronicItem
{
    /** @inheritDoc */
    protected $maxExtras = 4;

    /** @inheritDoc */
    protected $type = self::ELECTRONIC_ITEM_CONSOLE;
}
