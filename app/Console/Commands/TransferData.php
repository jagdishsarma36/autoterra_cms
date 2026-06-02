<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TransferData extends Command
{
    protected $signature = 'db:transfer {--sqlite= : Path to SQLite database file (relative to project root)}';

    protected $description = 'Transfer data from SQLite to MySQL';

    private $sqlitePath;
    private $totalRows = 0;

    private array $tables = [
        // Phase 1 — independent tables
        'users',
        'products',
        'pages',
        'page_contents',
        'posts',
        'settings',
        'media',
        'quote_requests',
        'forms',
        // Phase 2 — depends on Phase 1
        'product_prices',
        'orders',
        'subscriptions',
        'form_fields',
        'form_submissions',
        // Phase 3 — depends on Phase 1 + 2
        'license_keys',
    ];

    private array $jsonColumns = [
        'form_fields' => ['options'],
        'posts' => ['tags'],
        'form_submissions' => ['data'],
    ];

    public function handle(): int
    {
        $this->sqlitePath = $this->option('sqlite') ?: database_path('database.sqlite');

        if (!File::exists($this->sqlitePath)) {
            $this->error("SQLite file not found: {$this->sqlitePath}");
            return 1;
        }

        // Register SQLite connection
        config()->set('database.connections.sqlite_file', [
            'driver' => 'sqlite',
            'database' => $this->sqlitePath,
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]);

        try {
            DB::connection('sqlite_file')->getPdo();
        } catch (\Exception $e) {
            $this->error("Failed to connect to SQLite: {$e->getMessage()}");
            return 1;
        }

        $this->info("SQLite: {$this->sqlitePath}");
        $this->info("Target: MySQL (" . config('database.connections.mysql.database') . ")");
        $this->newLine();

        // Disable FK checks
        DB::connection('mysql')->unprepared('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($this->tables as $table) {
                $this->transferTable($table);
            }
        } finally {
            // Re-enable FK checks
            DB::connection('mysql')->unprepared('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->newLine();
        $this->info("Transfer complete! {$this->totalRows} total rows across " . count($this->tables) . " tables.");

        return 0;
    }

    private function transferTable(string $table): void
    {
        $sqlite = DB::connection('sqlite_file');
        $mysql = DB::connection('mysql');

        $count = $sqlite->table($table)->count();

        if ($count === 0) {
            $this->line("  <comment>{$table}</comment> — 0 rows (skipped)");
            return;
        }

        $rows = $sqlite->table($table)->orderBy('id')->get();

        if ($rows->isEmpty()) {
            $this->line("  <comment>{$table}</comment> — 0 rows (skipped)");
            return;
        }

        // Get column names from the first row
        $columns = array_keys((array) $rows->first());

        // Delete existing rows to avoid unique constraint conflicts
        $existingCount = $mysql->table($table)->count();
        $mysql->table($table)->delete();

        // Process rows in chunks for efficiency
        $chunks = $rows->chunk(500);

        foreach ($chunks as $chunk) {
            $records = [];

            foreach ($chunk as $row) {
                $record = [];

                foreach ($columns as $column) {
                    $value = $row->{$column} ?? null;

                    // Handle JSON columns — convert PHP serialized to JSON
                    if (isset($this->jsonColumns[$table]) && in_array($column, $this->jsonColumns[$table])) {
                        $value = $this->convertToJson($value);
                    }

                    $record[$column] = $value;
                }

                $records[] = $record;
            }

            $mysql->table($table)->insert($records);
        }

        $this->totalRows += $count;
        $label = "{$existingCount} existing deleted, {$count} inserted";
        $this->line("  <info>{$table}</info> — {$count} rows transferred ({$label})");

        // Reset auto-increment counter
        $maxId = $mysql->table($table)->max('id');
        if ($maxId) {
            $nextId = $maxId + 1;
            $mysql->unprepared("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");
        }
    }

    private function convertToJson(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Already valid JSON
        if (is_string($value) && str_starts_with($value, '{') || str_starts_with($value, '[')) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $value;
            }
        }

        // PHP serialized — unserialize then json_encode
        if (is_string($value) && preg_match('/^[aOsi]:/', $value)) {
            $unserialized = @unserialize($value);
            if ($unserialized !== false) {
                return json_encode($unserialized, JSON_UNESCAPED_UNICODE);
            }
        }

        // Plain string — wrap in JSON string
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
