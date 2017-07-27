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

namespace JsonApiPhp\JsonApi;

use JsonApiPhp\JsonApi\Document\Error;
use JsonApiPhp\JsonApi\Document\LinksTrait;
use JsonApiPhp\JsonApi\Document\Meta;
use JsonApiPhp\JsonApi\Document\Resource\ResourceInterface;
use JsonApiPhp\JsonApi\Document\Resource\ResourceObject;

class Document implements \JsonSerializable
{
    const MEDIA_TYPE = 'application/vnd.api+json';
    const DEFAULT_API_VERSION = '1.0';

    use LinksTrait;

    private $api;
    private $data;
    private $errors;
    private $included;
    private $isSparse = false;
    private $meta;

    private function __construct()
    {
    }

    public static function fromMeta(Meta $meta): self
    {
        $doc = (new self)
            ->withMeta($meta);
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
        $doc = new self;
        $doc->data = $data;
        return $doc;
    }

    public static function fromResources(ResourceInterface ...$data): self
    {
        $doc = new self;
        $doc->data = $data;
        return $doc;
    }

    public function withApiVersion(string $version = self::DEFAULT_API_VERSION): self
    {
        $clone = clone $this;
        $clone->api['version'] = $version;
        return $clone;
    }

    public function withApiMeta(array $meta): self
    {
        $clone = clone $this;
        $clone->api['meta'] = $meta;
        return $clone;
    }

    public function withIncluded(ResourceObject ...$included): self
    {
        $clone = clone $this;
        $clone->included = $included;
        return $clone;
    }

    public function sparse(): self
    {
        $clone = clone $this;
        $clone->isSparse = true;
        return $clone;
    }

    public function withMeta(Meta $meta): self
    {
        $clone = clone $this;
        $clone->meta = $meta;
        return $clone;
    }

    public function withLink(string $name, string $url): self
    {
        $clone = clone $this;
        $clone->links[$name] = $url;
        return $clone;
    }

    public function withLinkObject(string $name, string $href, Meta $meta = null): self
    {
        $link = [
            'href' => $href,
        ];
        if ($meta) {
            $link['meta'] = $meta;
        }
        $clone = clone $this;
        $clone->links[$name] = $link;
        return $clone;
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
        if ($this->isSparse || empty($this->included)) {
            return;
        }
        foreach ($this->included as $included) {
            if ($this->hasLinkTo($included) || $this->anotherIncludedResourceIdentifies($included)) {
                continue;
            }
            throw new \LogicException("Full linkage is required for $included");
        }
    }

    private function anotherIncludedResourceIdentifies(ResourceObject $resource): bool
    {
        /** @var ResourceObject $included */
        foreach ($this->included as $included) {
            if ($included !== $resource && $included->identifies($resource)) {
                return true;
            }
        }
        return false;
    }

    private function hasLinkTo(ResourceObject $resource): bool
    {
        /** @var ResourceInterface $existingResource */
        foreach ($this->toResources() as $existingResource) {
            if ($existingResource->identifies($resource)) {
                return true;
            }
        }
        return false;
    }

    private function toResources(): \Iterator
    {
        if ($this->data instanceof ResourceInterface) {
            yield $this->data;
        } elseif (is_array($this->data)) {
            foreach ($this->data as $datum) {
                yield $datum;
            }
        }
    }
}
