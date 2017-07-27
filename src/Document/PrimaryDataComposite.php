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

use JsonApiPhp\JsonApi\Document\Resource\ResourceIdentifier;
use JsonApiPhp\JsonApi\Document\Resource\ResourceInterface;

class PrimaryDataComposite implements PrimaryDataInterface
{
    private $resources;

    public function __construct(ResourceIdentifier ...$resources)
    {
        $this->resources = $resources;
    }

    public function identifies(ResourceInterface $another_resource): bool
    {
        foreach ($this->resources as $resource) {
            if ($resource->identifies($another_resource)) {
                return true;
            }
        }
        return false;
    }

    function jsonSerialize()
    {
        return $this->resources;
    }
}