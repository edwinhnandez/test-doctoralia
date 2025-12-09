<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\JsonDecoder;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for JsonDecoder service.
 */
final class JsonDecoderTest extends TestCase
{
    private JsonDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new JsonDecoder();
    }

    public function testDecodeValidJsonString(): void
    {
        $json = '{"id": 1, "name": "John Doe"}';
        $result = $this->decoder->decode($json);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
        $this->assertSame('John Doe', $result['name']);
    }

    public function testDecodeValidJsonArray(): void
    {
        $json = '[{"id": 1}, {"id": 2}]';
        $result = $this->decoder->decode($json);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]['id']);
        $this->assertSame(2, $result[1]['id']);
    }

    public function testDecodeFalseReturnsEmptyArray(): void
    {
        $result = $this->decoder->decode(false);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testDecodeInvalidJsonThrowsException(): void
    {
        $this->expectException(JsonException::class);
        $this->decoder->decode('{invalid json}');
    }

    public function testDecodeEmptyString(): void
    {
        $result = $this->decoder->decode('');
        $this->assertNull($result);
    }
}

