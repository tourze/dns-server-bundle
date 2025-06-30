<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Controller\Admin;

use DnsServerBundle\Controller\Admin\UpstreamDnsServerCrudController;
use DnsServerBundle\Entity\UpstreamDnsServer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use PHPUnit\Framework\TestCase;

class UpstreamDnsServerCrudControllerTest extends TestCase
{
    private UpstreamDnsServerCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new UpstreamDnsServerCrudController();
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(UpstreamDnsServer::class, UpstreamDnsServerCrudController::getEntityFqcn());
    }

    public function testConfigureCrud(): void
    {
        $crud = $this->createMock(Crud::class);
        
        // 配置所有可能被调用的方法都返回 $crud 以支持链式调用
        $crud->method('setEntityLabelInSingular')->willReturn($crud);
        $crud->method('setEntityLabelInPlural')->willReturn($crud);
        $crud->method('setPageTitle')->willReturn($crud);
        $crud->method('setHelp')->willReturn($crud);
        $crud->method('setDefaultSort')->willReturn($crud);
        $crud->method('setSearchFields')->willReturn($crud);

        $result = $this->controller->configureCrud($crud);
        
        // 只需验证方法返回相同的对象
        $this->assertInstanceOf(Crud::class, $result);
    }

    public function testConfigureFields(): void
    {
        $fields = $this->controller->configureFields('index');
        
        // 将生成器转换为数组以进行测试
        $fieldsArray = iterator_to_array($fields);
        $this->assertNotEmpty($fieldsArray);
        
        $fieldTypes = [];
        foreach ($fieldsArray as $field) {
            $fieldTypes[] = get_class($field);
        }
        
        $this->assertContains(TextField::class, $fieldTypes);
        $this->assertContains(IntegerField::class, $fieldTypes);
    }
}