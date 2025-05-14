<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Enum\MatchStrategy;

class DnsMatcherService
{
    public function isMatch(string $domain, string $pattern, MatchStrategy $strategy): bool
    {
        return match ($strategy) {
            MatchStrategy::EXACT => $this->matchExact($domain, $pattern),
            MatchStrategy::WILDCARD => $this->matchWildcard($domain, $pattern),
            MatchStrategy::REGEX => $this->matchRegex($domain, $pattern),
            MatchStrategy::PREFIX => $this->matchPrefix($domain, $pattern),
            MatchStrategy::SUFFIX => $this->matchSuffix($domain, $pattern),
        };
    }

    private function matchExact(string $domain, string $pattern): bool
    {
        return strtolower($domain) === strtolower($pattern);
    }

    private function matchWildcard(string $domain, string $pattern): bool
    {
        // 将通配符模式转换为正则表达式
        // 首先转义点符号
        $regexPattern = str_replace('.', '\.', $pattern);
        // 将星号转换为正则通配符
        $regexPattern = str_replace('*', '.*', $regexPattern);
        // 构建完整正则表达式
        $regexPattern = '/^' . $regexPattern . '$/i';
        
        // 使用正则匹配域名 - 安全处理无效模式
        try {
            return (bool)preg_match($regexPattern, $domain);
        } catch (\Throwable $e) {
            // 正则表达式异常情况
            return false;
        }
    }

    private function matchRegex(string $domain, string $pattern): bool
    {
        // 安全处理无效正则表达式
        try {
            return (bool)preg_match($pattern, $domain);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function matchPrefix(string $domain, string $pattern): bool
    {
        return str_starts_with(strtolower($domain), strtolower($pattern));
    }

    private function matchSuffix(string $domain, string $pattern): bool
    {
        $domainLower = strtolower($domain);
        $patternLower = strtolower($pattern);
        
        // 处理点开头的模式 (.example.com)
        if (str_starts_with($patternLower, '.')) {
            // 直接检查域名是否以模式结尾（包括点）
            return $domainLower === substr($patternLower, 1) || 
                   str_ends_with($domainLower, $patternLower);
        }
        
        // 普通后缀匹配
        return str_ends_with($domainLower, $patternLower);
    }
}
