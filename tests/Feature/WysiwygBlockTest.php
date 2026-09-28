<?php

namespace Tests\Feature;

use App\Filament\Resources\PageCmsResource\Pages\CreatePage;
use App\Filament\Resources\PageCmsResource\Pages\EditPage;
use App\Models\PageContent;
use App\Models\PageCms;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Livewire\Features\SupportTesting\Testable;
use Tests\TestCase;

class WysiwygBlockTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    /**
     * A document using the two marks a bare StarterKit schema cannot express.
     */
    protected function doc(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'marks' => [['type' => 'underline']], 'text' => 'Underlined'],
                        ['type' => 'text', 'text' => ' and '],
                        [
                            'type' => 'text',
                            'marks' => [['type' => 'link', 'attrs' => ['href' => 'https://example.com']]],
                            'text' => 'linked',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function block(string $key, string $type, mixed $value): array
    {
        $block = ['key' => $key, 'type' => $type];

        if ($type === 'wysiwyg') {
            $block['wysiwyg_value'] = $value;
        } else {
            $block['value'] = $value;
        }

        return $block;
    }

    /**
     * `fillForm()` merges into the repeater instead of replacing it, so the
     * blocks the page was mounted with have to be cleared first.
     */
    protected function replaceBlocks(Testable $page, array $blocks): Testable
    {
        return $page->set('data.content_blocks', [])->fillForm(['content_blocks' => $blocks]);
    }

    public function test_it_stores_and_reads_back_a_wysiwyg_block(): void
    {
        $this->actingAs($this->admin());

        $uuid = (string) Str::uuid();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Wysiwyg Test',
                'slug' => 'wysiwyg-test',
                'content_blocks' => [
                    $uuid => $this->block('hero.body', 'wysiwyg', $this->doc()),
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $block = PageContent::where('key', 'hero.body')->first();

        $this->assertNotNull($block, 'The content block was not saved at all.');
        $this->assertSame('wysiwyg', $block->type);
        $this->assertSame(
            '<p><u>Underlined</u> and <a href="https://example.com">linked</a></p>',
            $block->value,
            'A WYSIWYG block must be stored as HTML, not as Tiptap JSON.',
        );
    }

    public function test_underline_and_link_survive_the_render_round_trip(): void
    {
        $page = PageCms::create(['title' => 'RT', 'slug' => 'rt']);

        PageContent::create([
            'page' => 'cms:' . $page->slug,
            'key' => 'hero.body',
            'type' => 'wysiwyg',
            'value' => json_encode($this->doc()),
        ]);

        $rendered = PageContent::get('cms:' . $page->slug, 'hero.body');

        $this->assertStringContainsString('<u>', $rendered, 'Underline was stripped on render.');
        $this->assertStringContainsString('href="https://example.com"', $rendered, 'Link was stripped on render.');
    }

    public function test_duplicate_keys_do_not_wipe_the_page(): void
    {
        $this->actingAs($this->admin());

        $page = PageCms::create(['title' => 'Dupes', 'slug' => 'dupes']);

        PageContent::create([
            'page' => 'cms:dupes',
            'key' => 'a.one',
            'type' => 'text',
            'value' => 'first',
        ]);

        $a = (string) Str::uuid();
        $b = (string) Str::uuid();

        $this->replaceBlocks(
            Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]),
            [
                $a => $this->block('a.one', 'text', 'first'),
                $b => $this->block('a.one', 'text', 'second'),
            ],
        )
            ->call('save')
            ->assertHasNoFormErrors();

        $remaining = PageContent::where('page', 'cms:dupes')->get();

        $this->assertCount(1, $remaining, 'Duplicate keys destroyed the page content.');
        $this->assertSame('first', $remaining->first()->value);
    }

    public function test_blank_keys_are_skipped_instead_of_wiping_the_page(): void
    {
        PageContent::create([
            'page' => 'cms:blanks',
            'key' => 'keep.me',
            'type' => 'text',
            'value' => 'safe',
        ]);

        PageContent::syncForPage('cms:blanks', [
            ['key' => '   ', 'type' => 'text', 'value' => 'junk'],
            ['key' => 'keep.me', 'type' => 'text', 'value' => 'safe'],
        ]);

        $this->assertSame(1, PageContent::where('page', 'cms:blanks')->count());
        $this->assertSame('safe', PageContent::where('key', 'keep.me')->value('value'));
    }

    public function test_a_failed_write_rolls_back_and_keeps_the_page(): void
    {
        PageContent::create([
            'page' => 'cms:atomic',
            'key' => 'keep.me',
            'type' => 'text',
            'value' => 'safe',
        ]);

        PageContent::saving(function (PageContent $block): void {
            if ($block->key === 'explode') {
                throw new \RuntimeException('simulated failure');
            }
        });

        try {
            PageContent::syncForPage('cms:atomic', [
                ['key' => 'replacement', 'type' => 'text', 'value' => 'x'],
                ['key' => 'explode', 'type' => 'text', 'value' => 'y'],
            ]);

            $this->fail('syncForPage() should have propagated the failure.');
        } catch (\RuntimeException) {
            // Expected.
        }

        $this->assertSame(1, PageContent::where('page', 'cms:atomic')->count());
        $this->assertSame('safe', PageContent::where('key', 'keep.me')->value('value'));
    }

    /**
     * Rendering a Tiptap editor for every block is what exhausted PHP's memory
     * limit and hung the browser, so the editor must stay gated behind the
     * per-block "Edit content" switch.
     */
    public function test_only_the_open_block_renders_a_rich_editor(): void
    {
        $this->actingAs($this->admin());

        // Filament repeats the wrapper class across a single editor's markup,
        // so the meaningful measurement is "none at all" versus "the same
        // amount no matter how many blocks the page has".
        $page = $this->replaceBlocks(Livewire::test(CreatePage::class), $this->wysiwygBlocks(3));

        $this->assertSame(
            0,
            substr_count($page->html(), 'fi-fo-rich-editor'),
            'A rich editor rendered for a block that is not open.',
        );

        $withThree = $this->openFirstBlock($page);
        $withEight = $this->openFirstBlock(
            $this->replaceBlocks(Livewire::test(CreatePage::class), $this->wysiwygBlocks(8)),
        );

        $this->assertGreaterThan(0, $withThree, 'Opening a block rendered no rich editor at all.');
        $this->assertSame(
            $withThree,
            $withEight,
            'The number of rendered editors grew with the number of blocks.',
        );
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function wysiwygBlocks(int $count): array
    {
        $blocks = [];

        foreach (range(1, $count) as $i) {
            $blocks[(string) Str::uuid()] = [
                ...$this->block("block.{$i}", 'wysiwyg', $this->doc()),
                'is_editing' => false,
            ];
        }

        return $blocks;
    }

    protected function openFirstBlock(Testable $page): int
    {
        $index = 0;

        $page->set('data.content_blocks', array_map(
            function (array $block) use (&$index): array {
                $block['is_editing'] = $index === 0;
                $index++;

                return $block;
            },
            $page->get('data.content_blocks'),
        ));

        return substr_count($page->html(), 'fi-fo-rich-editor');
    }

    public function test_editing_a_block_saves_its_content(): void
    {
        $this->actingAs($this->admin());

        $page = PageCms::create(['title' => 'Edit Me', 'slug' => 'edit-me']);

        PageContent::create([
            'page' => 'cms:edit-me',
            'key' => 'section.body',
            'type' => 'wysiwyg',
            'value' => json_encode($this->doc()),
        ]);

        $edited = [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Brand new copy']]],
            ],
        ];

        $uuid = (string) Str::uuid();

        $this->replaceBlocks(
            Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]),
            [$uuid => [...$this->block('section.body', 'wysiwyg', $edited), 'is_editing' => true]],
        )
            ->call('save')
            ->assertHasNoFormErrors();

        $block = PageContent::where('page', 'cms:edit-me')->where('key', 'section.body')->first();

        $this->assertNotNull($block, 'Saving wiped the block.');
        $this->assertSame(
            '<p>Brand new copy</p>',
            $block->value,
            'The editor content did not reach the database as HTML.',
        );
    }

    /**
     * The editor's JavaScript parses strings as HTML, so handing it a Tiptap
     * JSON string (`{"type":"doc",...}`) makes it display the raw JSON as
     * editable text — while the front end kept rendering proper HTML. Opening
     * a block must therefore always fill the editor with HTML.
     */
    public function test_opening_a_json_block_feeds_the_editor_html(): void
    {
        $this->actingAs($this->admin());

        $page = PageCms::create(['title' => 'Legacy JSON', 'slug' => 'legacy-json']);

        PageContent::create([
            'page' => 'cms:legacy-json',
            'key' => 'section.body',
            'type' => 'wysiwyg',
            'value' => json_encode($this->doc()),
        ]);

        $blocks = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->get('data.content_blocks');

        $uuid = array_key_first($blocks);
        $editorValue = $blocks[$uuid]['wysiwyg_value'] ?? null;

        $this->assertIsString($editorValue, 'The editor state should be a string, not a Tiptap array.');
        $this->assertStringStartsWith('<', $editorValue, 'The editor received raw JSON instead of HTML.');
        $this->assertStringNotContainsString('"type":"doc"', (string) $editorValue);
        $this->assertStringContainsString('<u>Underlined</u>', (string) $editorValue);
        $this->assertStringContainsString('href="https://example.com"', (string) $editorValue);
    }

    /**
     * Saving a page without opening the editor must not resurrect the raw
     * JSON: the fill already converted it to HTML, and the dehydrated block
     * value passes straight through unchanged.
     */
    public function test_saving_without_opening_the_editor_stores_html(): void
    {
        $this->actingAs($this->admin());

        $page = PageCms::create(['title' => 'Closed Save', 'slug' => 'closed-save']);

        PageContent::create([
            'page' => 'cms:closed-save',
            'key' => 'section.body',
            'type' => 'wysiwyg',
            'value' => json_encode($this->doc()),
        ]);

        $uuid = (string) Str::uuid();

        $this->replaceBlocks(
            Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]),
            [$uuid => [...$this->block('section.body', 'wysiwyg', '<p>Closed copy</p>'), 'is_editing' => false]],
        )
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            '<p>Closed copy</p>',
            PageContent::where('key', 'section.body')->value('value'),
            'A closed WYSIWYG block was saved back as raw JSON.',
        );
    }

    /**
     * The editor used to share the `value` state path with the plain textarea,
     * so merely opening a page rewrote every text/HTML block into a Tiptap
     * document and saved it back.
     */
    public function test_opening_a_page_does_not_rewrite_plain_blocks(): void
    {
        $this->actingAs($this->admin());

        $page = PageCms::create(['title' => 'Untouched', 'slug' => 'untouched']);

        PageContent::create([
            'page' => 'cms:untouched',
            'key' => 'hero.heading',
            'type' => 'text',
            'value' => 'Plain <b>heading</b>',
        ]);

        $this->replaceBlocks(
            Livewire::test(EditPage::class, ['record' => $page->getRouteKey()]),
            [(string) Str::uuid() => $this->block('hero.heading', 'text', 'Plain <b>heading</b>')],
        )
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            'Plain <b>heading</b>',
            PageContent::where('key', 'hero.heading')->value('value'),
            'A plain text block was rewritten into a Tiptap document.',
        );
    }

    public function test_a_plain_text_block_is_not_turned_into_json(): void
    {
        $this->actingAs($this->admin());

        $uuid = (string) Str::uuid();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Plain',
                'slug' => 'plain',
                'content_blocks' => [
                    $uuid => $this->block('hero.heading', 'text', 'Hello <b>world</b>'),
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('Hello <b>world</b>', PageContent::where('key', 'hero.heading')->value('value'));
    }

    public function test_html_section_type_keeps_its_class_suffix(): void
    {
        $this->actingAs($this->admin());

        $uuid = (string) Str::uuid();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Sections',
                'slug' => 'sections',
                'content_blocks' => [
                    $uuid => [
                        'key' => 'band',
                        'type' => 'html_section',
                        'value' => '<p>hi</p>',
                        'section_class' => 'section-dark',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame('html_section:section-dark', PageContent::where('key', 'band')->value('type'));
    }

    public function test_wysiwyg_block_with_section_class_keeps_the_class_suffix(): void
    {
        $this->actingAs($this->admin());

        $uuid = (string) Str::uuid();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Wysiwyg sections',
                'slug' => 'wysiwyg-sections',
                'content_blocks' => [
                    $uuid => [
                        'key' => 'band',
                        'type' => 'wysiwyg',
                        'wysiwyg_value' => $this->doc(),
                        'section_class' => 'section-dark',
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $block = PageContent::where('key', 'band')->first();

        $this->assertNotNull($block, 'The content block was not saved at all.');
        $this->assertSame('wysiwyg:section-dark', $block->type);
        $this->assertStringContainsString('<u>Underlined</u>', $block->value);
    }

    public function test_wysiwyg_block_without_a_section_class_stays_bare(): void
    {
        $this->actingAs($this->admin());

        $uuid = (string) Str::uuid();

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Bare wysiwyg',
                'slug' => 'bare-wysiwyg',
                'content_blocks' => [
                    $uuid => $this->block('hero.body', 'wysiwyg', $this->doc()),
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(
            'wysiwyg',
            PageContent::where('key', 'hero.body')->value('type'),
            'A WYSIWYG block with no section class must keep the bare type so existing pages render unchanged.',
        );
    }

    public function test_wysiwyg_section_class_renders_a_section_and_reopens_in_the_form(): void
    {
        $page = PageCms::create(['title' => 'Sectioned', 'slug' => 'sectioned']);
        $page->update(['is_published' => true, 'published_at' => now()]);

        PageContent::create([
            'page' => 'cms:sectioned',
            'key' => 'notes.body',
            'type' => 'wysiwyg:section-dark',
            'value' => '<p>Dark copy</p>',
        ]);

        $html = $this->get('/sectioned')->assertOk()->getContent();

        $this->assertStringContainsString('<section class="section section-dark">', $html);
        $this->assertStringContainsString('<p>Dark copy</p>', $html);

        // Reopening the admin decodes `wysiwyg:section-dark` back into the
        // base type plus the section class controls.
        $state = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->get('data.content_blocks');

        $uuid = array_key_first($state);
        $this->assertSame('wysiwyg', $state[$uuid]['type']);
        $this->assertSame('section-dark', $state[$uuid]['section_class']);
    }

    public function test_wysiwyg_custom_section_class_round_trips(): void
    {
        $page = PageCms::create(['title' => 'Custom', 'slug' => 'custom-class']);
        $page->update(['is_published' => true, 'published_at' => now()]);

        PageContent::create([
            'page' => 'cms:custom-class',
            'key' => 'band',
            'type' => 'wysiwyg:mega-band',
            'value' => '<p>Wrapped copy</p>',
        ]);

        $state = Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->get('data.content_blocks');

        $uuid = array_key_first($state);
        $this->assertSame('wysiwyg', $state[$uuid]['type']);
        $this->assertSame('custom', $state[$uuid]['section_class']);
        $this->assertSame('mega-band', $state[$uuid]['section_class_custom']);
        $this->assertSame('<p>Wrapped copy</p>', $state[$uuid]['wysiwyg_value']);

        $html = $this->get('/custom-class')->assertOk()->getContent();

        $this->assertStringContainsString('<section class="section mega-band">', $html);
        $this->assertStringContainsString('<p>Wrapped copy</p>', $html);
    }
}
