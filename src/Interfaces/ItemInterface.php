<?php

namespace TrackTik\Interfaces;

interface ItemInterface
{

    const ELECTRONIC_ITEM_TELEVISION = 'television';
    const ELECTRONIC_ITEM_CONSOLE = 'console';
    const ELECTRONIC_ITEM_MICROWAVE = 'microwave';
    const ELECTRONIC_ITEM_CONTROLLER = 'controller';

    /**
     * @return void
     */
    public function getMaxExtras(): int;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param float $price
     */
    public function setPrice($price);

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return bool
     */
    public function canAddExtras(): bool;

    /**
     * Add electronic item.
     *
     * @param ItemInterface $item
     */
    public function addController(ItemInterface $item);

    /**
     * @return array
     */
    public function getControllers(): array;
}
