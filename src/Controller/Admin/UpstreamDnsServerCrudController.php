<?php

declare(strict_types=1);

namespace DnsServerBundle\Controller\Admin;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * 上游DNS服务器管理控制器
 */
class UpstreamDnsServerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UpstreamDnsServer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('上游DNS服务器')
            ->setEntityLabelInPlural('上游DNS服务器')
            ->setPageTitle('index', '上游DNS服务器列表')
            ->setPageTitle('detail', fn (UpstreamDnsServer $server) => sprintf('上游DNS服务器: %s', $server->getName()))
            ->setPageTitle('edit', fn (UpstreamDnsServer $server) => sprintf('编辑上游DNS服务器: %s', $server->getName()))
            ->setPageTitle('new', '新建上游DNS服务器')
            ->setHelp('index', '这里显示所有配置的上游DNS服务器')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'host', 'pattern', 'description']);
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本信息
        yield IdField::new('id')->setMaxLength(9999);
        yield TextField::new('name', '服务器名称');
        yield TextField::new('host', '服务器地址');
        yield IntegerField::new('port', '端口号');
        yield IntegerField::new('timeout', '超时时间(秒)');
        yield IntegerField::new('weight', '权重');
        yield TextareaField::new('description', '描述')
            ->hideOnIndex();

        // 匹配策略
        yield TextField::new('pattern', '匹配模式');
        
        yield ChoiceField::new('strategy', '匹配策略')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => MatchStrategy::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof MatchStrategy ? $value->getLabel() : '';
            });

        yield BooleanField::new('isDefault', '默认服务器');

        // DNS协议设置
        yield ChoiceField::new('protocol', 'DNS协议')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => DnsProtocolEnum::class,
            ])
            ->formatValue(function ($value) {
                return $value instanceof DnsProtocolEnum ? $value->getLabel() : '';
            });

        yield TextField::new('certPath', '证书路径')
            ->hideOnIndex()
            ->setHelp('仅在使用DOT或DOH协议时需要');
            
        yield TextField::new('keyPath', '私钥路径')
            ->hideOnIndex()
            ->setHelp('仅在使用DOT或DOH协议时需要');
            
        yield BooleanField::new('verifyCert', '验证证书')
            ->hideOnIndex();

        // 自定义应答
        yield ArrayField::new('customAnswers', '自定义应答IP列表')
            ->hideOnIndex()
            ->setHelp('格式为 JSON 数组，例如：["1.1.1.1", "8.8.8.8"]');
            
        yield IntegerField::new('ttl', 'TTL(秒)');

        // 状态标记
        yield BooleanField::new('valid', '有效');
    }

    public function configureFilters(Filters $filters): Filters
    {
        $strategyChoices = [];
        foreach (MatchStrategy::cases() as $case) {
            $strategyChoices[$case->getLabel()] = $case->value;
        }

        $protocolChoices = [];
        foreach (DnsProtocolEnum::cases() as $case) {
            $protocolChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(TextFilter::new('name', '服务器名称'))
            ->add(TextFilter::new('host', '服务器地址'))
            ->add(NumericFilter::new('port', '端口号'))
            ->add(TextFilter::new('pattern', '匹配模式'))
            ->add(ChoiceFilter::new('strategy', '匹配策略')
                ->setChoices($strategyChoices))
            ->add(BooleanFilter::new('isDefault', '默认服务器'))
            ->add(ChoiceFilter::new('protocol', 'DNS协议')
                ->setChoices($protocolChoices))
            ->add(BooleanFilter::new('valid', '有效'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters): \Doctrine\ORM\QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->select('entity')
            ->orderBy('entity.id', 'DESC');
    }

    /**
     * 格式化字节大小
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
