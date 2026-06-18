<?php

use App\Models\PageContent;

if (!function_exists('pageContent')) {
    /**
     * Get page content by page and key.
     */
    function pageContent(string $page, string $key, $default = ''): string
    {
        $value = PageContent::get($page, $key, $default);
        return is_string($value) ? $value : (string) $value;
    }
}

if (!function_exists('pageContentRaw')) {
    /**
     * Get raw page content (could be string, array, etc).
     */
    function pageContentRaw(string $page, string $key, $default = '')
    {
        return PageContent::get($page, $key, $default);
    }
}

if (!function_exists('pageContentJson')) {
    /**
     * Get JSON-decoded page content.
     */
    function pageContentJson(string $page, string $key, array $default = []): array
    {
        return PageContent::getJson($page, $key, $default);
    }
}

if (!function_exists('formatINR')) {
    /**
     * Format amount in Indian Rupees from paise.
     */
    function formatINR(int $paise): string
    {
        return '₹' . number_format($paise / 100, 0, '.', ',');
    }
}

if (!function_exists('formatUSD')) {
    /**
     * Format amount in USD from cents.
     */
    function formatUSD(int $cents): string
    {
        return '$' . number_format($cents / 100, 2, '.', ',');
    }
}

if (!function_exists('termMonths')) {
    /**
     * Get number of months for a term.
     */
    function termMonths(string $term): int
    {
        return match($term) {
            'daily' => 0,
            'weekly' => 0,
            '3mo' => 3,
            '6mo' => 6,
            '1yr' => 12,
            '3yr' => 36,
            '5yr' => 60,
            default => 12,
        };
    }
}

if (!function_exists('termLabel')) {
    /**
     * Get human-readable label for a term.
     */
    function termLabel(string $term): string
    {
        return match($term) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            '3mo' => '3-Month',
            '6mo' => '6-Month',
            '1yr' => '1-Year',
            '3yr' => '3-Year',
            '5yr' => '5-Year',
            default => $term,
        };
    }
}

if (!function_exists('toPaise')) {
    /**
     * Convert regular amount to paise/cents for Razorpay API.
     */
    function toPaise(float $amount): int
    {
        return (int) round($amount * 100);
    }
}

if (!function_exists('termDays')) {
    /**
     * Get number of days for a term.
     */
    function termDays(string $term): int
    {
        return match($term) {
            'daily' => 1,
            'weekly' => 7,
            '3mo' => 90,
            '6mo' => 180,
            '1yr' => 365,
            '3yr' => 1095,
            '5yr' => 1825,
            default => 365,
        };
    }
}

if (!function_exists('cmsBlock')) {
    /**
     * Get a CMS page content block by slug and key.
     */
    function cmsBlock(string $slug, string $key, $default = '')
    {
        return pageContent('cms:' . $slug, $key, $default);
    }
}

if (!function_exists('cmsBlockJson')) {
    /**
     * Get a JSON-decoded CMS page content block.
     */
    function cmsBlockJson(string $slug, string $key, array $default = []): array
    {
        return pageContentJson('cms:' . $slug, $key, $default);
    }
}

if (!function_exists('renderForm')) {
    /**
     * Render a form by slug. Returns HTML string.
     * Usage: {!! renderForm('contact-form') !!}
     */
    function renderForm(string $slug, array $options = []): string
    {
        $form = \App\Models\FormCms::where('slug', $slug)->where('is_active', true)->first();
        if (!$form) return '';

        $fields = $form->fields()->orderBy('sort_order')->get();
        if ($fields->isEmpty()) return '';

        $action = $options['action'] ?? route('form.submit', $form->slug);

        return view('partials.form-render', compact('form', 'fields', 'action'))->render();
    }
}