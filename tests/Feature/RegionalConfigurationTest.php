<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionalConfigurationTest extends TestCase
{
    /**
     * Test that Mexican regional configuration is properly set.
     */
    public function test_mexican_locale_is_configured(): void
    {
        $this->assertEquals('es_MX', config('app.locale'));
        $this->assertEquals('es_MX', config('app.fallback_locale'));
        $this->assertEquals('America/Mexico_City', config('app.timezone'));
        $this->assertEquals('es_MX', config('app.faker_locale'));
    }

    /**
     * Test that Mexican translation files exist.
     */
    public function test_mexican_translation_files_exist(): void
    {
        $this->assertFileExists(resource_path('lang/es_MX/auth.php'));
        $this->assertFileExists(resource_path('lang/es_MX/validation.php'));
    }

    /**
     * Test that Mexican auth messages contain expected translations.
     */
    public function test_mexican_auth_messages(): void
    {
        $messages = trans('auth');
        $this->assertArrayHasKey('failed', $messages);
        $this->assertArrayHasKey('password', $messages);
        $this->assertArrayHasKey('throttle', $messages);
        
        // Verify Spanish content
        $this->assertStringContainsString('credenciales', $messages['failed']);
        $this->assertStringContainsString('contraseña', $messages['password']);
    }

    /**
     * Test that helpers are loaded.
     */
    public function test_mexican_helpers_exist(): void
    {
        $this->assertTrue(function_exists('mx_format_date'));
        $this->assertTrue(function_exists('mx_format_currency'));
        $this->assertTrue(function_exists('mx_format_number'));
    }

    /**
     * Test date formatting.
     */
    public function test_mexican_date_format(): void
    {
        $date = now();
        $formatted = mx_format_date($date);
        $this->assertMatchesRegularExpression('/^\d{2}\/\d{2}\/\d{4}$/', $formatted);
    }

    /**
     * Test currency formatting.
     */
    public function test_mexican_currency_format(): void
    {
        $amount = 1234.56;
        $formatted = mx_format_currency($amount);
        $this->assertEquals('$1,234.56', $formatted);
        
        $formatted_no_symbol = mx_format_currency($amount, false);
        $this->assertEquals('1,234.56', $formatted_no_symbol);
    }
}
