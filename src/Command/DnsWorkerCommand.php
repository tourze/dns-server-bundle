<?php

declare(strict_types=1);

namespace DnsServerBundle\Command;

use DnsServerBundle\Service\DnsWorkerService;
use React\EventLoop\Loop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: self::NAME,
    description: 'Start DNS worker'
)]
class DnsWorkerCommand extends Command
{
    public const NAME = 'dns:worker:start';

    public function __construct(
        private readonly DnsWorkerService $dnsWorkerService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'DNS server host', '0.0.0.0')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'DNS server port', 53);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption('host');
        $port = (int)$input->getOption('port');

        $loop = Loop::get();
        $this->dnsWorkerService->start($loop, $host, $port);
        $loop->run();

        return Command::SUCCESS;
    }
}
