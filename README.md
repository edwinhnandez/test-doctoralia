# Refactoring & unit testing challenge

## It's great you're here!
We're excited to have you at this stage of the recruitment process — and we're looking forward to seeing how you think
and approach complex problems.

This task is designed not only to evaluate your ability to write clean and testable code, but more importantly 
to **understand your approach to software design, architecture, and testing at a senior level.**

## What we're looking for?
We're looking for a Senior developer who is able to work closely with business and understand the business logic and 
purpose behind it.

This exercise will give you space to demonstrate:

- Proficiency in refactoring legacy or procedural code into clean, maintainable OOP components.
- Your ability to deeply understand and translate complex business requirements into well-structured, maintainable, 
  and valuable software solutions.
- Strong understanding and application of SOLID principles.
- Use of appropriate design patterns where they add clarity and value.
Awareness and thoughtful application of modern software architecture principles, clearly separating technical 
   concerns while maintaining deep business insight.
- A pragmatic yet rigorous approach to unit testing, emphasizing the testability of business-critical components.
- Clear, developer-friendly communication—whether through your code structure, naming conventions, or thoughtful commentary
- Being up to date with PHP language development and its features.

## Task description
Please look at [`src/DoctorSlotsSynchronizer.php`](src/DoctorSlotsSynchronizer.php). 

Your goal is to:
- Clearly articulate and understand the existing business logic purely from the provided code.
- Refactor the class to make it easier to test, reason about, and maintain. Think in terms of architecture, 
modularity, and clarity.
- Write tests for the extracted/refactored business logic using your preferred PHP testing framework.
- If you see opportunities for improvements that go beyond the scope of the task, feel free to:
  - Add them, but please remember to keep your changes minimal.
  - Or simply leave comments describing your suggestions and reasoning. It's the preferred way.

We will very much appreciate comments about why you've chosen certain solutions!

### Additional Notes & Tips
- Focus on demonstrating clarity and business-driven quality over quantity.
- You decide how much time you want to spend — quality over quantity is key.
- If something could be improved but would take too long, just leave a comment or TODO.
- If anything is unclear, don’t hesitate to reach out — asking questions is a good sign.

## How to use this repository?
- To create your copy, 
  - Use the green "Use this template" button on the top right. It should be set as private repo. 
  - Do not use the Fork feature.
- Complete the task as described above.
- When done, give access to the repo to the hiring manager and other people provided.
- Send us the link to the PULL REQUEST **in your repo**, so we can review your work.

## Quick Start

### 1. Install Dependencies

```bash
composer install
```

### 2. Run Tests

```bash
vendor/bin/phpunit
```

You should see all 31 tests passing!

## Installation

### Dependencies

Install PHP dependencies using Composer:

```bash
composer install
```

### Vendor API

Only the vendor API is dockerized and configured to work with `docker-compose`. However, feel free to dockerize the rest of the project if you find it helpful.

To run the container, use:

```bash
docker-compose up -d
```

After a while, the vendor API serving doctors will be accessible on `http://localhost:2137`.

## Running Tests

Run the test suite using PHPUnit:

```bash
vendor/bin/phpunit
```

For verbose output:

```bash
vendor/bin/phpunit --verbose
```

## Refactoring Summary

The `DoctorSlotsSynchronizer` class has been refactored following SOLID principles and best practices:

### Key Improvements

1. **Separation of Concerns**: Extracted HTTP client, JSON decoder, name normalizer, slot parser, and error reporter into separate services
2. **Dependency Injection**: All dependencies are injected via constructor for better testability
3. **Interface-Based Design**: All services implement interfaces following the Dependency Inversion Principle
4. **Repository Pattern**: Abstracted data persistence behind repository interfaces
5. **Comprehensive Testing**: Unit tests written for all components

### Architecture

- **Service Layer**: Business logic components (`src/Service/`)
- **Repository Layer**: Data persistence abstractions (`src/Repository/`)
- **Entity Layer**: Domain models (`src/Entity/`)
- **Test Layer**: Unit tests (`tests/`)

### Documentation

See [REFACTORING.md](REFACTORING.md) for detailed documentation about the refactoring decisions, architecture, and design patterns used.

## Code Structure

```
src/
├── DoctorSlotsSynchronizer.php      # Main synchronizer (orchestrator)
├── StaticDoctorSlotsSynchronizer.php # Static data version for testing
├── Entity/                           # Domain entities
│   ├── Doctor.php
│   └── Slot.php
├── Repository/                       # Data persistence layer
│   ├── DoctorRepositoryInterface.php
│   ├── DoctorRepository.php
│   ├── SlotRepositoryInterface.php
│   └── SlotRepository.php
└── Service/                          # Business logic services
    ├── HttpClientInterface.php
    ├── HttpClient.php
    ├── JsonDecoderInterface.php
    ├── JsonDecoder.php
    ├── NameNormalizerInterface.php
    ├── NameNormalizer.php
    ├── SlotParserInterface.php
    ├── SlotParser.php
    ├── ErrorReporterInterface.php
    ├── ErrorReporter.php
    ├── VendorApiClientInterface.php
    ├── VendorApiClient.php
    └── StaticVendorApiClient.php

tests/
├── DoctorSlotsSynchronizerTest.php
├── Repository/
│   └── DoctorRepositoryTest.php
└── Service/
    ├── NameNormalizerTest.php
    ├── JsonDecoderTest.php
    ├── SlotParserTest.php
    ├── ErrorReporterTest.php
    └── VendorApiClientTest.php
```
