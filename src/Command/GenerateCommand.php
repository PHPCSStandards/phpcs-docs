<?php
declare(strict_types=1);

namespace App\Command;

use App\Handler\GenerateHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected static $defaultName = 'generate';
    private GenerateHandler $handler;

    public function __construct(GenerateHandler $handler)
    {
        parent::__construct(self::$defaultName);
        $this->handler = $handler;
    }

    protected function configure()
    {
        $this->addArgument('sniff', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sniffPath = null;
        if ($input->hasArgument('sniff')) {
            $sniffPath = $input->getArgument('sniff');
        }

        foreach ($this->handler->handle($sniffPath) as $message) {
            $output->writeln($message);
        };

        return Command::SUCCESS;
    }
}
