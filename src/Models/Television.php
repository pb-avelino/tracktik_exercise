<?php

namespace TrackTik\Models;

use TrackTik\Models\ElectronicItem;

class Television extends ElectronicItem
{
    /** @inheritDoc */
    protected $type = self::ELECTRONIC_ITEM_TELEVISION;

    public function canAddExtras(): bool
    {
        return true;
    }
}
