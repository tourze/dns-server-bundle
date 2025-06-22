<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Command;

use DnsServerBundle\Command\DnsWorkerCommand;
use DnsServerBundle\Service\DnsWorkerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class DnsWorkerCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private MockObject $dnsWorkerService;

    protected function setUp(): void
    {
        $this->dnsWorkerService = $this->createMock(DnsWorkerService::class);
        
        // 设置React\EventLoop\Loop的静态方法get返回值
        // 使用反射操作静态属性是危险的，对于测试可以使用静态方法打桩
        // 简化起见，我们跳过Loop::get()的模拟，直接测试DnsWorkerCommand的参数传递逻辑
        
        $command = new DnsWorkerCommand($this->dnsWorkerService);
        
        $application = new Application();
        $application->add($command);
        
        $this->commandTester = new CommandTester($command);
    }
    
    public function testExecute_withDefaultOptions(): void
    {
        // 设置期望
        $this->dnsWorkerService->expects($this->once())
            ->method('start')
            ->with(
                $this->isInstanceOf(LoopInterface::class), // 简化测试，只验证参数类型
                '0.0.0.0',
                53
            );
            
        // 执行命令 - 这里会调用真实的Loop::get()
        $result = $this->commandTester->execute([]);
        
        // 验证结果
        $this->assertSame(Command::SUCCESS, $result);
    }
    
    public function testExecute_withCustomOptions(): void
    {
        // 设置期望
        $this->dnsWorkerService->expects($this->once())
            ->method('start')
            ->with(
                $this->isInstanceOf(LoopInterface::class), // 简化测试，只验证参数类型
                '127.0.0.1',
                5353
            );
        
        // 执行命令
        $result = $this->commandTester->execute([
            '--host' => '127.0.0.1',
            '--port' => 5353,
        ]);
        
        // 验证结果
        $this->assertSame(Command::SUCCESS, $result);
    }
    
    public function testConfigure(): void
    {
        $realService = $this->createMock(DnsWorkerService::class);
        $command = new DnsWorkerCommand($realService);
        
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
} 