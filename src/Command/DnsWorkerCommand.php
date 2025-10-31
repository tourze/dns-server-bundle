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
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[AsCommand(
    name: self::NAME,
    description: 'Start DNS worker'
)]
#[Autoconfigure(public: true)]
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
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'DNS server port', 53)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hostOption = $input->getOption('host');
        $portOption = $input->getOption('port');

        // Type-safe extraction with explicit validation
        $host = is_string($hostOption) ? $hostOption : '0.0.0.0';
        $port = is_numeric($portOption) ? (int) $portOption : 53;

        $loop = Loop::get();
        $this->dnsWorkerService->start($loop, $host, $port);
        $loop->run();

        return Command::SUCCESS;
    }
}
