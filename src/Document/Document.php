<?php
declare(strict_types=1);

/*
 * This file is part of JSON:API implementation for PHP.
 *
 * (c) Alexey Karapetov <karapetov@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JsonApiPhp\JsonApi\Document;

use JsonApiPhp\JsonApi\Document\Resource\ResourceInterface;
use JsonApiPhp\JsonApi\Document\Resource\ResourceObject;

final class Document implements \JsonSerializable
{
    const MEDIA_TYPE = 'application/vnd.api+json';
    const DEFAULT_API_VERSION = '1.0';

    use LinksTrait;
    use MetaTrait;

    /**
     * @var PrimaryDataInterface
     */
    private $data;
    private $errors;
    private $api;
    private $included;
    private $is_sparse = false;

    private function __construct()
    {
    }

    public static function fromMeta(array $meta): self
    {
        $doc = new self;
        $doc->replaceMeta($meta);
        return $doc;
    }

    public static function fromErrors(Error ...$errors): self
    {
        $doc = new self;
        $doc->errors = $errors;
        return $doc;
    }

    public static function fromResource(ResourceInterface $data): self
    {
        return self::fromPrimaryData(new PrimaryData($data));
    }

    public static function fromResources(ResourceInterface ...$data): self
    {
        return self::fromPrimaryData(new PrimaryDataComposite(...$data));
    }

    private static function fromPrimaryData(PrimaryDataInterface $data)
    {
        $doc = new self;
        $doc->data = $data;
        return $doc;
    }

    public function setApiVersion(string $version = self::DEFAULT_API_VERSION)
    {
        $this->api['version'] = $version;
    }

    public function setApiMeta(array $meta)
    {
        $this->api['meta'] = $meta;
    }

    public function setIncluded(ResourceObject ...$included)
    {
        $this->included = $included;
    }

    public function markSparse()
    {
        $this->is_sparse = true;
    }

    public function jsonSerialize()
    {
        $this->enforceFullLinkage();
        return array_filter(
            [
                'data' => $this->data,
                'errors' => $this->errors,
                'meta' => $this->meta,
                'jsonapi' => $this->api,
                'links' => $this->links,
                'included' => $this->included,
            ],
            function ($v) {
                return null !== $v;
            }
        );
    }

    private function enforceFullLinkage()
    {
        if ($this->is_sparse || empty($this->included)) {
            return;
        }
        foreach ($this->included as $included_resource) {
            if ($this->data->identifies($included_resource) || $this->anotherIncludedResourceIdentifies($included_resource)) {
                continue;
            }
            throw new \LogicException("Full linkage is required for " . json_encode($included_resource));
        }
    }

    private function anotherIncludedResourceIdentifies(ResourceObject $resource): bool
    {
        /** @var ResourceObject $included_resource */
        foreach ($this->included as $included_resource) {
            if ($included_resource !== $resource && $included_resource->identifies($resource)) {
                return true;
            }
        }
        return false;
    }
}
