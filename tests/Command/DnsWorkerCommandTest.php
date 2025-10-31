<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Command;

use DnsServerBundle\Command\DnsWorkerCommand;
use DnsServerBundle\Service\DnsWorkerService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(DnsWorkerCommand::class)]
#[RunTestsInSeparateProcesses]
final class DnsWorkerCommandTest extends AbstractCommandTestCase
{
    private CommandTester $commandTester;

    protected function getCommandTester(): CommandTester
    {
        if (!isset($this->commandTester)) {
            $command = self::getContainer()->get(DnsWorkerCommand::class);
            $this->assertInstanceOf(DnsWorkerCommand::class, $command);
            $this->commandTester = new CommandTester($command);
        }

        return $this->commandTester;
    }

    protected function onSetUp(): void
    {
        // 设置 DNS 服务测试环境
        // 不需要在 setUp 中关闭内核，会导致后续容器访问失败
    }

    public function testExecuteCanBeInstantiated(): void
    {
        $commandTester = $this->getCommandTester();

        // 验证命令可以正常实例化和配置
        $this->assertInstanceOf(CommandTester::class, $commandTester);
    }

    public function testConfigure(): void
    {
        $command = self::getContainer()->get(DnsWorkerCommand::class);
        $this->assertInstanceOf(DnsWorkerCommand::class, $command);

        // 验证命令名称
        $this->assertSame('dns:worker:start', $command->getName());

        // 验证命令描述
        $this->assertSame('Start DNS worker', $command->getDescription());

        // 验证命令选项
        $this->assertTrue($command->getDefinition()->hasOption('host'));
        $this->assertTrue($command->getDefinition()->hasOption('port'));

        // 验证选项默认值
        $this->assertSame('0.0.0.0', $command->getDefinition()->getOption('host')->getDefault());
        $this->assertSame(53, $command->getDefinition()->getOption('port')->getDefault());
    }

    public function testOptionHost(): void
    {
        $command = self::getContainer()->get(DnsWorkerCommand::class);
        $this->assertInstanceOf(DnsWorkerCommand::class, $command);
        $definition = $command->getDefinition();

        // 验证 host 选项存在
        $this->assertTrue($definition->hasOption('host'));

        // 验证选项配置
        $hostOption = $definition->getOption('host');
        $this->assertSame('host', $hostOption->getName());
        $this->assertSame('DNS server host', $hostOption->getDescription());
        $this->assertSame('0.0.0.0', $hostOption->getDefault());
        $this->assertTrue($hostOption->isValueOptional());
    }

    public function testOptionPort(): void
    {
        $command = self::getContainer()->get(DnsWorkerCommand::class);
        $this->assertInstanceOf(DnsWorkerCommand::class, $command);
        $definition = $command->getDefinition();

        // 验证 port 选项存在
        $this->assertTrue($definition->hasOption('port'));

        // 验证选项配置
        $portOption = $definition->getOption('port');
        $this->assertSame('port', $portOption->getName());
        $this->assertSame('DNS server port', $portOption->getDescription());
        $this->assertSame(53, $portOption->getDefault());
        $this->assertTrue($portOption->isValueOptional());
    }
}
