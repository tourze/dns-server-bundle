<?php

namespace DnsServerBundle\Controller;

use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsResolver;
use React\Dns\Model\Record;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * DNS HTTP查询接口
 * 提供DoH(DNS over HTTPS)服务，支持自定义DNS解析规则
 */
class DnsController extends AbstractController
{
    public function __construct(
        private readonly UpstreamDnsServerRepository $upstreamDnsServerRepository,
        private readonly DnsResolver $dnsResolver,
    ) {
    }

    #[Route(path: '/dns-query', methods: ['GET'])]
    public function dnsQuery(Request $request): Response
    {
        $name = strtolower($request->query->get('name'));
        if (!$name) {
            return $this->json(['Status' => 3]); // NXDOMAIN
        }

        // 查找匹配的上游服务器
        $upstreamServer = $this->upstreamDnsServerRepository->findMatchingServer($name);
        if (!$upstreamServer) {
            $upstreamServer = $this->upstreamDnsServerRepository->getDefaultServer();
        }

        try {
            // 根据配置选择查询方式
            if ($upstreamServer->getCustomAnswers()) {
                $response = $this->dnsResolver->createCustomResponse(
                    $name,
                    $upstreamServer->getCustomAnswers(),
                    $upstreamServer->getTtl()
                );
            } else {
                $response = $this->dnsResolver->query($name, $upstreamServer);
            }

            return $this->json([
                'Status' => $response->rcode,
                'TC' => $response->tc,
                'RD' => $response->rd,
                'RA' => $response->ra,
                'Question' => array_map(
                    fn(Record $r) => [
                        'name' => $r->name,
                        'type' => $r->type,
                        'class' => $r->class,
                    ],
                    $response->questions
                ),
                'Answer' => array_map(
                    fn(Record $r) => [
                        'name' => $r->name,
                        'type' => $r->type,
                        'TTL' => $r->ttl,
                        'data' => $r->data,
                    ],
                    $response->answers
                ),
            ]);
        } catch (\Throwable $e) {
            return $this->json(['Status' => 2]); // SERVFAIL
        }
    }
}
