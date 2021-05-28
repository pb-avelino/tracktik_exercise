<?php

namespace TrackTik\Models;

use TrackTik\Interfaces\ItemInterface;

class ElectronicItem implements ItemInterface
{

    /**
     * @var float
     */
    protected $price = 0;

    /**
     * Limits the number of extras an electronic item can have.
     *
     * @var int
     */
    protected $maxExtras = 0;

    /** @var array */
    protected $controllers = [];

    /**
     * @var string
     */
    protected $type;

    public static $types = [
        self::ELECTRONIC_ITEM_CONSOLE,
        self::ELECTRONIC_ITEM_MICROWAVE,
        self::ELECTRONIC_ITEM_TELEVISION,
        self::ELECTRONIC_ITEM_CONTROLLER,
    ];

    public function __construct()
    {
    }

    /** @inheritDoc */
    public function getMaxExtras(): int
    {
        return $this->maxExtras;
    }

    /** @inheritDoc */
    public function canAddExtras(): bool
    {
        return count($this->controllers) < $this->maxExtras;
    }

    /** @inheritDoc */
    public function addController(ItemInterface $item)
    {
        if ($item->getType() != self::ELECTRONIC_ITEM_CONTROLLER) {
            throw new \InvalidArgumentException('Item has to be of the type -> ' . self::ELECTRONIC_ITEM_CONTROLLER);
        }
        $this->controllers[] = $item;
    }

    /** @inheritDoc */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /** @inheritDoc */
    public function getPrice(): float
    {
        return $this->price;
    }

    /** @inheritDoc */
    public function getType(): string
    {
        return $this->type;
    }

    /** @inheritDoc */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /** @inheritDoc */
    public function setType($type)
    {
        $this->type = $type;
    }
}
