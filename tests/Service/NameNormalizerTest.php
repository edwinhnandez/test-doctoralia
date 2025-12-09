<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\NameNormalizer;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for NameNormalizer service.
 */
final class NameNormalizerTest extends TestCase
{
    private NameNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new NameNormalizer();
    }

    public function testNormalizeStandardName(): void
    {
        $result = $this->normalizer->normalize('john doe');
        $this->assertSame('John Doe', $result);
    }

    public function testNormalizeAlreadyCapitalized(): void
    {
        $result = $this->normalizer->normalize('John Doe');
        $this->assertSame('John Doe', $result);
    }

    public function testNormalizeNameWithOapostrophe(): void
    {
        $result = $this->normalizer->normalize("mary o'connor");
        $this->assertSame("Mary O'Connor", $result);
    }

    public function testNormalizeNameWithOapostropheAlreadyCapitalized(): void
    {
        $result = $this->normalizer->normalize("Mary O'Connor");
        $this->assertSame("Mary O'Connor", $result);
    }

    public function testNormalizeNameWithOBrien(): void
    {
        $result = $this->normalizer->normalize("patrick o'brien");
        $this->assertSame("Patrick O'Brien", $result);
    }

    public function testNormalizeNameWithLowercaseOapostrophe(): void
    {
        $result = $this->normalizer->normalize("sarah o'malley");
        $this->assertSame("Sarah O'Malley", $result);
    }

    public function testNormalizeNameWithoutOapostrophe(): void
    {
        $result = $this->normalizer->normalize('jane smith');
        $this->assertSame('Jane Smith', $result);
    }

    public function testNormalizeSingleWordName(): void
    {
        $result = $this->normalizer->normalize('madonna');
        $this->assertSame('Madonna', $result);
    }
}

