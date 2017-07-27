<?php
/**
 *  This file is part of JSON:API implementation for PHP.
 *
 *  (c) Alexey Karapetov <karapetov@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */
declare(strict_types=1);

namespace JsonApiPhp\JsonApi\Document;

use JsonApiPhp\JsonApi\Test\BaseTestCase;

class ErrorTest extends BaseTestCase
{
    public function testEmptyErrorIsEmptyObject()
    {
        $this->assertEquals('{}', json_encode(new Error()));
    }

    public function testErrorWithFullSetOfProperties()
    {
        $e = (new Error())
            ->withId('test_id')
            ->withAboutLink('http://localhost')
            ->withStatus('404')
            ->withCode('OMG')
            ->withTitle('Error')
            ->withDetail('Nothing is found')
            ->withSourcePointer('/data')
            ->withSourceParameter('test_param')
            ->withMeta(new ArrayMeta(['foo' => 'bar']));

        $this->assertEqualsAsJson(
            [
                'id' => 'test_id',
                'links' => [
                    'about' => 'http://localhost',
                ],
                'status' => '404',
                'code' => 'OMG',
                'title' => 'Error',
                'detail' => 'Nothing is found',
                'source' => [
                    'pointer' => '/data',
                    'parameter' => 'test_param',
                ],
                'meta' => [
                    'foo' => 'bar',
                ],
            ],
            $e
        );
    }
}
