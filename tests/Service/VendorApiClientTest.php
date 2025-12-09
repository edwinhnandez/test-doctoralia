<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\HttpClientInterface;
use App\Service\JsonDecoderInterface;
use App\Service\VendorApiClient;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for VendorApiClient service.
 */
final class VendorApiClientTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private JsonDecoderInterface $jsonDecoder;
    private VendorApiClient $client;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->jsonDecoder = $this->createMock(JsonDecoderInterface::class);
        $this->client = new VendorApiClient($this->httpClient, $this->jsonDecoder);
    }

    public function testGetDoctorsReturnsDecodedData(): void
    {
        $expectedDoctors = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Jane Smith'],
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('fetch')
            ->with(
                'http://localhost:2137/api/doctors',
                'docplanner',
                'docplanner'
            )
            ->willReturn(json_encode($expectedDoctors));

        $this->jsonDecoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn($expectedDoctors);

        $result = $this->client->getDoctors();

        $this->assertSame($expectedDoctors, $result);
    }

    public function testGetDoctorSlotsReturnsDecodedData(): void
    {
        $doctorId = 1;
        $expectedSlots = [
            ['start' => '2024-01-01 10:00:00', 'end' => '2024-01-01 10:30:00'],
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('fetch')
            ->with(
                'http://localhost:2137/api/doctors/1/slots',
                'docplanner',
                'docplanner'
            )
            ->willReturn(json_encode($expectedSlots));

        $this->jsonDecoder
            ->expects($this->once())
            ->method('decode')
            ->willReturn($expectedSlots);

        $result = $this->client->getDoctorSlots($doctorId);

        $this->assertSame($expectedSlots, $result);
    }

    public function testGetDoctorsThrowsExceptionOnInvalidJson(): void
    {
        $this->httpClient
            ->method('fetch')
            ->willReturn('invalid json');

        $this->jsonDecoder
            ->method('decode')
            ->willThrowException(new JsonException());

        $this->expectException(JsonException::class);
        $this->client->getDoctors();
    }
}

