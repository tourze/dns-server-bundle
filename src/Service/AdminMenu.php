<?php

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * DNS服务器管理菜单服务
 */
#[AutoconfigureTag(name: 'easy_admin.menu_provider')]
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('DNS服务器') === null) {
            $item->addChild('DNS服务器');
        }

        $dnsMenu = $item->getChild('DNS服务器');

        // 上游DNS服务器管理菜单
        $dnsMenu->addChild('上游DNS服务器')
            ->setUri($this->linkGenerator->getCurdListPage(UpstreamDnsServer::class))
            ->setAttribute('icon', 'fas fa-server');

        // DNS查询日志菜单
        $dnsMenu->addChild('DNS查询日志')
            ->setUri($this->linkGenerator->getCurdListPage(DnsQueryLog::class))
            ->setAttribute('icon', 'fas fa-history');
    }
}
