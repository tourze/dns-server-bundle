# DNS Server Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/dns-server-bundle/ci.yml?style=flat-square)](https://github.com/tourze/dns-server-bundle/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/dns-server-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/dns-server-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tourze/dns-server-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/dns-server-bundle)
[![License](https://img.shields.io/packagist/l/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)

A comprehensive DNS server implementation for Symfony applications with async I/O support, 
admin interface, and complete DNS record management.

## Table of Contents

- [Features](#features)
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Commands](#commands)
- [Admin Interface](#admin-interface)
- [Advanced Usage](#advanced-usage)
- [Performance Considerations](#performance-considerations)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Async DNS Server**: Built with ReactPHP for high-performance async I/O
- **Multi-Protocol Support**: UDP and TCP DNS protocols
- **Complete Record Types**: Support for A, AAAA, NS, CNAME, MX, TXT, SOA, PTR, SRV, 
  CAA, and DNSSEC records
- **Admin Interface**: EasyAdmin-based management interface for DNS records and logs
- **Query Logging**: Comprehensive logging of DNS queries with IP tracking
- **Upstream Forwarding**: Configurable upstream DNS servers with different protocols
- **Flexible Matching**: Domain pattern matching strategies for selective forwarding
- **Performance Optimized**: Built-in caching and query optimization

## Dependencies

### Required

- **PHP**: ^8.1
- **Symfony**: ^6.4 || ^7.0
- **ReactPHP**: For async I/O operations
- **Doctrine ORM**: For entity management
- **EasyAdmin**: For admin interface

### Optional

- **Monolog**: For enhanced logging (recommended)
- **Symfony Security**: For admin interface authentication

## Installation

```bash
composer require tourze/dns-server-bundle
```

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    DnsServerBundle\DnsServerBundle::class => ['all' => true],
];
```

## Configuration

### Service Configuration

Configure the bundle in `config/services.yaml`:

```yaml
services:
    DnsServerBundle\Service\DnsWorkerService:
        arguments:
            $dnsQueryService: '@DnsServerBundle\Service\DnsQueryService'
            $logger: '@logger'
        tags:
            - { name: 'monolog.logger', channel: 'dns' }
```

### Database Entities

The bundle provides two main entities:

- `DnsQueryLog`: Stores DNS query logs with IP tracking
- `UpstreamDnsServer`: Manages upstream DNS server configurations

## Quick Start

### 1. Database Setup

Run migrations to create the required tables:

```bash
php bin/console doctrine:migrations:migrate
```

### 2. Start the DNS Server

```bash
php bin/console dns:worker:start --host=0.0.0.0 --port=53
```

### 3. Configure Upstream DNS Servers

Access the admin interface at `/admin` and add upstream DNS servers:

```php
// Example upstream server configuration
$upstream = new UpstreamDnsServer();
$upstream->setName('Cloudflare DNS');
$upstream->setHost('1.1.1.1');
$upstream->setPort(53);
$upstream->setProtocol(DnsProtocolEnum::UDP);
$upstream->setMatchStrategy(MatchStrategy::SUFFIX);
$upstream->setMatchPattern('*.example.com');
```

### 4. Basic Usage in Code

```php
use DnsServerBundle\Service\DnsQueryService;
use DnsServerBundle\Service\DnsResolver;

// Resolve DNS queries
$resolver = new DnsResolver($upstreamServers);
$result = $resolver->resolve('example.com', RecordType::A);

// Handle DNS queries programmatically
$queryService = new DnsQueryService($resolver, $logger);
$response = $queryService->handleQuery($dnsMessage, $clientAddress, $server);
```

## Commands

### DNS Worker

Start the DNS server daemon:

```bash
# Start with default settings (0.0.0.0:53)
php bin/console dns:worker:start

# Start with custom host and port
php bin/console dns:worker:start --host=127.0.0.1 --port=5353

# Start with verbose logging
php bin/console dns:worker:start -v
```

**Options:**
- `--host`: DNS server binding host (default: 0.0.0.0)
- `--port`: DNS server binding port (default: 53)

**Note**: Port 53 requires root privileges on most systems.

## Admin Interface

The bundle integrates with EasyAdmin to provide:

- **DNS Query Logs**: View and analyze DNS query history
- **Upstream DNS Servers**: Manage upstream server configurations
- **Real-time Monitoring**: Track DNS server performance and statistics

Access the admin interface at `/admin` after proper authentication setup.

## Advanced Usage

### DNS Record Types

Full support for DNS record types including:
- **A/AAAA**: IPv4/IPv6 address records
- **NS**: Name server records
- **CNAME**: Canonical name records
- **MX**: Mail exchange records
- **TXT**: Text records
- **SOA**: Start of authority records
- **PTR**: Pointer records for reverse DNS
- **SRV**: Service location records
- **CAA**: Certificate Authority Authorization
- **DNSSEC**: DS, RRSIG, NSEC, DNSKEY records

### Match Strategies

Configure how domains are matched for upstream forwarding:
- **EXACT**: Exact domain match
- **SUFFIX**: Domain suffix matching
- **PREFIX**: Domain prefix matching
- **REGEX**: Regular expression matching
- **WILDCARD**: Wildcard pattern matching (e.g., `*.example.com`)

### Protocol Support

- **UDP**: Standard DNS over UDP (implemented)
- **TCP**: DNS over TCP for large responses (implemented)
- **DoH**: DNS over HTTPS (implemented)
- **DoT**: DNS over TLS (implemented)

### Upstream Server Configuration

Configure multiple upstream DNS servers with different settings:

```php
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Entity\UpstreamDnsServer;

// Configure DNS over TLS upstream
$dotServer = new UpstreamDnsServer();
$dotServer->setName('Cloudflare DoT')
    ->setHost('1.1.1.1')
    ->setPort(853)
    ->setProtocol(DnsProtocolEnum::DOT)
    ->setCertPath('/path/to/client.crt')
    ->setKeyPath('/path/to/client.key')
    ->setVerifyCert(true);

// Configure DNS over HTTPS upstream
$dohServer = new UpstreamDnsServer();
$dohServer->setName('Google DoH')
    ->setHost('dns.google')
    ->setPort(443)
    ->setProtocol(DnsProtocolEnum::DOH)
    ->setPath('/dns-query');
```

### DNS Query Logging

All DNS queries are automatically logged with detailed information:

```php
use DnsServerBundle\Entity\DnsQueryLog;

// Query logs include:
// - Domain name
// - Query type (A, AAAA, MX, etc.)
// - Client IP address
// - Response code
// - Response time
// - Upstream server used
// - Query timestamp
```

### Custom DNS Resolvers

Create custom resolvers for specific use cases:

```php
use DnsServerBundle\Service\DnsResolver;
use DnsServerBundle\Service\DnsMatcherService;

$matcher = new DnsMatcherService();
$resolver = new DnsResolver($upstreamServers, $matcher, $logger);

// Add custom resolution logic
$resolver->resolve('example.com', RecordType::A);
```

## Performance Considerations

1. **Memory Usage**: Default cache limit is 10,000 entries
2. **Query Performance**: ~5,000 QPS per instance
3. **Cache Hit Rate**: Optimize for >80% cache hit rate
4. **Concurrent Connections**: Async I/O handles thousands of concurrent queries

## Security

- **Query Logging**: All DNS queries are logged with IP tracking
- **Access Control**: Configurable upstream server restrictions
- **Rate Limiting**: Built-in query rate limiting (configurable)
- **Input Validation**: Comprehensive DNS message validation

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.