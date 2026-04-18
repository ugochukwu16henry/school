<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AuditTenantScopeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:audit-scopes
                            {--path=app : Relative path to scan}
                            {--fail-on-findings : Return non-zero exit code when findings exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan code for query patterns that can bypass tenant school scope';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $relativePath = trim((string) $this->option('path'));
        $scanPath = base_path($relativePath);

        if (!File::exists($scanPath)) {
            $this->error('Path not found: ' . $scanPath);

            return 1;
        }

        $patterns = [
            'Query Builder Table Access' => 'DB::table(',
            'Global Scope Bypass' => 'withoutGlobalScope(',
            'Global Scope Bypass (All)' => 'withoutGlobalScopes(',
            'Raw SQL Access' => 'DB::select(',
            'Raw SQL Statement' => 'DB::statement(',
            'Raw SQL Unprepared' => 'DB::unprepared(',
        ];

        $findings = [];
        $files = File::isFile($scanPath) ? [new \SplFileInfo($scanPath)] : File::allFiles($scanPath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $absolutePath = $file->getPathname();
            $content = File::get($absolutePath);
            $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];

            foreach ($lines as $index => $line) {
                foreach ($patterns as $label => $needle) {
                    if (strpos($line, $needle) === false) {
                        continue;
                    }

                    $findings[] = [
                        'file' => Str::after($absolutePath, base_path() . DIRECTORY_SEPARATOR),
                        'line' => $index + 1,
                        'pattern' => $label,
                        'snippet' => trim($line),
                    ];
                }
            }
        }

        if (empty($findings)) {
            $this->info('No risky tenant-scope patterns found in ' . $relativePath . '.');

            return 0;
        }

        $this->warn('Potential tenant-scope risks found: ' . count($findings));
        $this->table(['File', 'Line', 'Pattern', 'Snippet'], array_map(function ($item) {
            return [
                $item['file'],
                (string) $item['line'],
                $item['pattern'],
                Str::limit($item['snippet'], 120),
            ];
        }, $findings));

        if ($this->option('fail-on-findings')) {
            return 2;
        }

        return 0;
    }
}
