<?php

declare(strict_types=1);

namespace App;

use App\Repository\DoctorRepositoryInterface;
use App\Repository\SlotRepositoryInterface;
use App\Service\ErrorReporterInterface;
use App\Service\NameNormalizerInterface;
use App\Service\SlotParserInterface;
use App\Service\StaticVendorApiClient;

/**
 * Static Doctor Slots Synchronizer
 *
 * This class extends DoctorSlotsSynchronizer and uses StaticVendorApiClient
 * instead of the real vendor API client. This is useful for testing or
 * development scenarios where you don't want to make actual HTTP requests.
 */
class StaticDoctorSlotsSynchronizer extends DoctorSlotsSynchronizer
{
    public function __construct(
        DoctorRepositoryInterface $doctorRepository,
        SlotRepositoryInterface $slotRepository,
        NameNormalizerInterface $nameNormalizer,
        SlotParserInterface $slotParser,
        ErrorReporterInterface $errorReporter,
    ) {
        parent::__construct(
            vendorApiClient: new StaticVendorApiClient(),
            doctorRepository: $doctorRepository,
            slotRepository: $slotRepository,
            nameNormalizer: $nameNormalizer,
            slotParser: $slotParser,
            errorReporter: $errorReporter,
        );
    }
}
