<?php
/**
 * This file is part of Railgun package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Railgun\Tests\Http;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Exception;
use Serafim\Railgun\Http\Request;
use Serafim\Railgun\Http\NativeRequest;
use Serafim\Railgun\Http\SymfonyRequest;
use Serafim\Railgun\Http\IlluminateRequest;
use Serafim\Railgun\Tests\AbstractTestCase;
use Illuminate\Http\Request as LaravelNativeRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyNativeRequest;

/**
 * Class RequestsFactoryTestCase
 * @package Serafim\Railgun\Tests\Http
 */
class RequestsFactoryTestCase extends AbstractTestCase
{
    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function testNativeRequestResolved(): void
    {
        Assert::assertInstanceOf(NativeRequest::class, Request::create());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function testLaravelRequestResolved(): void
    {
        $laravel = Request::create(LaravelNativeRequest::createFromGlobals());

        Assert::assertInstanceOf(IlluminateRequest::class, $laravel);
    }

    /**
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testSymfonyRequestResolved(): void
    {
        $symfony = Request::create(SymfonyNativeRequest::createFromGlobals());

        Assert::assertInstanceOf(SymfonyRequest::class, $symfony);
    }
}