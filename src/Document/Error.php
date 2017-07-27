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

final class Error implements \JsonSerializable
{
    private $code;
    private $detail;
    private $id;
    private $links;
    private $meta;
    private $source;
    private $status;
    private $title;

    public function withId(string $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withAboutLink(string $link): self
    {
        $clone = clone $this;
        $clone->links['about'] = $link;
        return $clone;
    }

    public function withStatus(string $status): self
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withCode(string $code): self
    {
        $clone = clone $this;
        $clone->code = $code;
        return $clone;
    }

    public function withTitle(string $title): self
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    public function withDetail(string $detail): self
    {
        $clone = clone $this;
        $clone->detail = $detail;
        return $clone;
    }

    public function withSourcePointer(string $pointer): self
    {
        $clone = clone $this;
        $clone->source['pointer'] = $pointer;
        return $clone;
    }

    public function withSourceParameter(string $parameter): self
    {
        $clone = clone $this;
        $clone->source['parameter'] = $parameter;
        return $clone;
    }

    public function withMeta(Meta $meta): self
    {
        $clone = clone $this;
        $clone->meta = $meta;
        return $clone;
    }

    public function jsonSerialize()
    {
        return array_filter(
            [
                'id' => $this->id,
                'links' => $this->links,
                'status' => $this->status,
                'code' => $this->code,
                'title' => $this->title,
                'detail' => $this->detail,
                'source' => $this->source,
                'meta' => $this->meta,
            ],
            function ($v) {
                return null !== $v;
            }
        ) ?: (object) [];
    }
}
