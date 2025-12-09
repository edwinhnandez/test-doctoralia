# Refactoring Documentation

## Overview

This document describes the refactoring of the `DoctorSlotsSynchronizer` class to improve testability, maintainability, and adherence to SOLID principles.

## Key Design Decisions

### 1. Separation of Concerns

The original `DoctorSlotsSynchronizer` class had multiple responsibilities:
- HTTP communication with vendor API
- JSON decoding
- Name normalization
- Slot parsing
- Entity persistence
- Error reporting

Each of these concerns has been extracted into separate, focused services with clear interfaces.

### 2. Dependency Injection

All dependencies are now injected via constructor, making the code:
- More testable (dependencies can be mocked)
- More flexible (implementations can be swapped)
- More maintainable (dependencies are explicit)

### 3. Interface-Based Design

All services implement interfaces, following the Dependency Inversion Principle:
- `HttpClientInterface` - HTTP operations
- `JsonDecoderInterface` - JSON decoding
- `NameNormalizerInterface` - Name normalization
- `SlotParserInterface` - Slot parsing
- `VendorApiClientInterface` - Vendor API operations
- `ErrorReporterInterface` - Error reporting
- `DoctorRepositoryInterface` - Doctor persistence
- `SlotRepositoryInterface` - Slot persistence

### 4. Single Responsibility Principle

Each class now has a single, well-defined responsibility:
- `HttpClient` - Makes HTTP requests
- `JsonDecoder` - Decodes JSON strings
- `NameNormalizer` - Normalizes doctor names
- `SlotParser` - Parses slot data into entities
- `VendorApiClient` - Encapsulates vendor API interactions
- `ErrorReporter` - Handles error reporting logic
- `DoctorRepository` - Manages doctor persistence
- `SlotRepository` - Manages slot persistence
- `DoctorSlotsSynchronizer` - Orchestrates the synchronization process

## Architecture

### Service Layer

The service layer contains business logic components:

```
src/Service/
├── HttpClientInterface.php          # HTTP client abstraction
├── HttpClient.php                   # HTTP client implementation
├── JsonDecoderInterface.php         # JSON decoder abstraction
├── JsonDecoder.php                  # JSON decoder implementation
├── NameNormalizerInterface.php      # Name normalizer abstraction
├── NameNormalizer.php               # Name normalizer implementation
├── SlotParserInterface.php         # Slot parser abstraction
├── SlotParser.php                   # Slot parser implementation
├── ErrorReporterInterface.php       # Error reporter abstraction
├── ErrorReporter.php                # Error reporter implementation
├── VendorApiClientInterface.php     # Vendor API client abstraction
├── VendorApiClient.php              # Vendor API client implementation
└── StaticVendorApiClient.php        # Static vendor API client for testing
```

### Repository Layer

The repository layer abstracts data persistence:

```
src/Repository/
├── DoctorRepositoryInterface.php    # Doctor repository abstraction
├── DoctorRepository.php             # Doctor repository implementation
├── SlotRepositoryInterface.php      # Slot repository abstraction
└── SlotRepository.php               # Slot repository implementation
```

### Entity Layer

The entity layer contains domain models:

```
src/Entity/
├── Doctor.php                       # Doctor entity
└── Slot.php                         # Slot entity
```

## Business Logic Preservation

All original business logic has been preserved:

