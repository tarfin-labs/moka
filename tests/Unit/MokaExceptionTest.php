<?php

declare(strict_types=1);

use Tarfin\Moka\Exceptions\MokaException;

it('creates MokaException with correct message and code', function (): void {
    $exception = new MokaException('Error message', 'Error code');

    expect($exception->getMessage())->toBe('Error message')
        ->and($exception->getCode())->toBe('Error code');
});

it('creates MokaException with default code when not provided', function (): void {
    $exception = new MokaException('Error message');

    expect($exception->getMessage())->toBe('Error message')
        ->and($exception->getCode())->toBe('');
});
