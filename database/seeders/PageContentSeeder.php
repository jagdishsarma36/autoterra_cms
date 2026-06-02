<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGlobal();
        $this->seedHome();
        $this->seedAbout();
        $this->seedProducts();
        $this->seedPricing();
        $this->seedBuy();
        $this->seedContact();
        $this->seedQuote();
        $this->seedPro();
        $this->seedProSpatial();
        $this->seedSolutions();
        $this->seedResources();
        $this->seedBlog();
        $this->seedLogin();
        $this->seedSignup();
    }

    private function seedGlobal(): void
    {
        $this->content('global', 'site.title', 'AutoTerra', 'text');
        $this->content('global', 'site.description', 'Professional LiDAR, survey, and terrain software by Infyterra Technologies. 25+ years of engineering software, trusted in 20+ countries since 1998.', 'text');
        $this->content('global', 'footer.description', 'Professional LiDAR, survey, and terrain software by Infyterra Technologies. 25+ years of engineering software, trusted in 20+ countries since 1998.', 'text');
        $this->content('global', 'footer.copyright', '© 2026 Infyterra Technologies. All rights reserved.', 'text');
    }

    private function seedHome(): void
    {
        // Hero
        $this->content('home', 'hero.pill_text', 'Geospatial engineering platform', 'text');
        $this->content('home', 'hero.heading', 'Survey. Model. Deliver.', 'text');
        $this->content('home', 'hero.subheading', 'A complete desktop platform for field engineers and GIS professionals — from point cloud processing to contour generation, CAD drafting to highway design.', 'text');
        $this->content('home', 'hero.button_primary_text', 'Explore product family', 'text');
        $this->content('home', 'hero.button_primary_url', 'products.html', 'text');
        $this->content('home', 'hero.button_secondary_text', 'Watch 2-min overview', 'text');

        // Stats
        $this->content('home', 'stats', json_encode([
            ['number' => '25+', 'label' => 'Years of engineering software'],
            ['number' => '20+', 'label' => 'Countries worldwide'],
            ['number' => '50+', 'label' => 'File formats supported'],
            ['number' => '10+', 'label' => 'Integrated modules'],
        ]), 'json');

        // Feature 1
        $this->content('home', 'feature1.eyebrow', 'Platform capabilities', 'text');
        $this->content('home', 'feature1.heading', 'One licence. One interface. Zero software-switching.', 'text');
        $this->content('home', 'feature1.description', 'From classifying a billion-point cloud to generating finished CAD plans and engineering reports — inside a single session, without exporting to another application.', 'text');
        $this->content('home', 'feature1.features', json_encode([
            ['title' => 'LiDAR point cloud processing', 'description' => 'GPU-accelerated classification, object detection, vegetation analysis — interactive in full 3D at any dataset size.'],
            ['title' => 'Surface & terrain modelling', 'description' => 'TIN surfaces, contours, slope analysis, volumes and cross-sections — from raw survey or classified point cloud.'],
            ['title' => 'GIS-native CAD environment', 'description' => 'Full CAD drafting with live GIS layers, online maps and survey data coexisting in the same project workspace.'],
        ]), 'json');
        $this->content('home', 'feature1.link_text', 'Explore all modules', 'text');

        // Feature 2
        $this->content('home', 'feature2.eyebrow', 'Survey to deliverable', 'text');
        $this->content('home', 'feature2.heading', 'Field data to finished drawing — without leaving AutoTerra', 'text');
        $this->content('home', 'feature2.description', 'Import from total station, DGPS or legacy instruments. AutoTerra\'s code library converts field observations into production-ready CAD drawings automatically at import.', 'text');
        $this->content('home', 'feature2.checklist', json_encode([
            'Field-to-finish code-driven drafting',
            'Traverse computation & COGO',
            '50+ instrument and file formats',
            'Section, volume & corridor workflows',
            'Traditional & modern survey support',
        ]), 'json');

        // Testimonials
        $this->content('home', 'testimonials', json_encode([
            [
                'quote' => 'AutoTerra has become an integral part of every infrastructure project we handle. One platform replacing five separate tools — the productivity gain is significant.',
                'author_name' => 'Project Director',
                'author_org' => 'Infrastructure Consultancy — India',
            ],
            [
                'quote' => 'Point cloud classification accuracy is competitive with specialist platforms costing three times as much. Pro Spatial is exceptional value for serious LiDAR work.',
                'author_name' => 'LiDAR Processing Lead',
                'author_org' => 'Geospatial Survey Company — UAE',
            ],
        ]), 'json');

        // Products
        $this->content('home', 'products.eyebrow', 'Product family', 'text');
        $this->content('home', 'products.heading', 'One family. Every scale.', 'text');
        $this->content('home', 'products.description', 'From individual site surveys to billion-point aerial LiDAR and full highway corridor projects.', 'text');

        // CTA
        $this->content('home', 'cta.heading', 'Ready to see AutoTerra on your data?', 'text');
        $this->content('home', 'cta.description', 'Request a 30-minute demo with a product specialist — using your own data if you want. No generic slideshows.', 'text');
        $this->content('home', 'cta.button_primary_text', 'Buy Now', 'text');
        $this->content('home', 'cta.button_secondary_text', 'Download datasheet', 'text');
    }

    private function seedAbout(): void
    {
        // Hero
        $this->content('about', 'hero.eyebrow', 'About Infyterra Technologies', 'text');
        $this->content('about', 'hero.heading', '25+ years building software for engineers', 'text');
        $this->content('about', 'hero.description', 'AutoTerra is developed by Infyterra Technologies — the engineering software division behind Infycons. Since 1998 we\'ve been building tools that help surveyors, road engineers, and geospatial professionals work faster and deliver more accurate results.', 'text');

        // Story
        $this->content('about', 'story.eyebrow', 'Our story', 'text');
        $this->content('about', 'story.heading', 'Engineering software built by engineers who use it', 'text');
        $this->content('about', 'story.paragraphs', json_encode([
            'Infyterra Technologies was founded by engineers who had experienced the pain of slow, expensive, and inflexible spatial software first-hand. We set out to build tools that respect the way surveyors and engineers actually work — not the way software architects think they should.',
            'Today AutoTerra is deployed by survey firms, road agencies, mapping bureaux, mining companies, and national mapping organisations across 20+ countries. Our clients range from solo practitioners to teams of 200+ seats, and our software processes everything from small site surveys to billion-point aerial LiDAR datasets.',
            'We remain headquartered in Bangalore, India, with regional support offices and certified partners across the geographies we serve.',
        ]), 'json');

        // Timeline
        $this->content('about', 'timeline', json_encode([
            ['year' => '1998', 'text' => 'Infyterra Technologies founded in Bangalore. First survey data processing tool released for AutoCAD-based workflows.'],
            ['year' => '2004', 'text' => 'AutoPlotter becomes the reference tool for land surveying across India. 500+ engineering firms adopt the platform.'],
            ['year' => '2010', 'text' => 'GIS+ module released. Infycons international expansion begins — first clients in UAE, Kenya, and Southeast Asia.'],
            ['year' => '2016', 'text' => 'AutoRoads launched for complete highway geometric design. First national mapping agency deployment.'],
            ['year' => '2020', 'text' => 'AutoTerra brand launched — unified suite covering the full survey to LiDAR to terrain to CAD pipeline.'],
            ['year' => '2024', 'text' => 'AI-powered point cloud classification engine released. Pro Spatial becomes the flagship edition.'],
            ['year' => 'Today', 'text' => 'AutoTerra deployed in 20+ countries. 7 editions. AutoRoads early access programme open. UAV Photogrammetry module in development.'],
        ]), 'json');

        // Stats
        $this->content('about', 'stats', json_encode([
            ['number' => '25+', 'label' => 'years of engineering software'],
            ['number' => '20+', 'label' => 'countries deployed'],
            ['number' => '1,000+', 'label' => 'engineering firms trust us'],
            ['number' => '7', 'label' => 'product editions'],
        ]), 'json');

        // Values
        $this->content('about', 'values.eyebrow', 'Our values', 'text');
        $this->content('about', 'values.heading', 'What drives us', 'text');
        $this->content('about', 'values.description', 'Every decision we make traces back to one goal: make the engineer\'s job faster, more accurate, and less frustrating.', 'text');
        $this->content('about', 'values.cards', json_encode([
            ['title' => 'Engineering accuracy first', 'description' => 'Every algorithm, interpolation method, and output format is reviewed by practising civil engineers before release.'],
            ['title' => 'Built for the field, not just the office', 'description' => 'Our tools are designed around real workflows — the way surveyors actually work in the field and the office.'],
            ['title' => 'Global reach, local support', 'description' => 'We serve 20+ countries with localised support, regional pricing, and technical documentation in multiple languages.'],
            ['title' => 'Continuous improvement', 'description' => 'Annual maintenance plans include every new feature release — not just bug fixes. Your software gets better every year.'],
            ['title' => 'Honest, transparent licensing', 'description' => 'No hidden fees, no mandatory cloud, no annual re-licensing traps. You own your licence term.'],
            ['title' => 'Open to the future', 'description' => 'Python scripting API, REST endpoints, and plugin SDK mean AutoTerra grows with your workflow.'],
        ]), 'json');

        // Team
        $this->content('about', 'team.eyebrow', 'The team', 'text');
        $this->content('about', 'team.heading', 'The people behind AutoTerra', 'text');
        $this->content('about', 'team.description', 'A dedicated team of civil engineers, geospatial scientists, and software developers — all based in Bangalore.', 'text');
        $this->content('about', 'team.members', json_encode([
            ['name' => 'Sandipan Chakraborty', 'role' => 'Director & Founder'],
            ['name' => 'Engineering Lead', 'role' => 'Head of Engineering'],
            ['name' => 'LiDAR Lead', 'role' => 'LiDAR & Geospatial Science'],
            ['name' => 'Solutions Lead', 'role' => 'Customer Solutions'],
        ]), 'json');

        // Clients
        $this->content('about', 'clients.eyebrow', 'Trusted by', 'text');
        $this->content('about', 'clients.heading', '1,000+ engineering firms worldwide', 'text');
        $this->content('about', 'clients.description', 'Government agencies, highway consultants, survey firms, mining companies, and mapping bureaux across Asia, Africa, and the Middle East.', 'text');

        // Infycons
        $this->content('about', 'infycons.eyebrow', 'The Infycons family', 'text');
        $this->content('about', 'infycons.heading', 'Part of a 25-year engineering software legacy', 'text');
        $this->content('about', 'infycons.paragraphs', json_encode([
            'AutoTerra is the international brand built on the same codebase and engineering expertise as Infycons. Our Indian market products — AutoPlotter, Road Estimator, and AutoRoads — have been trusted by L&T, Tata, JSW, IndianOil, Reliance, and hundreds of government departments across India for over two decades.',
            'Infycons products have been trusted by L&T, Tata, JSW, IndianOil, Reliance, and hundreds of government departments across India for over two decades.',
        ]), 'json');
        $this->content('about', 'infycons.button_text', 'Visit Infycons.com', 'text');

        // Office
        $this->content('about', 'office.title', 'Infyterra Technologies — HQ', 'text');
        $this->content('about', 'office.address', 'F-2104, 1st Floor, Tower B, Ardent Office One, Hoodi, Bangalore 560048, Karnataka, India', 'text');
        $this->content('about', 'office.phone', '+91 80 66320710', 'text');
        $this->content('about', 'office.email', 'sales@infycons.com', 'text');

        // CTA
        $this->content('about', 'cta.heading', 'Ready to see AutoTerra in action?', 'text');
        $this->content('about', 'cta.description', 'Talk to our team about your workflow and we\'ll show you exactly how AutoTerra fits in.', 'text');
        $this->content('about', 'cta.button_primary_text', 'Request a demo', 'text');
        $this->content('about', 'cta.button_secondary_text', 'Explore products', 'text');
    }

    private function seedProducts(): void
    {
        $this->content('products', 'hero.eyebrow', 'Product family', 'text');
        $this->content('products', 'hero.heading', 'One platform. Every scale.', 'text');
        $this->content('products', 'hero.description', 'From individual site surveys to billion-point aerial LiDAR datasets and full highway corridor projects. Choose the edition that fits your workflow.', 'text');

        $this->content('products', 'tracks.eyebrow', 'Two tracks', 'text');
        $this->content('products', 'tracks.heading', 'Find your workflow', 'text');
        $this->content('products', 'tracks.description', 'AutoTerra is organised into two product tracks — choose based on your primary discipline. Both tracks share the same CAD engine and project format.', 'text');
        $this->content('products', 'tracks', json_encode([
            [
                'name' => 'Survey / CAD / Terrain',
                'subtitle' => 'Traditional surveying through to corridor design',
                'description' => 'For field engineers, surveyors and CAD operators who work with total station data, GPS, DTM surfaces, contours, cross-sections and volumes. Full CAD environment included.',
            ],
            [
                'name' => 'LiDAR / Point Cloud',
                'subtitle' => 'Site-scale to corridor-scale airborne & UAV data',
                'description' => 'For LiDAR operators, UAV survey teams and geospatial analysts who need GPU-accelerated point cloud classification, object detection, DSM/DEM export and large-dataset handling.',
            ],
        ]), 'json');

        $this->content('products', 'comparison.heading', 'Feature comparison', 'text');
        $this->content('products', 'comparison.description', 'All editions include the core CAD environment, DWG/DXF exchange and online map integration.', 'text');

        $this->content('products', 'faq', json_encode([
            [
                'question' => 'Can I upgrade between editions?',
                'answer' => 'Yes — upgrades are available at any time. You pay the difference between your current licence and the target edition. Your project files, settings and code libraries carry across without any migration step.',
            ],
            [
                'question' => 'What is the difference between AutoTerra Spatial and Pro Spatial?',
                'answer' => 'AutoTerra Spatial bundles Standard-level survey and terrain tools with basic LiDAR processing — suited for site-scale projects with standard point cloud capacity. Pro Spatial adds the full LiDAR suite: large-dataset tile-based processing, all 8 object detection categories, 3D animation export, and advanced classification routines.',
            ],
            [
                'question' => 'Is a perpetual licence available, or subscription only?',
                'answer' => 'Both perpetual and annual subscription licences are available. Pricing varies by region — contact the team for a quote tailored to your location.',
            ],
            [
                'question' => 'What are the system requirements?',
                'answer' => 'AutoTerra runs on Windows 10 / 11 (64-bit). Minimum 8 GB RAM; 16 GB recommended for LiDAR work. A dedicated GPU is recommended for point cloud processing and 3D terrain views — especially for Pro Spatial with large datasets.',
            ],
        ]), 'json');

        $this->content('products', 'cta.heading', 'Ready to see AutoTerra in action?', 'text');
        $this->content('products', 'cta.description', 'Request a 30-day trial of Pro Spatial — full platform, no restrictions.', 'text');
        $this->content('products', 'cta.button_primary_text', 'Buy Now', 'text');
        $this->content('products', 'cta.button_secondary_text', 'Talk to sales', 'text');
    }

    private function seedPricing(): void
    {
        $this->content('pricing', 'hero.eyebrow', 'Pricing', 'text');
        $this->content('pricing', 'hero.heading', 'The right edition for every team and budget', 'text');
        $this->content('pricing', 'hero.description', 'AutoTerra is priced to make professional-grade LiDAR and survey tools accessible — whether you\'re a solo surveyor, a mid-size consultancy, or a national mapping agency.', 'text');
        $this->content('pricing', 'hero.region_note', 'Pricing varies by country and region. Contact us for a quote tailored to your location and team size — we\'ll respond within one business day.', 'text');

        $this->content('pricing', 'track_description.survey', '4 editions in the Survey / CAD / Terrain track — from free viewing to the complete survey + LiDAR platform.', 'text');
        $this->content('pricing', 'track_description.lidar', '3 editions in the LiDAR / Point Cloud track — focused on classification, terrain, and large-scale airborne or drone-LiDAR workflows.', 'text');

        $this->content('pricing', 'faq', json_encode([
            [
                'question' => 'Why aren\'t prices shown?',
                'answer' => 'AutoTerra is sold in 20+ countries with purchasing power parity applied — the same edition genuinely costs different amounts in different markets. Showing a single price would be misleading. Contact us and we\'ll quote your exact region within one business day.',
            ],
            [
                'question' => 'What\'s included in annual maintenance?',
                'answer' => 'All new feature releases, version upgrades, and priority support are included in the annual maintenance plan. You never pay for a major version upgrade separately.',
            ],
            [
                'question' => 'What\'s a floating license?',
                'answer' => 'A floating (network) license lets any machine on your network activate the software up to the licensed seat count simultaneously. Ideal for teams where not everyone works at the same time.',
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Yes. We offer a fully featured 30-day trial of AutoTerra Pro Spatial — no credit card required. Apply via the Contact page.',
            ],
            [
                'question' => 'Do you offer academic pricing?',
                'answer' => 'Yes. Universities, research institutions, and accredited training organisations are eligible for significantly discounted academic licenses.',
            ],
            [
                'question' => 'Can I upgrade my edition later?',
                'answer' => 'Absolutely. You can upgrade to a higher edition at any time — you pay only the difference in license value, and your project files and settings migrate seamlessly.',
            ],
        ]), 'json');

        $this->content('pricing', 'enterprise.heading', 'Large team or government agency?', 'text');
        $this->content('pricing', 'enterprise.description', 'We offer volume discounts, site licenses, and dedicated account management for teams of 10+ seats, government departments, and national mapping organisations.', 'text');
        $this->content('pricing', 'enterprise.button_primary_text', 'Talk to our enterprise team', 'text');
        $this->content('pricing', 'enterprise.button_secondary_text', 'Compare all features', 'text');
    }

    private function seedBuy(): void
    {
        $this->content('buy', 'hero.heading', 'Buy <span>AutoTerra</span>', 'richtext');
        $this->content('buy', 'hero.sub', 'All plans include updates during the subscription period and standard email support. Node-locked license — one activation per seat.', 'text');
        $this->content('buy', 'geo_banner.detecting', 'Detecting your location…', 'text');
        $this->content('buy', 'tax_note', 'Prices are in Indian Rupees (₹) and exclusive of GST (18%). GST will be added at checkout. Floating network license available for Pro, Spatial, Pro Spatial.', 'text');
        $this->content('buy', 'intl.notice_heading', 'Online purchase is currently available for India only', 'text');
        $this->content('buy', 'intl.notice_text', 'Online checkout with INR pricing is available for Indian customers. For your region, our team will provide a customised quote in your local currency — typically within one business day.', 'text');
    }

    private function seedContact(): void
    {
        $this->content('contact', 'hero.heading', 'Get in touch with our team', 'text');
        $this->content('contact', 'hero.description', 'Whether you need a product demo, a free trial, or just have a question — fill in the form and we\'ll get back to you within one business day.', 'text');

        $this->content('contact', 'info.steps', json_encode([
            ['title' => 'We review your request', 'description' => 'A solutions engineer reads your message and matches it with the right product or demo track.'],
            ['title' => 'We reach out within 1 business day', 'description' => 'You\'ll receive a personal reply — not a bot — from the regional AutoTerra team.'],
            ['title' => 'Demo or trial is scheduled', 'description' => 'We\'ll arrange a live walkthrough or issue your trial license — typically within 48 hours of first contact.'],
        ]), 'json');

        $this->content('contact', 'info.email', 'support@autoterra.net', 'text');
        $this->content('contact', 'info.made_by', 'Infyterra Technologies — infycons.com', 'text');
    }

    private function seedQuote(): void
    {
        $this->content('quote', 'hero.badge_text', 'Request a Quote', 'text');
        $this->content('quote', 'hero.heading', 'Pricing tailored to your organisation', 'text');
        $this->content('quote', 'hero.description', 'Tell us about your requirements — product, team size, and preferred licence model — and our sales team will prepare a detailed quote, usually within one business day.', 'text');
        $this->content('quote', 'hero.trust_items', json_encode([
            'No commitment required',
            'Response within 24 hours',
            'Dedicated account support',
            'Volume & multi-year discounts available',
        ]), 'json');

        $this->content('quote', 'sidebar.heading', 'What happens after you submit', 'text');
        $this->content('quote', 'sidebar.steps', json_encode([
            ['title' => 'We review your requirements', 'description' => 'Our technical sales team reads every request and selects the most suitable edition and licence configuration.'],
            ['title' => 'You receive a tailored quote', 'description' => 'A formal PDF quote arrives within one business day. It includes itemised pricing, volume discounts, and available payment methods.'],
            ['title' => 'Demo or trial (optional)', 'description' => 'If you\'d like to evaluate AutoTerra first, we\'ll arrange a guided demo or a 14-day trial licence.'],
            ['title' => 'Activate & onboard', 'description' => 'Once confirmed, licence keys are issued same day and our onboarding team helps you get started.'],
        ]), 'json');

        $this->content('quote', 'sidebar.email', 'sales@autoterra.net', 'text');
        $this->content('quote', 'sidebar.india_note', 'Based in India? INR pricing with GST breakdown and direct online purchase is available on our Buy page.', 'text');
    }

    private function seedPro(): void
    {
        $this->content('pro', 'hero.badge', 'Engineering Edition', 'text');
        $this->content('pro', 'hero.heading', 'AutoTerra Pro', 'text');
        $this->content('pro', 'hero.description', 'The professional survey and terrain platform for civil engineers and land surveyors. Full DTM generation, cross-section workflows, DXF/DWG integration, and advanced terrain analysis.', 'text');
        $this->content('pro', 'hero.button_primary_text', 'Get a quote', 'text');
        $this->content('pro', 'hero.button_secondary_text', 'Request a demo', 'text');
        $this->content('pro', 'hero.button_tertiary_text', 'Compare editions', 'text');

        $this->content('pro', 'differentiators.items', json_encode([
            'DTM / DSM generation', 'Cross-sections & profiles', 'DXF / DWG read & write',
            'COGO & traverse', 'Volume calculations', '50+ CRS supported',
        ]), 'json');

        $this->content('pro', 'section1.eyebrow', 'Terrain & Surface Modeling', 'text');
        $this->content('pro', 'section1.heading', 'Survey-grade terrain models — DTM, DSM, and everything in between', 'text');
        $this->content('pro', 'section1.description', 'AutoTerra Pro takes your field survey data and generates production-ready terrain models with full control over interpolation, break-lines, and output resolution.', 'text');

        $this->content('pro', 'section2.eyebrow', 'Cross-Sections & Long Profiles', 'text');
        $this->content('pro', 'section2.heading', 'Submission-ready section drawings — generated automatically along any alignment', 'text');

        $this->content('pro', 'section3.eyebrow', 'Survey & CAD Integration', 'text');
        $this->content('pro', 'section3.heading', 'From field observations to submission-ready CAD — in one environment', 'text');

        $this->content('pro', 'capabilities.eyebrow', 'Everything included', 'text');
        $this->content('pro', 'capabilities.heading', 'The complete Pro capability set', 'text');

        $this->content('pro', 'who.eyebrow', 'Who uses AutoTerra Pro', 'text');
        $this->content('pro', 'who.heading', 'Built for the professional survey team', 'text');
        $this->content('pro', 'who.personas', json_encode([
            ['title' => 'Land surveyors', 'description' => 'Cadastral surveys, topographic mapping, traverse processing.'],
            ['title' => 'Civil engineers', 'description' => 'Road and canal cross-sections, earthwork quantities.'],
            ['title' => 'Mining survey teams', 'description' => 'Stockpile volumes, pit floor surveys, strata-wise section generation.'],
        ]), 'json');

        $this->content('pro', 'cta.heading', 'Ready to try AutoTerra Pro?', 'text');
        $this->content('pro', 'cta.description', 'Buy online or request a 30-day trial — our team will get back to you within one business day.', 'text');
        $this->content('pro', 'cta.button_primary_text', 'Buy Now', 'text');
        $this->content('pro', 'cta.button_secondary_text', 'Book a demo', 'text');
    }

    private function seedProSpatial(): void
    {
        $this->content('pro_spatial', 'hero.badge_primary', 'Most Popular', 'text');
        $this->content('pro_spatial', 'hero.badge_secondary', 'Full Suite', 'text');
        $this->content('pro_spatial', 'hero.heading', 'AutoTerra Pro Spatial', 'text');
        $this->content('pro_spatial', 'hero.description', 'The complete platform for survey-grade LiDAR processing, advanced terrain modeling, and spatial analysis. From raw point cloud to submission-ready deliverable.', 'text');
        $this->content('pro_spatial', 'hero.button_primary_text', 'Start Free Trial', 'text');
        $this->content('pro_spatial', 'hero.button_secondary_text', 'Request Demo', 'text');
        $this->content('pro_spatial', 'hero.button_tertiary_text', 'Compare editions', 'text');

        $this->content('pro_spatial', 'stats', json_encode([
            ['number' => '50+', 'label' => 'LiDAR file formats'],
            ['number' => '1 B+', 'label' => 'points processed per session'],
            ['number' => '10', 'label' => 'integrated modules'],
            ['number' => '20+', 'label' => 'countries deployed'],
            ['number' => 'GPU', 'label' => 'accelerated classification'],
        ]), 'json');

        $this->content('pro_spatial', 'modules', json_encode([
            [
                'eyebrow' => 'Point Cloud Processing',
                'heading' => 'Handle any LiDAR dataset — at any scale',
                'description' => 'AutoTerra Pro Spatial ingests raw point cloud data from airborne, mobile, terrestrial, and drone-borne sensors.',
                'chips' => ['LAS / LAZ 1.0-1.4', 'E57', 'PLY / XYZ / PTS', 'FLS / FWS', 'RCP / RCS', 'COPC streaming'],
            ],
            [
                'eyebrow' => 'AI-Powered Classification',
                'heading' => 'Deep learning classification — trained on 50M+ labeled points',
                'description' => 'The built-in classification engine uses convolutional neural networks pre-trained on a global labeled dataset.',
                'chips' => ['Ground / Low veg.', 'Buildings', 'High vegetation', 'Powerlines', 'Vehicles', 'Bridge decks', 'Water surface', 'Custom classes'],
            ],
            [
                'eyebrow' => 'Terrain & DTM Generation',
                'heading' => 'Survey-grade terrain models from classified cloud to raster',
                'description' => 'Derive bare-earth DTMs, surface models (DSM), and normalized height models (nDHM) from classified point cloud data.',
                'chips' => ['DTM / DSM / nDHM', 'TIN interpolation', 'Break-line enforcement', 'Contour generation', 'Slope / Aspect', 'Flood modeling', 'GeoTIFF export'],
            ],
            [
                'eyebrow' => 'Survey & CAD Integration',
                'heading' => 'From 3D point cloud to submission-ready CAD drawings',
                'description' => 'Pro Spatial bridges the gap between raw spatial data and engineering deliverables.',
                'chips' => ['DXF / DWG read & write', 'Cross-sections', 'Long-profiles', 'Control points', 'Stake-out', 'Field data import'],
            ],
            [
                'eyebrow' => 'Corridors & Road Survey',
                'heading' => 'Road asset inventory and alignment design from LiDAR',
                'description' => 'The corridor module automates road asset extraction from mobile or airborne LiDAR.',
                'chips' => ['Asset inventory', 'Lane marking detection', 'Sign recognition', 'Pavement analysis', 'Alignment design', 'IRI calculation'],
            ],
            [
                'eyebrow' => 'Coming Soon',
                'heading' => 'UAV Photogrammetry — from raw images to survey-grade point cloud',
                'description' => 'The UAV Photogrammetry add-on brings drone imagery processing directly into your AutoTerra workflow.',
                'chips' => ['Dense matching', 'Bundle adjustment', 'Orthoimage export', 'GCP integration', 'DSM generation', 'Accuracy reporting'],
            ],
        ]), 'json');

        $this->content('pro_spatial', 'specs.heading', 'Technical specifications', 'text');
        $this->content('pro_spatial', 'specs.description', 'Full capabilities of AutoTerra Pro Spatial.', 'text');

        $this->content('pro_spatial', 'sysreq.heading', 'System requirements', 'text');
        $this->content('pro_spatial', 'sysreq.minimum', json_encode([
            'os' => 'Windows 10 (64-bit, v21H2+)',
            'cpu' => 'Intel Core i7 / AMD Ryzen 7 (8 cores)',
            'ram' => '16 GB',
            'gpu' => 'NVIDIA GTX 1060 (6 GB VRAM)',
            'storage' => 'SSD, 50 GB free',
            'display' => '1920x1080, OpenGL 4.5',
        ]), 'json');
        $this->content('pro_spatial', 'sysreq.recommended', json_encode([
            'os' => 'Windows 11 (64-bit)',
            'cpu' => 'Intel Core i9 / AMD Ryzen 9 (12+ cores)',
            'ram' => '64 GB',
            'gpu' => 'NVIDIA RTX 3080 / RTX 4070 (12 GB VRAM)',
            'storage' => 'NVMe SSD, 500 GB+ free',
            'display' => '2560x1440, dual-monitor setup',
        ]), 'json');

        $this->content('pro_spatial', 'upgrades.heading', 'Choose your starting point', 'text');
        $this->content('pro_spatial', 'upgrades.description', 'All editions include the 3D viewer and basic annotation. Upgrade at any time.', 'text');

        $this->content('pro_spatial', 'cta.heading', 'Ready to see Pro Spatial in action?', 'text');
        $this->content('pro_spatial', 'cta.description', 'Our solution engineers will walk you through a workflow tailored to your project.', 'text');
        $this->content('pro_spatial', 'cta.button_primary_text', 'Request a demo', 'text');
        $this->content('pro_spatial', 'cta.button_secondary_text', 'Start free trial', 'text');
    }

    private function seedSolutions(): void
    {
        $this->content('solutions', 'hero.heading', 'AutoTerra for every spatial workflow', 'text');
        $this->content('solutions', 'hero.description', 'From drone-collected LiDAR to submission-ready CAD — AutoTerra adapts to the way your industry works.', 'text');

        $this->content('solutions', 'verticals', json_encode([
            [
                'id' => 'survey',
                'label' => 'Survey & Mapping',
                'heading' => 'Precision survey — from total station to topographic deliverable',
                'description' => 'AutoTerra has been the surveyor\'s tool of choice across Asia, Africa, and the Middle East for over two decades.',
            ],
            [
                'id' => 'roads',
                'label' => 'Roads & Highway',
                'heading' => 'LiDAR-driven road surveys and corridor design',
            ],
            [
                'id' => 'lidar',
                'label' => 'LiDAR Aerial Mapping',
                'heading' => 'From raw airborne or drone LiDAR to publication-ready terrain',
            ],
            [
                'id' => 'mining',
                'label' => 'Mining',
                'heading' => 'Stockpile volumes, strata mapping, and pit survey',
            ],
            [
                'id' => 'govt',
                'label' => 'Government & Infrastructure',
                'heading' => 'National mapping, flood modeling, and utility corridor surveys',
            ],
            [
                'id' => 'uav',
                'label' => 'UAV & Drone Mapping',
                'heading' => 'From drone to deliverable — in a single workspace',
            ],
        ]), 'json');

        $this->content('solutions', 'cta.heading', 'Find the right edition for your workflow', 'text');
        $this->content('solutions', 'cta.description', 'All solutions run on the same AutoTerra platform — compare editions or talk to our team.', 'text');
        $this->content('solutions', 'cta.button_primary_text', 'Compare editions', 'text');
        $this->content('solutions', 'cta.button_secondary_text', 'Request a demo', 'text');
    }

    private function seedResources(): void
    {
        $this->content('resources', 'hero.eyebrow', 'Resources', 'text');
        $this->content('resources', 'hero.heading', 'Everything you need to get the most from AutoTerra', 'text');
        $this->content('resources', 'hero.description', 'User guides, API documentation, video tutorials, sample datasets, release notes, and recorded webinars — all in one place.', 'text');

        $this->content('resources', 'auth_gate.heading', 'Sign in to access resources', 'text');
        $this->content('resources', 'auth_gate.description', 'Documentation, video tutorials, sample datasets, and release notes are available to registered AutoTerra users.', 'text');
        $this->content('resources', 'auth_gate.button_signin', 'Sign in', 'text');
        $this->content('resources', 'auth_gate.button_signup', 'Create a free account', 'text');
    }

    private function seedBlog(): void
    {
        $this->content('blog', 'hero.eyebrow', 'Blog', 'text');
        $this->content('blog', 'hero.heading', 'Insights from the AutoTerra team', 'text');
        $this->content('blog', 'hero.description', 'Technical articles, case studies, and product updates from the engineers behind AutoTerra.', 'text');
    }

    private function seedLogin(): void
    {
        $this->content('login', 'tagline', 'Your spatial data, fully in control', 'text');
        $this->content('login', 'tagline_sub', 'Sign in to access your projects, licences, downloads, and support tickets — all in one place.', 'text');
        $this->content('login', 'features', json_encode([
            'Download the latest software release',
            'Manage floating and node-locked licences',
            'Access project files and saved workspaces',
            'Raise and track support requests',
            'Full documentation and video tutorials',
        ]), 'json');
    }

    private function seedSignup(): void
    {
        $this->content('signup', 'tagline', 'Start your free 30-day trial today', 'text');
        $this->content('signup', 'tagline_sub', 'No credit card required. Full access to AutoTerra Standard features to evaluate the platform.', 'text');
        $this->content('signup', 'perks', json_encode([
            'Full Standard-tier access for 30 days — no limitations on project count',
            'Access to all documentation, tutorials, and sample datasets',
            'No credit card or payment details required to start',
            'Onboarding call with our support team included',
            'Convert to a paid subscription at any time — keep all your work',
        ]), 'json');
    }

    private function content(string $page, string $key, string $value, string $type): void
    {
        PageContent::updateOrCreate(
            ['page' => $page, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}
