# DNS 服务器包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/dns-server-bundle/ci.yml?style=flat-square)](https://github.com/tourze/dns-server-bundle/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/dns-server-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/dns-server-bundle)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tourze/dns-server-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/dns-server-bundle)
[![License](https://img.shields.io/packagist/l/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/dns-server-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/dns-server-bundle)

为 Symfony 应用程序提供的综合 DNS 服务器实现，支持异步 I/O、管理界面和完整的 DNS 记录管理。

## 目录

- [功能特性](#功能特性)
- [依赖要求](#依赖要求)
- [安装](#安装)
- [配置](#配置)
- [快速开始](#快速开始)
- [命令](#命令)
- [管理界面](#管理界面)
- [高级用法](#高级用法)
- [性能考虑](#性能考虑)
- [安全](#安全)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- **异步 DNS 服务器**: 基于 ReactPHP 构建，提供高性能异步 I/O
- **多协议支持**: 支持 UDP 和 TCP DNS 协议
- **完整记录类型**: 支持 A、AAAA、NS、CNAME、MX、TXT、SOA、PTR、SRV、
  CAA 和 DNSSEC 记录
- **管理界面**: 基于 EasyAdmin 的 DNS 记录和日志管理界面
- **查询日志**: 全面记录 DNS 查询，包含 IP 跟踪
- **上游转发**: 可配置上游 DNS 服务器，支持不同协议
- **灵活匹配**: 域名模式匹配策略，支持选择性转发
- **性能优化**: 内置缓存和查询优化

## 依赖要求

### 必需

- **PHP**: ^8.1
- **Symfony**: ^6.4 || ^7.0
- **ReactPHP**: 用于异步 I/O 操作
- **Doctrine ORM**: 用于实体管理
- **EasyAdmin**: 用于管理界面

### 可选

- **Monolog**: 用于增强日志记录（推荐）
- **Symfony Security**: 用于管理界面身份验证

## 安装

```bash
composer require tourze/dns-server-bundle
```

将 bundle 添加到你的 `config/bundles.php`：

```php
return [
    // ...
    DnsServerBundle\DnsServerBundle::class => ['all' => true],
];
```

## 配置

### 服务配置

在 `config/services.yaml` 中配置 bundle：

```yaml
services:
    DnsServerBundle\Service\DnsWorkerService:
        arguments:
            $dnsQueryService: '@DnsServerBundle\Service\DnsQueryService'
            $logger: '@logger'
        tags:
            - { name: 'monolog.logger', channel: 'dns' }
```

### 数据库实体

该 bundle 提供两个主要实体：

- `DnsQueryLog`: 存储 DNS 查询日志，包含 IP 跟踪
- `UpstreamDnsServer`: 管理上游 DNS 服务器配置

## 快速开始

### 1. 数据库设置

运行迁移来创建所需的数据表：

```bash
php bin/console doctrine:migrations:migrate
```

### 2. 启动 DNS 服务器

```bash
php bin/console dns:worker:start --host=0.0.0.0 --port=53
```

### 3. 配置上游 DNS 服务器

访问管理界面 `/admin` 并添加上游 DNS 服务器：

```php
// 上游服务器配置示例
$upstream = new UpstreamDnsServer();
$upstream->setName('Cloudflare DNS');
$upstream->setHost('1.1.1.1');
$upstream->setPort(53);
$upstream->setProtocol(DnsProtocolEnum::UDP);
$upstream->setMatchStrategy(MatchStrategy::SUFFIX);
$upstream->setMatchPattern('*.example.com');
```

### 4. 代码中的基本使用

```php
use DnsServerBundle\Service\DnsQueryService;
use DnsServerBundle\Service\DnsResolver;

// 解析 DNS 查询
$resolver = new DnsResolver($upstreamServers);
$result = $resolver->resolve('example.com', RecordType::A);

// 程序化处理 DNS 查询
$queryService = new DnsQueryService($resolver, $logger);
$response = $queryService->handleQuery($dnsMessage, $clientAddress, $server);
```

## 命令

### DNS 工作进程

启动 DNS 服务器守护进程：

```bash
# 使用默认设置启动 (0.0.0.0:53)
php bin/console dns:worker:start

# 使用自定义主机和端口启动
php bin/console dns:worker:start --host=127.0.0.1 --port=5353

# 使用详细日志启动
php bin/console dns:worker:start -v
```

**选项：**
- `--host`: DNS 服务器绑定主机 (默认: 0.0.0.0)
- `--port`: DNS 服务器绑定端口 (默认: 53)

**注意**: 端口 53 在大多数系统上需要 root 权限。

## 管理界面

该 bundle 集成了 EasyAdmin 来提供：

- **DNS 查询日志**: 查看和分析 DNS 查询历史
- **上游 DNS 服务器**: 管理上游服务器配置
- **实时监控**: 跟踪 DNS 服务器性能和统计信息

在正确设置身份验证后，访问 `/admin` 管理界面。

## 高级用法

### DNS 记录类型

完全支持 DNS 记录类型，包括：
- **A/AAAA**: IPv4/IPv6 地址记录
- **NS**: 域名服务器记录
- **CNAME**: 规范名称记录
- **MX**: 邮件交换记录
- **TXT**: 文本记录
- **SOA**: 权威记录起始
- **PTR**: 反向 DNS 指针记录
- **SRV**: 服务位置记录
- **CAA**: 证书颁发机构授权
- **DNSSEC**: DS、RRSIG、NSEC、DNSKEY 记录

### 匹配策略

配置域名匹配策略以进行上游转发：
- **EXACT**: 精确域名匹配
- **SUFFIX**: 域名后缀匹配
- **PREFIX**: 域名前缀匹配
- **REGEX**: 正则表达式匹配
- **WILDCARD**: 通配符模式匹配（例如：`*.example.com`）

### 协议支持

- **UDP**: 标准 DNS over UDP（已实现）
- **TCP**: DNS over TCP，适用于大响应（已实现）
- **DoH**: DNS over HTTPS（已实现）
- **DoT**: DNS over TLS（已实现）

### 上游服务器配置

配置多个具有不同设置的上游 DNS 服务器：

```php
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Entity\UpstreamDnsServer;

// 配置 DNS over TLS 上游
$dotServer = new UpstreamDnsServer();
$dotServer->setName('Cloudflare DoT')
    ->setHost('1.1.1.1')
    ->setPort(853)
    ->setProtocol(DnsProtocolEnum::DOT)
    ->setCertPath('/path/to/client.crt')
    ->setKeyPath('/path/to/client.key')
    ->setVerifyCert(true);

// 配置 DNS over HTTPS 上游
$dohServer = new UpstreamDnsServer();
$dohServer->setName('Google DoH')
    ->setHost('dns.google')
    ->setPort(443)
    ->setProtocol(DnsProtocolEnum::DOH)
    ->setPath('/dns-query');
```

### DNS 查询日志

所有 DNS 查询都会自动记录详细信息：

```php
use DnsServerBundle\Entity\DnsQueryLog;

// 查询日志包括：
// - 域名
// - 查询类型（A、AAAA、MX 等）
// - 客户端 IP 地址
// - 响应代码
// - 响应时间
// - 使用的上游服务器
// - 查询时间戳
```

### 自定义 DNS 解析器

为特定用例创建自定义解析器：

```php
use DnsServerBundle\Service\DnsResolver;
use DnsServerBundle\Service\DnsMatcherService;

$matcher = new DnsMatcherService();
$resolver = new DnsResolver($upstreamServers, $matcher, $logger);

// 添加自定义解析逻辑
$resolver->resolve('example.com', RecordType::A);
```

## 性能考虑

1. **内存使用**: 默认缓存限制为 10,000 条记录
2. **查询性能**: 每个实例约 5,000 QPS
3. **缓存命中率**: 优化为 >80% 缓存命中率
4. **并发连接**: 异步 I/O 处理数千个并发查询

## 安全

- **查询日志**: 所有 DNS 查询都会记录 IP 跟踪
- **访问控制**: 可配置的上游服务器限制
- **速率限制**: 内置查询速率限制（可配置）
- **输入验证**: 全面的 DNS 消息验证

## 贡献

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详情。

## 许可证

MIT 许可证 (MIT)。请查看 [License File](LICENSE) 了解更多信息。