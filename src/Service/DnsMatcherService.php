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
        $pattern = str_replace(['*', '.'], ['.*', '\.'], $pattern);
        return (bool)preg_match('/^' . $pattern . '$/i', $domain);
    }

    private function matchRegex(string $domain, string $pattern): bool
    {
        return (bool)@preg_match($pattern, $domain);
    }

    private function matchPrefix(string $domain, string $pattern): bool
    {
        return str_starts_with(strtolower($domain), strtolower($pattern));
    }

    private function matchSuffix(string $domain, string $pattern): bool
    {
        return str_ends_with(strtolower($domain), strtolower($pattern));
    }
}
