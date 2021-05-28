<?php

namespace TrackTik\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use TrackTik\Interfaces\ItemInterface;
use TrackTik\Models\ElectronicItem;
use TrackTik\Models\ElectronicItems;
use TrackTik\Models\ItemFactory;

class Excercise extends Command
{
    use \TrackTik\Traits\ControllerTrait;

    /** @var \Symfony\Component\Console\Helper\QuestionHelper */
    private $helper;

    /** @var string */
    private $itemFormat = " %s Info\n -Price: %s\n -Controller(s): %d of (%d)";

    /** @var string */
    private $poFormat = " %s\t with %d controller(s) \t\t%s";

    /** @inheritDoc */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->helper = $this->getHelper('question');
    }

    /** @inheritDoc */
    protected function configure()
    {
        $this
            ->setDescription('Developer Evaluation')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'What do you want to do? (-h or --help to see list of actions)'
            )
            ->setHelp(<<<HELP
- ACTIONS:
 * create : Create an electronic Item
 * q1a: Question 1 on developer evaluation (PO created by the program).
 * q1m: Question 1 on developer evaluation (PO is created by the user).
 * q2: Question 2 on customer evaluation.
HELP);
    }

    /** @inheritDoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        switch ($action) {
            case 'create':
                $item = $this->createItem($input, $output);
                $output->writeln(sprintf(
                    $this->itemFormat,
                    $item->getType(),
                    $item->getPrice(),
                    count($item->getControllers()),
                    $item->getMaxExtras(),
                ));
                break;

            case 'q1m':
                $po = $this->manualPurchaseOrder($input, $output);
                $this->outputPo($output, $po->getSortedItems('price'));
                break;

            case 'q1a':
                $po = $this->autoPurchaseOrder($input, $output);
                $this->outputPo($output, $po->getSortedItems('price'));
                break;

            case 'q2':
                $this->displayPurchaseOrderItems($input, $output);
                break;
            default:
                $output->writeln(sprintf('<error>** Action "%s" not supported. See help (-h) for more information.</error>', $action));
                exit(1);
                break;
        }

        exit(0);
    }

    /**
     * Create and electronic Item with attachments and price.
     *
     * @param InputInterface $input
     * @param OuputInterface $output
     * @return ItemInterface
     */
    private function createItem(InputInterface $input, OutputInterface $output)
    {
        $itemTypeQuestion = new ChoiceQuestion(
            'What type of Item: ',
            array_values(ElectronicItem::$types)
        );
        $itemType = $this->helper->ask($input, $output, $itemTypeQuestion);

        $priceQuestion = new Question("What is the price of the $itemType? > ");
        $itemPrice = $this->helper->ask($input, $output, $priceQuestion);

        /** @var ElectronicItem $item */
        $item = ItemFactory::create($itemType);
        $item->setPrice($itemPrice);

        $numberOfControllers = 0;
        if ($item->canAddExtras()) {
            $extrasQuestion = new Question('How many controllers do you want to add to the item? > ');
            $numberOfControllers = $this->helper->ask($input, $output, $extrasQuestion);
        }

        for ($i = 0; $i < $numberOfControllers; $i++) {
            if (!$item->canAddExtras()) {
                $output->writeln("<comment>Max number of contollers reached!</comment>\n");
                break;
            }

            $wiredQuestion = new ConfirmationQuestion(sprintf('Is the controller %d wired (y|n)? > ', ($i + 1)));
            $wired = $this->helper->ask($input, $output, $wiredQuestion);

            /** @var \TrackTik\Models\Controller $controller */
            $controller = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_CONTROLLER);
            $controller->setWired($wired);

            $item->addController($controller);
        }

        return $item;
    }

    /**
     * Creates a Purchase order with user interaction.
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ElectronicItems
     */
    private function manualPurchaseOrder(InputInterface $input, OutputInterface $output): object
    {
        $numberOfitemsQuestion = new Question('How many items you want to create? > ');
        $numberofItems = $this->helper->ask($input, $output, $numberOfitemsQuestion);
        $items = [];

        for ($i = 0; $i < $numberofItems; $i++) {
            $items[] = $this->createItem($input, $output);
        }

        return new ElectronicItems($items);
    }

    /**
     * Create a Purchase order with:
     *    - 1 console with 2 remote and 2 wired controllers
     *    - 1 tv with random price and 2 remote controllers
     *    - 1 tv with random price and 1 remote controllers
     *    - 1 microwave
     * Sort the items by price and output the total pricing.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ElectronicItems
     */
    private function autoPurchaseOrder(InputInterface $input, OutputInterface $output): object
    {
        $console = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_CONSOLE);
        $console->setPrice($this->generatePrice());
        $this->attachControllers($console, ['wired' => 2, 'remote' => 2]);

        $tv1 = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_TELEVISION);
        $tv1->setPrice($this->generatePrice());
        $this->attachControllers($tv1, ['remote' => 2]);

        $tv2 = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_TELEVISION);
        $tv2->setPrice($this->generatePrice());
        $this->attachControllers($tv2, ['remote' => 1]);

        $microwave = ItemFactory::create(ItemInterface::ELECTRONIC_ITEM_MICROWAVE);
        $microwave->setPrice($this->generatePrice());

        return new ElectronicItems(
            [$console, $tv1, $tv2, $microwave]
        );
    }

    /**
     * Creates a PO then prompts the user for a "type" and returns the items of that type.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function displayPurchaseOrderItems(InputInterface $input, OutputInterface $output)
    {
        $poQuestion = new ChoiceQuestion(
            'How do you want to create your purchase order (defaults to auto)',
            ['auto' => 'a', 'manual' => 'm'],
            'a'
        );
        $poQuestion->setMaxAttempts(5);

        $poType = $this->helper->ask($input, $output, $poQuestion);

        if ($poType == 'm') {
            $po = $this->manualPurchaseOrder($input, $output);
        } else {
            $po = $this->autoPurchaseOrder($input, $output);
        }

        $itemTypeQuestion = new ChoiceQuestion(
            'Select what type of items to display ',
            array_values(ElectronicItem::$types)
        );
        $itemType = $this->helper->ask($input, $output, $itemTypeQuestion);

        $items = $po->getItemsByType($itemType);

        if (empty($items)) {
            $output->writeln('No items found.');
        } else {
            $this->outputPo($output, $items);
        }
    }

    /**
     * Generates a random float
     *
     * @return float
     */
    private function generatePrice(): float
    {
        return rand(100, 1000) + (rand(0, 100) / 100);
    }

    /**
     * Print PO
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array $sorted
     */
    private function outputPo(OutputInterface $output, array $sorted)
    {
        $total = 0;
        $output->writeln("\n PURCHASE ORDER");
        $output->writeln(str_repeat('=', 100));
        /** @var ElectronicItem $item */
        foreach ($sorted as $item) {
            $output->writeln(sprintf(
                $this->poFormat,
                $item->getType(),
                count($item->getControllers()),
                number_format($item->getPrice(), 2)
            ));

            $total += $item->getPrice();
        }


        $output->writeln(str_repeat('=', 100));
        $output->writeln("<info>- Total: $total</info>");
    }
}
