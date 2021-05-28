<?php

namespace TrackTik\Models;

class ElectronicItems
{

    const ALL_TYPES = 'all';

    /** @var array \TrackTik\Interfaces\ItemInterface */
    private $items = [];

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Returns the items depending on the sorting type requested
     *
     * @return array
     */
    public function getSortedItems($type)
    {

        $sorted = [];
        /** @var \TrackTik\Interfaces\ItemInterface $item */
        foreach ($this->items as $item) {

            $sorted[($item->getPrice() * 100)] = $item;
        }

        ksort($sorted, SORT_NUMERIC);

        return $sorted;
    }

    /**
     *
     * @param string $type
     * @return array
     */
    public function getItemsByType($type)
    {
        if ($type == self::ALL_TYPES) {
            return $this->items;
        }

        if (in_array($type, ElectronicItem::$types)) {

            /** @var \TrackTik\Interfaces\ItemInterface $item */
            $callback = function ($item) use ($type) {

                return $item->getType() == $type;
            };

            return array_filter($this->items, $callback);
        }

        return [];
    }
}
