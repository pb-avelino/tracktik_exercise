<?php

namespace TrackTik\Models;

use TrackTik\Models\ElectronicItem;

class Microwave extends ElectronicItem
{
    /** @inheritDoc */
    protected $type = self::ELECTRONIC_ITEM_MICROWAVE;
}
