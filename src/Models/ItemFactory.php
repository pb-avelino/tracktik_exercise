<?php

namespace TrackTik\Models;

use TrackTik\Models\ElectronicItem;

class ItemFactory
{
    private static $namespace = "TrackTik\\Models\\";

    /**
     * Undocumented function
     *
     * @param string $type
     * @return \TrackTik\Interfaces\ItemInterface
     */
    public static function create($type): object
    {
        if (!in_array($type, ElectronicItem::$types)) {
            throw new \InvalidArgumentException("Unknoun type: $type");
        }

        $className = self::$namespace . ucfirst($type);
        return new $className();
    }
}
