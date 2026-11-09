@extends('layouts.admin')

@section('title', 'Atur Kewenangan Peran: ' . ucfirst(str_replace('_', ' ', $role->name)))

@section('content')
    <div style="max-width: 980px; margin: 0 auto;">
        <!-- TOP BREADCRUMB & HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
            <div style="display:flex; align-items:center; gap:12px;">
                <a href="{{ route('admin.users.index', ['tab' => 'roles']) }}" class="btn btn-secondary btn-sm" style="padding:6px 12px;">
                    &larr; Kembali ke Daftar Peran
                </a>
                <div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:16px; font-weight:700; color:var(--text); letter-spacing:-0.01em;">
                            Peran:
                        </span>
                        <span class="badge badge-primary" style="font-size:13px; font-family:monospace; padding:4px 10px;">
                            {{ $role->name }}
                        </span>
                    </div>
                    <p style="font-size:13px; color:var(--text-sec); margin-top:2px;">
                        Tentukan menu dan modul operasional yang dapat diakses oleh panitia dengan peran ini.
                    </p>
                </div>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllPermissions(true)">
                    ✓ Centang Semua
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAllPermissions(false)">
                    ✗ Kosongkan Semua
                </button>
            </div>
        </div>

        @if ($role->name === 'admin')
            <div style="background:#EEF1F9; border:1px solid #C7D3F0; border-radius:10px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:flex-start; gap:12px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--accent); flex-shrink:0; margin-top:2px;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <div style="font-size:13px; color:var(--text); line-height:1.5;">
                    <strong style="color:var(--accent);">Peran Utama Administrator:</strong><br>
                    Peran <code>admin</code> memiliki kendali penuh ke seluruh modul pemilihan tanpa pembatasan.
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.roles.update-permissions', $role->id) }}" id="permissionsForm">
            @csrf
            @method('PUT')

            <!-- DAFTAR MODUL DENGAN GRID ACCORDION -->
            <div style="display:flex; flex-direction:column; gap:20px; margin-bottom:28px;">
                @foreach ($modules as $moduleKey => $mod)
                    @php
                        $modulePermissions = array_keys($mod['permissions']);
                        $activeCount = count(array_intersect($modulePermissions, $rolePermissions));
                        $isFull = $activeCount === count($modulePermissions);
                    @endphp
                    <div class="card" style="margin-bottom:0; padding:20px 24px; border-radius:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid var(--border); flex-wrap:wrap; gap:10px;">
                            <div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <h2 style="font-size:16px; font-weight:700; color:var(--text); margin:0;">
                                        {{ $mod['name'] }}
                                    </h2>
                                    <span class="badge {{ $activeCount > 0 ? 'badge-success' : 'badge-neutral' }}" id="badge-module-{{ $moduleKey }}" style="font-size:11px;">
                                        {{ $activeCount }} / {{ count($modulePermissions) }} Izin Aktif
                                    </span>
                                </div>
                                <div style="font-size:12.5px; color:var(--text-sec); margin-top:3px;">
                                    {{ $mod['description'] }}
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleModulePermissions('{{ $moduleKey }}')" style="font-size:11.5px; padding:4px 10px;">
                                Toggle Semua Modul Ini
                            </button>
                        </div>

                        <!-- LIST PERMISSIONS CHECKBOX -->
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:12px;">
                            @foreach ($mod['permissions'] as $permKey => $permLabel)
                                @php
                                    $isChecked = in_array($permKey, $rolePermissions, true);
                                @endphp
                                <label style="display:flex; align-items:flex-start; gap:10px; background:#FAFBFC; border:1px solid {{ $isChecked ? 'var(--accent)' : 'var(--border)' }}; border-radius:8px; padding:12px 14px; cursor:pointer; transition:all 0.15s ease;" id="label-{{ $permKey }}">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permKey }}"
                                        data-module="{{ $moduleKey }}"
                                        {{ $isChecked ? 'checked' : '' }}
                                        onchange="onPermissionChange('{{ $permKey }}', '{{ $moduleKey }}')"
                                        style="margin-top:2px; width:16px; height:16px; accent-color:var(--accent); flex-shrink:0; cursor:pointer;"
                                    >
                                    <div style="flex:1;">
                                        <div style="font-size:12px; font-family:monospace; font-weight:700; color:var(--text);">
                                            {{ $permKey }}
                                        </div>
                                        <div style="font-size:12px; color:var(--text-sec); margin-top:3px; line-height:1.4;">
                                            {{ $permLabel }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- BOTTOM SUBMIT BAR -->
            <div style="position:sticky; bottom:20px; background:#FFFFFF; border:1px solid var(--border); border-radius:12px; padding:16px 24px; box-shadow:0 8px 24px rgba(0,0,0,0.08); display:flex; justify-content:space-between; align-items:center; z-index:20;">
                <div>
                    <span style="font-size:13.5px; font-weight:600; color:var(--text);">Simpan Perubahan Matriks Hak Akses?</span>
                    <div style="font-size:12px; color:var(--text-sec);">Perubahan izin akan langsung berlaku untuk seluruh akun dengan peran ini.</div>
                </div>

                <div style="display:flex; gap:12px;">
                    <a href="{{ route('admin.users.index', ['tab' => 'roles']) }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px;">
                        Simpan Hak Akses Peran
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function onPermissionChange(permKey, moduleKey) {
        const checkbox = document.querySelector(`input[value="${permKey}"]`);
        const label = document.getElementById(`label-${permKey}`);
        if (checkbox && label) {
            if (checkbox.checked) {
                label.style.borderColor = 'var(--accent)';
                label.style.background = '#F6F8FD';
            } else {
                label.style.borderColor = 'var(--border)';
                label.style.background = '#FAFBFC';
            }
        }
        updateModuleBadge(moduleKey);
    }

    function updateModuleBadge(moduleKey) {
        const checkboxes = document.querySelectorAll(`input[data-module="${moduleKey}"]`);
        let checked = 0;
        checkboxes.forEach(cb => { if (cb.checked) checked++; });

        const badge = document.getElementById(`badge-module-${moduleKey}`);
        if (badge) {
            badge.textContent = `${checked} / ${checkboxes.length} Izin Aktif`;
            if (checked > 0) {
                badge.className = 'badge badge-success';
            } else {
                badge.className = 'badge badge-neutral';
            }
        }
    }

    function toggleModulePermissions(moduleKey) {
        const checkboxes = document.querySelectorAll(`input[data-module="${moduleKey}"]`);
        let allChecked = true;
        checkboxes.forEach(cb => { if (!cb.checked) allChecked = false; });

        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
            onPermissionChange(cb.value, moduleKey);
        });
    }

    function toggleAllPermissions(check) {
        const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
        checkboxes.forEach(cb => {
            cb.checked = check;
            const moduleKey = cb.getAttribute('data-module');
            onPermissionChange(cb.value, moduleKey);
        });
    }
</script>
@endpush

