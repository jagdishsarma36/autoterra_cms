<?php

namespace Database\Seeders;

use App\Models\PageCms;
use App\Models\PageContent;
use App\Models\Post;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    public function run(): void
    {
        // Sample pages
        $aboutPage = PageCms::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'About AutoTerra',
            'excerpt' => 'Learn about AutoTerra and Infyterra Technologies.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        // Add content blocks for about page
        $this->block('cms:about-us', 'hero.heading', 'About AutoTerra', 'richtext');
        $this->block('cms:about-us', 'hero.description', 'Professional LiDAR, survey, and terrain software by Infyterra Technologies. 25+ years of engineering software, trusted in 20+ countries since 1998.', 'richtext');
        $this->block('cms:about-us', 'section.eyebrow', 'Our Story', 'text');
        $this->block('cms:about-us', 'section.heading', 'Engineering software built by engineers', 'richtext');
        $this->block('cms:about-us', 'section.description', 'Infyterra Technologies was founded by engineers who had experienced the pain of slow, expensive, and inflexible spatial software first-hand. We set out to build tools that respect the way surveyors and engineers actually work.', 'richtext');
        $this->block('cms:about-us', 'stats', json_encode([
            ['number' => '25+', 'label' => 'years of engineering software'],
            ['number' => '20+', 'label' => 'countries deployed'],
            ['number' => '1,000+', 'label' => 'engineering firms'],
            ['number' => '7', 'label' => 'product editions'],
        ]), 'json');
        $this->block('cms:about-us', 'features', json_encode([
            ['title' => 'Engineering accuracy first', 'description' => 'Every algorithm reviewed by practising civil engineers.'],
            ['title' => 'Built for the field', 'description' => 'Designed around real workflows surveyors actually use.'],
            ['title' => 'Global reach, local support', 'description' => '20+ countries with localised support and regional pricing.'],
        ]), 'json');
        $this->block('cms:about-us', 'cta.heading', 'Ready to see AutoTerra in action?', 'richtext');
        $this->block('cms:about-us', 'cta.description', 'Talk to our team about your workflow.', 'richtext');
        $this->block('cms:about-us', 'cta.button_primary_text', 'Request a demo', 'text');
        $this->block('cms:about-us', 'cta.button_primary_url', '/contact', 'text');

        // Pricing page
        $pricingPage = PageCms::updateOrCreate(['slug' => 'pricing-dynamic'], [
            'title' => 'Pricing Overview',
            'excerpt' => 'AutoTerra pricing — from free viewer to enterprise.',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->block('cms:pricing-dynamic', 'hero.heading', 'The right edition for every team', 'richtext');
        $this->block('cms:pricing-dynamic', 'hero.description', 'Professional-grade LiDAR and survey tools accessible to every engineering team.', 'richtext');
        $this->block('cms:pricing-dynamic', 'faq', json_encode([
            ['question' => 'Why aren\'t prices shown?', 'answer' => 'AutoTerra is sold in 20+ countries with purchasing power parity applied.'],
            ['question' => 'Is there a free trial?', 'answer' => 'Yes — 30-day trial of Pro Spatial, no credit card required.'],
            ['question' => 'Can I upgrade later?', 'answer' => 'Absolutely. Pay only the difference between editions.'],
        ]), 'json');
        $this->block('cms:pricing-dynamic', 'cta.heading', 'Need help choosing?', 'richtext');
        $this->block('cms:pricing-dynamic', 'cta.button_primary_text', 'Talk to sales', 'text');
        $this->block('cms:pricing-dynamic', 'cta.button_primary_url', '/contact', 'text');

        // Sample blog posts
        Post::updateOrCreate(['slug' => 'introducing-autoterra-pro-spatial'], [
            'title' => 'Introducing AutoTerra Pro Spatial',
            'content' => '<h2>The Complete Platform</h2><p>We\'re excited to announce AutoTerra Pro Spatial — the most comprehensive geospatial engineering platform we\'ve ever built.</p><h2>Key Features</h2><ul><li>GPU-accelerated point cloud processing</li><li>AI classification engine trained on 50M+ labeled points</li><li>Complete survey and CAD toolkit</li><li>Corridor and road survey module</li></ul>',
            'excerpt' => 'The most comprehensive geospatial engineering platform — survey + LiDAR + terrain + roads.',
            'author_name' => 'AutoTerra Team',
            'category' => 'Product Updates',
            'tags' => ['Pro Spatial', 'LiDAR', 'Product Launch'],
            'is_published' => true,
            'published_at' => now()->subDays(5),
        ]);

        Post::updateOrCreate(['slug' => 'lidar-classification-best-practices'], [
            'title' => 'LiDAR Classification Best Practices',
            'content' => '<h2>Getting Accurate Results</h2><p>AI-powered point cloud classification has transformed how we process LiDAR data.</p><h2>Training Data Quality</h2><p>The classification engine works best when you provide high-quality training data. Even 500 well-labeled points can significantly improve accuracy.</p>',
            'excerpt' => 'Tips and techniques for getting the most accurate classification results.',
            'author_name' => 'LiDAR Team',
            'category' => 'Tutorials',
            'tags' => ['LiDAR', 'Classification'],
            'is_published' => true,
            'published_at' => now()->subDays(12),
        ]);

        Post::updateOrCreate(['slug' => 'highway-corridor-survey-workflow'], [
            'title' => 'Highway Corridor Survey Workflow',
            'content' => '<h2>From Mobile LiDAR to Deliverable</h2><p>Highway corridor surveys using mobile LiDAR have become the standard for road agencies worldwide.</p><h2>Step 1: Data Import</h2><p>Import mobile LiDAR data directly — LAS, LAZ, E57 supported.</p><h2>Step 2: Classification</h2><p>Use AI to automatically separate ground, vegetation, buildings, vehicles.</p>',
            'excerpt' => 'How to streamline your highway corridor survey workflow.',
            'author_name' => 'Solutions Team',
            'category' => 'Case Studies',
            'tags' => ['Highways', 'Mobile LiDAR'],
            'is_published' => true,
            'published_at' => now()->subDays(20),
        ]);
    }

    protected function block(string $page, string $key, string $value, string $type = 'text'): void
    {
        PageContent::updateOrCreate(
            ['page' => $page, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
