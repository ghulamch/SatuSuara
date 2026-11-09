<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoterQueue;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Path to the settings JSON file.
     */
    protected function settingsPath(): string
    {
        return storage_path('app/settings.json');
    }

    /**
     * Load settings from the JSON file.
     *
     * @return array<string, mixed>
     */
    protected function loadSettings(): array
    {
        $path = $this->settingsPath();

        if (! file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);

        return json_decode($json, true) ?? [];
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $settings = $this->loadSettings();

        $stats = [
            'voters_count'     => User::role('voters')->count(),
            'candidates_count' => Candidate::count(),
            'votes_count'      => Vote::count(),
        ];

        return view('admin.settings.index', compact('settings', 'stats'));
    }

    /**
     * Save the election settings and logo to storage/app/settings.json.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'election_name'         => ['nullable', 'string', 'max:255'],
            'institution'           => ['nullable', 'string', 'max:255'],
            'election_date'         => ['nullable', 'date'],
            'start_time'            => ['nullable', 'string'],
            'end_time'              => ['nullable', 'string'],
            'status_pelaksanaan'    => ['nullable', 'in:auto,buka,tutup'],
            'enable_queue'          => ['nullable', 'boolean'],
            'max_concurrent_voters' => ['nullable', 'integer', 'min:1', 'max:500'],
            'booth_session_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'timezone'              => ['nullable', 'string', 'max:100'],
            'locale'                => ['nullable', 'string', 'in:id,en'],
            'kiosk_mode'            => ['nullable', 'boolean'],
            'kiosk_timeout_seconds' => ['nullable', 'integer', 'min:3', 'max:60'],
            'enable_kotak_kosong'   => ['nullable', 'boolean'],
            'enable_single_session' => ['nullable', 'boolean'],
            'logo_instansi'         => [
                'nullable',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (! $value instanceof \Illuminate\Http\UploadedFile) {
                        return;
                    }

                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowed = ['png', 'jpg', 'jpeg', 'svg', 'webp'];

                    if (! in_array($extension, $allowed, true)) {
                        $fail('Logo instansi harus berformat PNG, JPG, JPEG, SVG, atau WebP.');
                        return;
                    }

                    // Validasi khusus untuk SVG dengan sanitasi Stored XSS & XXE
                    if ($extension === 'svg') {
                        $content = file_get_contents($value->getRealPath());
                        if ($content === false || stripos($content, '<svg') === false) {
                            $fail('Berkas SVG tidak valid atau tidak memiliki elemen <svg>.');
                            return;
                        }

                        $cleanSvg = $this->sanitizeSvg($content);
                        if ($cleanSvg === null) {
                            $fail('Berkas SVG tidak valid atau mengandung elemen yang dilarang (skrip aktif / XXE entity).');
                            return;
                        }
                        return;
                    }

                    // Validasi file gambar raster (PNG, JPG, WebP)
                    if (! @getimagesize($value->getRealPath())) {
                        $fail('Berkas gambar tidak valid atau rusak.');
                    }
                },
            ],
            'hapus_logo'            => ['nullable', 'boolean'],
        ]);

        $validated['enable_queue'] = $request->boolean('enable_queue', true);
        $validated['max_concurrent_voters'] = (int) ($validated['max_concurrent_voters'] ?? 10);
        $validated['booth_session_minutes'] = (int) ($validated['booth_session_minutes'] ?? 3);
        $validated['kiosk_mode'] = $request->boolean('kiosk_mode', false);
        $validated['kiosk_timeout_seconds'] = (int) ($validated['kiosk_timeout_seconds'] ?? 5);
        $validated['enable_kotak_kosong'] = $request->boolean('enable_kotak_kosong', false);
        $validated['enable_single_session'] = $request->boolean('enable_single_session', true);

        $settings = $this->loadSettings();
        $uploadDir = public_path('uploads/settings');

        if (! file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle logo removal
        if ($request->boolean('hapus_logo')) {
            if (! app()->environment('testing') && ! empty($settings['logo_instansi']) && file_exists(public_path($settings['logo_instansi']))) {
                @unlink(public_path($settings['logo_instansi']));
            }
            $settings['logo_instansi'] = null;
        }

        // Handle new logo upload with strict sanitization (without storage:link)
        if ($request->hasFile('logo_instansi')) {
            // Delete old logo if exists (skip unlinking in testing to preserve user assets)
            if (! app()->environment('testing') && ! empty($settings['logo_instansi']) && file_exists(public_path($settings['logo_instansi']))) {
                @unlink(public_path($settings['logo_instansi']));
            }

            $file = $request->file('logo_instansi');
            $extension = strtolower($file->getClientOriginalExtension());
            $safeExtension = in_array($extension, ['png', 'jpg', 'jpeg', 'svg', 'webp'], true) ? $extension : 'png';
            $fileName = 'logo_' . time() . '_' . uniqid() . '.' . $safeExtension;

            if ($safeExtension === 'svg') {
                $rawContent = file_get_contents($file->getRealPath());
                $cleanSvg = $this->sanitizeSvg($rawContent);
                file_put_contents($uploadDir . '/' . $fileName, $cleanSvg ?? $rawContent);
            } else {
                $file->move($uploadDir, $fileName);
            }

            $settings['logo_instansi'] = 'uploads/settings/' . $fileName;
        }

        unset($validated['logo_instansi'], $validated['hapus_logo']);

        $settings = array_merge($settings, $validated);

        // Ensure defaults
        $settings['timezone'] = $settings['timezone'] ?? 'Asia/Jakarta';
        $settings['locale']   = $settings['locale'] ?? 'id';

        file_put_contents($this->settingsPath(), json_encode($settings, JSON_PRETTY_PRINT));

        // Refresh in-memory config
        foreach ($settings as $key => $value) {
            config(['election.' . $key => $value]);
        }

        // Apply immediately to current request
        date_default_timezone_set($settings['timezone']);
        config(['app.timezone' => $settings['timezone']]);
        \Illuminate\Support\Facades\App::setLocale($settings['locale']);
        \Carbon\Carbon::setLocale($settings['locale']);
        config(['app.locale' => $settings['locale']]);

        AuditLogger::log('pengaturan', 'Memperbarui pengaturan pemilihan, jadwal, kuota antrian, dan preferensi sistem');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan dan logo instansi berhasil disimpan.');
    }

    /**
     * Clear all DPT (voters accounts) and all votes with mandatory current_password verification.
     */
    public function clearVoters(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.required'         => 'Kata sandi akun Anda wajib diisi untuk konfirmasi keamanan.',
            'password.current_password' => 'Kata sandi yang Anda masukkan salah. Operasi pembersihan dibatalkan demi keamanan.',
        ]);

        DB::transaction(function () {
            // Delete all vote transactions and queues
            Vote::query()->delete();
            VoterQueue::query()->delete();

            // Delete only users with voters role (protect admin accounts)
            $voters = User::role('voters')->get();
            foreach ($voters as $voter) {
                $voter->delete();
            }
        });

        AuditLogger::log('reset', 'DANGER ZONE: Membersihkan seluruh akun pemilih (DPT) dan riwayat suara');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Seluruh data Pemilih (DPT) dan riwayat suara berhasil dibersihkan.');
    }

    /**
     * Clear all candidates and clean their uploaded photos with mandatory current_password verification.
     */
    public function clearCandidates(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.required'         => 'Kata sandi akun Anda wajib diisi untuk konfirmasi keamanan.',
            'password.current_password' => 'Kata sandi yang Anda masukkan salah. Operasi pembersihan dibatalkan demi keamanan.',
        ]);

        DB::transaction(function () {
            // Delete votes referencing candidates
            Vote::query()->delete();
            VoterQueue::query()->delete();

            $candidates = Candidate::all();
            foreach ($candidates as $c) {
                // Delete photo files from public directory
                if ($c->foto_ketua && file_exists(public_path($c->foto_ketua))) {
                    @unlink(public_path($c->foto_ketua));
                }
                if ($c->foto_wakil && file_exists(public_path($c->foto_wakil))) {
                    @unlink(public_path($c->foto_wakil));
                }
                $c->delete();
            }
        });

        AuditLogger::log('reset', 'DANGER ZONE: Membersihkan seluruh data pasangan calon dan berkas foto');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Seluruh data Pasangan Calon dan berkas foto berhasil dibersihkan.');
    }

    /**
     * Full election reset: clear all votes, voters, and candidates with mandatory current_password verification.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.required'         => 'Kata sandi akun Anda wajib diisi untuk konfirmasi keamanan.',
            'password.current_password' => 'Kata sandi yang Anda masukkan salah. Operasi pembersihan dibatalkan demi keamanan.',
        ]);

        DB::transaction(function () {
            Vote::query()->delete();
            VoterQueue::query()->delete();

            $voters = User::role('voters')->get();
            foreach ($voters as $voter) {
                $voter->delete();
            }

            $candidates = Candidate::all();
            foreach ($candidates as $c) {
                if ($c->foto_ketua && file_exists(public_path($c->foto_ketua))) {
                    @unlink(public_path($c->foto_ketua));
                }
                if ($c->foto_wakil && file_exists(public_path($c->foto_wakil))) {
                    @unlink(public_path($c->foto_wakil));
                }
                $c->delete();
            }
        });

        AuditLogger::log('reset', 'DANGER ZONE: Reset total seluruh data pemilihan (DPT, Paslon, dan perolehan suara)');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Reset Total Berhasil: Seluruh data DPT, Paslon, dan perolehan suara telah dibersihkan untuk pemilihan baru.');
    }

    /**
     * Sanitize and validate SVG content against Stored XSS and XXE.
     * Returns sanitized XML string, or null if invalid / contains blocked elements.
     */
    protected function sanitizeSvg(string $content): ?string
    {
        // 1. Block XML external entity declarations and DOCTYPE (XXE protection)
        if (preg_match('/<!DOCTYPE|<!ENTITY|SYSTEM|PUBLIC/i', $content)) {
            return null;
        }

        // 2. Parse XML safely with libxml internal errors enabled
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument();
        $loaded = $dom->loadXML($content, LIBXML_NONET | LIBXML_NOENT | LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        if (! $loaded) {
            return null;
        }

        // Root element must be <svg>
        if (! $dom->documentElement || strtolower($dom->documentElement->nodeName) !== 'svg') {
            return null;
        }

        // 3. Remove dangerous executable elements
        $blockedTags = ['script', 'foreignobject', 'embed', 'object', 'iframe', 'set', 'animate', 'use', 'meta', 'link'];
        foreach ($blockedTags as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            while ($elements->length > 0) {
                $el = $elements->item(0);
                $el->parentNode->removeChild($el);
            }
        }

        // 4. Remove dangerous attributes (event handlers on*, javascript: hrefs)
        $xpath = new \DOMXPath($dom);
        $allElements = $xpath->query('//*');

        if ($allElements) {
            foreach ($allElements as $node) {
                if ($node instanceof \DOMElement && $node->hasAttributes()) {
                    $attributesToRemove = [];
                    foreach ($node->attributes as $attr) {
                        $name = strtolower($attr->name);
                        $val = strtolower(trim($attr->value));

                        // Block inline event handlers (onload, onclick, onerror, onmouseover, dll)
                        if (str_starts_with($name, 'on') || str_contains($name, ':on')) {
                            $attributesToRemove[] = $attr->name;
                        }
                        // Block javascript:, vbscript:, or data: pseudo-protocols in hrefs / actions
                        elseif (in_array($name, ['href', 'xlink:href', 'src', 'action', 'data'], true)) {
                            if (preg_match('/^(javascript|vbscript|data):/i', $val)) {
                                $attributesToRemove[] = $attr->name;
                            }
                        }
                    }

                    foreach ($attributesToRemove as $attrName) {
                        $node->removeAttribute($attrName);
                    }
                }
            }
        }

        return $dom->saveXML();
    }
}
