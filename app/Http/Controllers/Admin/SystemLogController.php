<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SystemLogController extends Controller {
    public function index(Request $request): View {
        $logDirectory = storage_path('logs');
        $files = collect(File::files($logDirectory))
            ->filter(fn ($file) => $file->getExtension() === 'log')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        $selectedFile = $request->filled('file')
            ? $files->first(fn ($file) => $file->getFilename() === basename((string) $request->file))
            : $files->first();

        $entries = $selectedFile ? $this->parseLogFile($selectedFile->getPathname()) : collect();

        if ($request->filled('level')) {
            $entries = $entries->where('level', strtoupper((string) $request->level))->values();
        }

        if ($request->filled('search')) {
            $search = Str::lower(trim((string) $request->search));
            $entries = $entries->filter(function ($entry) use ($search) {
                return Str::contains(Str::lower($entry['message'].' '.$entry['raw']), $search);
            })->values();
        }

        $stats = [
            'total' => $entries->count(),
            'error' => $entries->where('level', 'ERROR')->count(),
            'warning' => $entries->where('level', 'WARNING')->count(),
            'info' => $entries->where('level', 'INFO')->count(),
            'debug' => $entries->where('level', 'DEBUG')->count(),
        ];

        return view('admin.system-logs.index', [
            'files' => $files,
            'selectedFile' => $selectedFile,
            'entries' => $entries->take(200),
            'stats' => $stats,
        ]);
    }

    protected function parseLogFile(string $path): Collection {
        $content = $this->readRecentLogChunk($path);

        if (blank($content)) {
            return collect();
        }

        $chunks = preg_split('/(?=^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/m', $content, -1, PREG_SPLIT_NO_EMPTY);

        return collect($chunks)->map(function ($chunk, $index) {
            $chunk = trim((string) $chunk);
            $lines = preg_split('/\r\n|\r|\n/', $chunk);
            $firstLine = $lines[0] ?? '';

            if (preg_match('/^\[(?<timestamp>[^\]]+)\]\s+(?<environment>[^.]+)\.(?<level>[A-Z]+):\s*(?<message>.*)$/', $firstLine, $matches)) {
                return [
                    'id' => $index,
                    'timestamp' => $matches['timestamp'],
                    'environment' => $matches['environment'],
                    'level' => strtoupper($matches['level']),
                    'message' => $matches['message'],
                    'raw' => $chunk,
                ];
            }

            return [
                'id' => $index,
                'timestamp' => null,
                'environment' => null,
                'level' => 'UNKNOWN',
                'message' => Str::limit($firstLine, 180),
                'raw' => $chunk,
            ];
        })->reverse()->values();
    }

    protected function readRecentLogChunk(string $path): string {
        $size = File::size($path);

        if ($size <= 1024 * 1024) {
            return File::get($path);
        }

        $handle = fopen($path, 'rb');
        if (! $handle) {
            return '';
        }

        $offset = max($size - (1024 * 1024), 0);
        fseek($handle, $offset);

        if ($offset > 0) {
            fgets($handle);
        }

        $contents = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $contents;
    }
}
