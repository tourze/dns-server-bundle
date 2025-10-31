<?php

namespace DnsServerBundle\Controller;

use DnsServerBundle\Exception\GeneralDnsServerException;
use DnsServerBundle\Service\DnsResolver;
use DnsServerBundle\Service\UpstreamServerMatcherService;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Query\Query;
use React\Promise\PromiseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * DNS HTTP查询接口
 * 提供DoH(DNS over HTTPS)服务，支持自定义DNS解析规则
 */
final class DnsController extends AbstractController
{
    public function __construct(
        private readonly UpstreamServerMatcherService $upstreamServerMatcherService,
        private readonly DnsResolver $dnsResolver,
    ) {
    }

    /**
     * @param PromiseInterface<Message> $promise
     */
    private function waitForPromise(PromiseInterface $promise): Message
    {
        $result = null;
        $error = null;

        $promise->then(
            function ($value) use (&$result) {
                $result = $value;
            },
            function ($reason) use (&$error) {
                $error = $reason;
            }
        );

        if (null !== $error) {
            throw $error;
        }

        if (null === $result) {
            throw new GeneralDnsServerException('Promise did not resolve');
        }

        return $result;
    }

    #[Route(path: '/dns-query', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $name = strtolower((string) $request->query->get('name'));
        if ('' === $name) {
            return $this->json(['Status' => 3]); // NXDOMAIN
        }

        // 查找匹配的上游服务器
        $upstreamServer = $this->upstreamServerMatcherService->findMatchingOrDefaultServer($name);

        try {
            // 根据配置选择查询方式
            if (null !== $upstreamServer && null !== $upstreamServer->getCustomAnswers()) {
                $response = $this->dnsResolver->createCustomResponse(
                    $name,
                    $upstreamServer->getCustomAnswers(),
                    $upstreamServer->getTtl()
                );
            } elseif (null !== $upstreamServer) {
                $promise = $this->dnsResolver->query($name, $upstreamServer);
                $response = $this->waitForPromise($promise);
            } else {
                throw new GeneralDnsServerException('No upstream DNS server found');
            }

            return $this->json([
                'Status' => $response->rcode,
                'TC' => $response->tc,
                'RD' => $response->rd,
                'RA' => $response->ra,
                'Question' => array_map(
                    static fn (Query $q) => [
                        'name' => $q->name,
                        'type' => $q->type,
                        'class' => $q->class,
                    ],
                    $response->questions
                ),
                'Answer' => array_map(
                    fn (Record $r) => [
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
