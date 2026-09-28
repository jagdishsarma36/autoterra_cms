<?php

namespace Tests\Feature;

use App\Filament\Resources\PageCmsResource\Pages\CreatePage;
use App\Filament\Resources\PageCmsResource\Pages\EditPage;
use App\Models\PageContent;
use App\Models\PageCms;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The front end used to render content blocks in a hard-coded section order
 * (hero -> stats -> section -> features -> faq -> testimonials -> form -> cta)
 * no matter how they were arranged in the CMS, so dragging a Content Block to
 * a new position in the admin did nothing on the page. These tests pin the
 * front end to the CMS order instead.
 */
class ContentBlockOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    protected function makePublishedPage(string $slug): PageCms
    {
        return PageCms::create([
            'title' => 'Ordered page',
            'slug' => $slug,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    protected function insertBlock(PageCms $page, string $key, string $type = 'text', ?string $value = null): void
    {
        PageContent::create([
            'page' => 'cms:' . $page->slug,
            'key' => $key,
            'type' => $type,
            'value' => $value ?? 'value of ' . $key,
        ]);
    }

    protected function pageHtml(PageCms $page): string
    {
        return $this->get('/' . $page->slug)->assertOk()->getContent();
    }

    public function test_front_end_renders_named_sections_in_cms_order(): void
    {
        $page = $this->makePublishedPage('ordered-page');

        // Stored CTA-first on purpose: the old template rendered hero first no
        // matter what, which is the bug being fixed.
        $this->insertBlock($page, 'cta.heading');
        $this->insertBlock($page, 'hero.heading');
        $this->insertBlock($page, 'section.heading');
        $this->insertBlock($page, 'faq', 'json', json_encode([
            ['question' => 'Q1', 'answer' => 'A1'],
        ]));

        $html = $this->pageHtml($page);

        $cta = strpos($html, '<section class="cta-band">');
        $hero = strpos($html, '<section style="background:var(--navy);padding:60px;">');
        $section = strpos($html, '<h2 class="sec-h2">value of section.heading</h2>');
        $faq = strpos($html, '<div class="sec-eye">FAQ</div>');

        $this->assertNotFalse($cta, 'CTA section should render.');
        $this->assertNotFalse($hero, 'Hero section should render.');
        $this->assertNotFalse($section, '"section" section should render.');
        $this->assertNotFalse($faq, 'FAQ section should render.');

        $this->assertLessThan($hero, $cta, 'CTA must render before the hero when its block is first in the CMS.');
        $this->assertLessThan($section, $hero, 'Hero must render before "section" when ordered so.');
        $this->assertLessThan($faq, $section, '"section" must render before FAQ when ordered so.');
    }

    public function test_reordering_blocks_in_the_admin_moves_them_on_the_page(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Reorder me',
                'slug' => 'reorder-me',
                'content_blocks' => [
                    'a' => ['key' => 'hero.heading', 'type' => 'text', 'value' => 'Hero first'],
                    'b' => ['key' => 'cta.heading', 'type' => 'text', 'value' => 'CTA second'],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = PageCms::where('slug', 'reorder-me')->firstOrFail();
        $page->update(['is_published' => true, 'published_at' => now()]);

        // Before the reorder the hero renders above the CTA block.
        $html = $this->pageHtml($page);
        $this->assertLessThan(strpos($html, '<section class="cta-band">'), strpos($html, 'Hero first'));

        // Drag the CTA block to the top of the repeater, exactly as the
        // browser does on drag end.
        $edit = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]);
        $keys = array_keys($edit->get('data.content_blocks'));

        $edit->mountAction([
            'name' => 'reorder',
            'arguments' => ['items' => [$keys[1], $keys[0]]],
            'context' => ['schemaComponent' => 'form.content_blocks'],
        ]);

        $edit->call('save')->assertHasNoFormErrors();

        $html = $this->pageHtml($page->fresh());
        $this->assertLessThan(strpos($html, 'Hero first'), strpos($html, '<section class="cta-band">'));
    }

    public function test_unmatched_blocks_render_in_cms_order(): void
    {
        $page = $this->makePublishedPage('misc-order');

        $this->insertBlock($page, 'note.first');
        $this->insertBlock($page, 'note.second');

        $html = $this->pageHtml($page);

        $this->assertLessThan(strpos($html, 'value of note.second'), strpos($html, 'value of note.first'));
    }

    public function test_all_blocks_are_ordered_by_id_in_the_query(): void
    {
        $page = $this->makePublishedPage('order-query');
        $this->insertBlock($page, 'zeta.last');
        $this->insertBlock($page, 'alpha.first');

        $sql = null;
        DB::listen(function ($query) use (&$sql) {
            if (str_contains($query->sql, 'from "page_contents"') && str_contains($query->sql, 'where')) {
                $sql = strtolower($query->sql);
            }
        });

        $page->allBlocks();

        $this->assertNotNull($sql, 'allBlocks() should query page_contents.');
        $this->assertStringContainsString('order by "id"', $sql);
    }

    public function test_blocks_render_in_id_order_not_key_alphabetical_order(): void
    {
        $page = $this->makePublishedPage('alpha-vs-id');

        // On MySQL, `WHERE page = ?` can be served from the unique (page, key)
        // index, which returns rows sorted by key. Without an explicit ORDER BY
        // the page would render alpha.second before zeta.first no matter how the
        // blocks are arranged in the CMS.
        $this->insertBlock($page, 'zeta.first');
        $this->insertBlock($page, 'alpha.second');

        $html = $this->pageHtml($page);

        $this->assertLessThan(strpos($html, 'value of alpha.second'), strpos($html, 'value of zeta.first'));
    }
}