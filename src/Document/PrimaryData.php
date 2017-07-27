<?php
declare(strict_types=1);
/*
 *
 * This file is part of JSON:API implementation for PHP.
 *
 * (c) Alexey Karapetov <karapetov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace JsonApiPhp\JsonApi\Document;

use JsonApiPhp\JsonApi\Document\Resource\ResourceInterface;

class PrimaryData implements PrimaryDataInterface
{
    private $resource;

    public function __construct(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    public function identifies(ResourceInterface $another_resource): bool
    {
        return $this->resource->identifies($another_resource);
    }

    function jsonSerialize()
    {
        return $this->resource->jsonSerialize();
    }
}