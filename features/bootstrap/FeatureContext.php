<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use TrackTik\Interfaces\ItemInterface;
use TrackTik\Models\ElectronicItem;
use TrackTik\Models\ElectronicItems;
use TrackTik\Models\ItemFactory;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{

    use \TrackTik\Traits\ControllerTrait;

    public $contextVars = [
        'po' => null,
    ];

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given a purchase order with:
     */
    public function aPurchaseOrderWith(TableNode $table)
    {
        $tableItems = $table->getColumnsHash();
        $poItems = [];

        foreach ($tableItems as $item) {
            /** @var ItemInterface $poItem */
            $poItem = ItemFactory::create($item['type']);
            $poItem->setPrice($item['price']);
            unset($item['type'], $item['price']);
            $this->attachControllers($poItem, $item);
            $poItems[] = $poItem;
        }

        $this->contextVars['po'] = new ElectronicItems($poItems);
    }

    /**
     * @Then Purchase order has
     */
    public function purchaseOrderHas(TableNode $table)
    {
        $tableItems = $table->getColumnsHash();
        /** @var ElectronicItems $po */
        $po = $this->contextVars['po'];
        foreach ($tableItems as $item) {
            $poItems = $po->getItemsByType($item['type']);
            if (count($poItems) != $item['qty']) {
                throw new \Exception(sprintf(
                    "Expected %s of type %s, PO conatains %s",
                    $item['qty'],
                    $item['type'],
                    count($poItems)
                ));
            }
        }
    }

    /**
     * @Then total price of :arg1 items is grater than cero
     */
    public function totalPriceOfItemsIsGraterThanCero($arg1)
    {
        /** @var ElectronicItems $po */
        $po = $this->contextVars['po'];
        $total = 0;
        /** @var ItemInterface $item */
        foreach ($po->getItemsByType($arg1) as $item) {
            $total += $item->getPrice();
        }

        if (!$total) {
            throw new \Exception("Total is $total");
        }
    }
}
