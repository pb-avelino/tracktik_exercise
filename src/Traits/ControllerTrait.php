<?php

namespace TrackTik\Traits;

use TrackTik\Interfaces\ItemInterface;
use TrackTik\Models\ItemFactory;

trait ControllerTrait
{
    /**
     * Create and attach Controllers
     *
     * @param ItemInterface $item
     * @param array $config
     *      ["wired" => n, "remote" => n]
     */
    private function attachControllers(ItemInterface &$item, array $config)
    {
        foreach ($config as $k => $v) {
            $wired = $k == 'wired';
            for ($i = 0; $i < intval($v); $i++) {
                /** @var \TrackTik\Models\Controller $controller */
                $controller = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_CONTROLLER);
                $controller->setWired($wired);
                if ($item->canAddExtras()) {
                    $item->addController($controller);
                }
            }
        }
    }
}
