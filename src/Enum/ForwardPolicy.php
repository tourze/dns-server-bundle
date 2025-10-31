<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 转发策略枚举
 *
 * 定义了 DNS 服务器处理查询请求时的转发行为策略。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc2308 DNS查询转发
 * @see https://datatracker.ietf.org/doc/html/rfc5625 DNS代理实现指南
 */
enum ForwardPolicy: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 从不转发
     * 只使用本地解析，不会将查询转发给其他DNS服务器
     * 适用于权威DNS服务器或只需要本地解析的场景
     *
     * @see https://datatracker.ietf.org/doc/html/rfc2308#section-2.1
     */
    case NEVER = 'never';

    /**
     * 先查本地，找不到再转发
     * 优先查询本地记录，如果未找到匹配记录则转发到上游DNS服务器
     * 适用于混合模式DNS服务器，既有本地解析又需要外部解析的场景
     */
    case FIRST = 'first';

    /**
     * 只转发，不查本地
     * 所有查询都直接转发到上游DNS服务器，不查询本地记录
     * 适用于纯转发DNS服务器或DNS代理服务器
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5625#section-2
     */
    case ONLY = 'only';

    /**
     * 条件转发
     * 根据域名或记录类型决定是否转发查询
     * 适用于需要针对特定域名或记录类型使用不同策略的场景
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5625#section-3
     */
    case CONDITIONAL = 'conditional';

    /**
     * 获取转发策略的描述
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::NEVER => '从不转发',
            self::FIRST => '先查本地，找不到再转发',
            self::ONLY => '只转发，不查本地',
            self::CONDITIONAL => '条件转发（基于域名或记录类型）',
        };
    }

    /**
     * 判断是否需要查询本地记录
     */
    public function shouldQueryLocal(): bool
    {
        return in_array($this, [self::NEVER, self::FIRST, self::CONDITIONAL], true);
    }

    /**
     * 判断是否需要转发查询
     */
    public function shouldForward(): bool
    {
        return in_array($this, [self::FIRST, self::ONLY, self::CONDITIONAL], true);
    }
}
