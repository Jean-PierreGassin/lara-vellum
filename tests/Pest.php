<?php

use Illuminate\Contracts\Auth\Authenticatable;
use JeanPierreGassin\LaraVellum\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function vellumUser(): Authenticatable
{
    return new class implements Authenticatable {
        public function getAuthIdentifierName(): string
        {
            return 'id';
        }

        public function getAuthIdentifier(): int
        {
            return 1;
        }

        public function getAuthPassword(): string
        {
            return '';
        }

        public function getAuthPasswordName(): string
        {
            return 'password';
        }

        public function getRememberToken(): string
        {
            return '';
        }

        public function setRememberToken($value): void {}

        public function getRememberTokenName(): string
        {
            return '';
        }
    };
}
