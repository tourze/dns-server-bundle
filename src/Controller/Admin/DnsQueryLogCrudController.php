<?php

declare(strict_types=1);

namespace DnsServerBundle\Controller\Admin;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * DNS查询日志管理控制器
 */
class DnsQueryLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DnsQueryLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DNS查询日志')
            ->setEntityLabelInPlural('DNS查询日志')
            ->setPageTitle('index', 'DNS查询日志列表')
            ->setPageTitle('detail', fn (DnsQueryLog $log) => sprintf('DNS查询日志 #%s', $log->getId()))
            ->setPageTitle('edit', fn (DnsQueryLog $log) => sprintf('编辑DNS查询日志 #%s', $log->getId()))
            ->setPageTitle('new', '新建DNS查询日志')
            ->setHelp('index', '这里显示所有的DNS查询日志记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'domain', 'clientIp', 'response']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->setMaxLength(9999);
        yield TextField::new('domain', '查询域名');
        
        yield ChoiceField::new('queryType', '查询类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => RecordType::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof RecordType 
                    ? sprintf('%s (%s)', $value->getName(), $value->getDescription()) 
                    : '';
            });
        
        yield TextField::new('clientIp', '客户端IP');
        
        yield TextareaField::new('response', 'DNS响应内容')
            ->hideOnIndex();
        
        yield BooleanField::new('isHit', '命中缓存');
        
        yield IntegerField::new('responseTime', '响应时间(毫秒)');
        
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm();
    }

    public function configureFilters(Filters $filters): Filters
    {
        $queryTypeChoices = [];
        foreach (RecordType::cases() as $case) {
            $queryTypeChoices[sprintf('%s (%s)', $case->getName(), $case->getDescription())] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('domain', '查询域名'))
            ->add(ChoiceFilter::new('queryType', '查询类型')
                ->setChoices($queryTypeChoices))
            ->add(TextFilter::new('clientIp', '客户端IP'))
            ->add(BooleanFilter::new('isHit', '命中缓存'))
            ->add(NumericFilter::new('responseTime', '响应时间(毫秒)'))
            ->add(DateTimeFilter::new('createTime', '创建时间'));
    }

    public function createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters): \Doctrine\ORM\QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->select('entity')
            ->orderBy('entity.id', 'DESC');
    }
} 