1. **Name Normalization**: Special handling for names starting with "O'" (e.g., O'Connor)
2. **Error Handling**: Errors are marked on doctors when slot fetching fails
3. **Error Reporting**: Errors are not reported on Sundays
4. **Stale Slot Updates**: Slots older than 5 minutes have their end time updated
5. **Doctor Updates**: Existing doctors are updated with new names and error flags cleared

## Testing Strategy

### Unit Tests

Comprehensive unit tests have been written for all components:

- `NameNormalizerTest` - Tests name normalization logic
- `JsonDecoderTest` - Tests JSON decoding
- `SlotParserTest` - Tests slot parsing and stale slot handling
- `ErrorReporterTest` - Tests error reporting logic
- `VendorApiClientTest` - Tests vendor API client
- `DoctorRepositoryTest` - Tests doctor repository
- `DoctorSlotsSynchronizerTest` - Tests main synchronizer orchestration

### Test Coverage

Tests cover:
- Happy path scenarios
- Error scenarios
- Edge cases
- Business rule validation

## Usage Example

### Basic Usage

```php
use App\DoctorSlotsSynchronizer;
use App\Repository\DoctorRepository;
use App\Repository\SlotRepository;
use App\Service\HttpClient;
use App\Service\JsonDecoder;
use App\Service\NameNormalizer;
use App\Service\SlotParser;
use App\Service\VendorApiClient;
use App\Service\ErrorReporter;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$entityManager = // ... get EntityManager

$httpClient = new HttpClient();
$jsonDecoder = new JsonDecoder();
$nameNormalizer = new NameNormalizer();
$slotParser = new SlotParser();
$logger = new Logger('logger', [new StreamHandler('php://stderr')]);
$errorReporter = new ErrorReporter($logger);

$vendorApiClient = new VendorApiClient($httpClient, $jsonDecoder);
$doctorRepository = new DoctorRepository($entityManager);
$slotRepository = new SlotRepository($entityManager);

$synchronizer = new DoctorSlotsSynchronizer(
    $vendorApiClient,
    $doctorRepository,
    $slotRepository,
    $nameNormalizer,
    $slotParser,
    $errorReporter
);

$synchronizer->synchronizeDoctorSlots();
```

### Testing Usage

```php
use App\StaticDoctorSlotsSynchronizer;
// ... other imports

$synchronizer = new StaticDoctorSlotsSynchronizer(
    $doctorRepository,
    $slotRepository,
    $nameNormalizer,
    $slotParser,
    $errorReporter
);

$synchronizer->synchronizeDoctorSlots();
```

## Improvements Made

### Code Quality

1. **Type Safety**: Strict types enabled throughout
2. **Documentation**: Comprehensive PHPDoc comments
3. **Naming**: Clear, descriptive names following conventions
4. **Structure**: Logical organization of code

### Maintainability

1. **Modularity**: Each component can be modified independently
2. **Testability**: All components are easily testable
3. **Extensibility**: New features can be added without modifying existing code
4. **Readability**: Code is self-documenting with clear structure

### SOLID Principles

1. **Single Responsibility**: Each class has one reason to change
2. **Open/Closed**: Open for extension, closed for modification
3. **Liskov Substitution**: Interfaces ensure substitutability
4. **Interface Segregation**: Focused, specific interfaces
5. **Dependency Inversion**: Depend on abstractions, not concretions

## Future Improvements

Potential improvements that could be made (not implemented to keep changes minimal):

1. **Dependency Injection Container**: Use a DI container (e.g., Symfony DI, PHP-DI) for easier dependency management
2. **Event System**: Implement an event system for better decoupling
3. **Retry Logic**: Add retry logic for failed HTTP requests
4. **Caching**: Add caching for vendor API responses
5. **Batch Processing**: Process doctors in batches for better performance
6. **Transaction Management**: Better transaction handling for database operations
7. **Configuration**: Extract hardcoded values (endpoints, credentials) to configuration
8. **Logging**: More structured logging with context
9. **Metrics**: Add metrics/monitoring for synchronization operations
10. **Validation**: Add input validation for API responses

## Migration Notes

### Breaking Changes

The `DoctorSlotsSynchronizer` constructor signature has changed. Code using the old constructor will need to be updated to inject the new dependencies.

### Backward Compatibility

The `StaticDoctorSlotsSynchronizer` class has been updated to work with the new architecture while maintaining its original purpose of providing static data for testing.

## Running Tests

```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit
```

## Code Standards

- PSR-12 coding standard
- Strict types enabled
- Comprehensive type hints
- PHPDoc comments for all public methods
- Meaningful variable and method names

