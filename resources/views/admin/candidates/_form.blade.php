@php
    $isEdit = isset($candidate) && $candidate->exists;
@endphp

<div style="display:grid; grid-template-columns: 140px 1fr; gap:16px;">
    <div class="form-group">
        <label for="nomor_urut" class="form-label">Nomor Urut *</label>
        <input
            type="number"
            name="nomor_urut"
            id="nomor_urut"
            value="{{ old('nomor_urut', $candidate->nomor_urut ?? '') }}"
            min="1"
            max="99"
            required
            class="form-control {{ $errors->has('nomor_urut') ? 'is-invalid' : '' }}"
            placeholder="Contoh: 1"
        >
        @error('nomor_urut')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="nama_ketua" class="form-label">Nama Calon Ketua *</label>
        <input
            type="text"
            name="nama_ketua"
            id="nama_ketua"
            value="{{ old('nama_ketua', $candidate->nama_ketua ?? '') }}"
            required
            class="form-control {{ $errors->has('nama_ketua') ? 'is-invalid' : '' }}"
            placeholder="Nama lengkap calon ketua"
        >
        @error('nama_ketua')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-group">
    <label for="nama_wakil" class="form-label">Nama Calon Wakil (Opsional)</label>
    <input
        type="text"
        name="nama_wakil"
        id="nama_wakil"
        value="{{ old('nama_wakil', $candidate->nama_wakil ?? '') }}"
        class="form-control {{ $errors->has('nama_wakil') ? 'is-invalid' : '' }}"
        placeholder="Nama lengkap calon wakil (kosongkan jika calon tunggal)"
    >
    @error('nama_wakil')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Unggah Foto Tunggal Pasangan Calon -->
<div class="form-group" style="margin-bottom: 22px;">
    <label class="form-label" style="font-weight: 600; font-size: 13.5px; margin-bottom: 6px; display: block;">
        Foto Pasangan Calon
    </label>

    <div class="photo-upload-dropzone" id="photoDropzone" style="border: 2px dashed var(--border-hi); border-radius: 12px; background: #FAFBFC; padding: 22px 18px; transition: all 0.2s ease; cursor: pointer;">
        <input
            type="file"
            name="foto_ketua"
            id="foto_ketua"
            accept="image/png,image/jpeg,image/jpg,image/webp"
            style="display: none;"
        >

        <!-- State: Belum Ada Foto / Kosong -->
        <div id="photoEmptyState" style="{{ ($isEdit && $candidate->foto_ketua) ? 'display:none;' : 'display:flex;' }} flex-direction: column; align-items: center; justify-content: center; text-align: center; gap: 10px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--accent-bg); color: var(--accent); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
            <div>
                <button type="button" class="btn btn-secondary btn-sm" id="btnBrowsePhoto" style="pointer-events: none; margin-bottom: 6px; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Pilih Berkas Foto
                </button>
                <div style="font-size: 12.5px; color: var(--text-sec);">atau seret dan lepas berkas gambar ke area ini</div>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-top: 4px;">
                <span style="font-size: 11.5px; background: #FFFFFF; border: 1px solid var(--border); border-radius: 6px; padding: 3px 8px; color: var(--text-sec); font-weight: 500;">
                    Rasio Saran: 1:1 Persegi (800 × 800 px s/d 2000 × 2000 px)
                </span>
                <span style="font-size: 11.5px; background: #FFFFFF; border: 1px solid var(--border); border-radius: 6px; padding: 3px 8px; color: var(--text-sec); font-weight: 500;">
                    JPG, PNG, WebP (Maks. 2MB)
                </span>
            </div>
        </div>

        <!-- State: Foto Terpilih / Sudah Tersimpan -->
        <div id="photoPreviewState" style="{{ ($isEdit && $candidate->foto_ketua) ? 'display:flex;' : 'display:none;' }} align-items: center; gap: 16px;">
            <div style="width: 84px; height: 84px; border-radius: 10px; overflow: hidden; border: 1px solid var(--border); background: #FFFFFF; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                <img id="photoPreviewImg" src="{{ ($isEdit && $candidate->foto_ketua) ? $candidate->foto_ketua_url : '' }}" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div style="flex: 1; min-width: 0;">
                <div id="photoFileName" style="font-size: 13.5px; font-weight: 600; color: var(--text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ ($isEdit && $candidate->foto_ketua) ? basename($candidate->foto_ketua) : 'Foto Paslon' }}
                </div>
                <div id="photoMeta" style="font-size: 12px; color: var(--text-sec); margin-top: 2px;">
                    {{ ($isEdit && $candidate->foto_ketua) ? 'Foto saat ini tersimpan di server' : '' }}
                </div>
                <div style="margin-top: 8px; display: flex; gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnChangePhoto" style="font-size: 12px; padding: 4px 12px;">
                        Ganti Foto
                    </button>
                    <button type="button" class="btn btn-sm" id="btnCancelPhoto" style="font-size: 12px; padding: 4px 12px; color: var(--err); background: var(--err-bg); border: 1px solid var(--err-bd); display: none;">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    @error('foto_ketua')
        <div class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="visi" class="form-label">Visi Utama</label>
    <textarea
        name="visi"
        id="visi"
        rows="3"
        maxlength="500"
        class="form-control {{ $errors->has('visi') ? 'is-invalid' : '' }}"
        placeholder="Tuliskan visi singkat paslon..."
    >{{ old('visi', $candidate->visi ?? '') }}</textarea>
    <div class="form-text">Maksimal 500 karakter. Visi akan ditampilkan di kartu pemilih.</div>
    @error('visi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="misi" class="form-label">Misi &amp; Program Kerja</label>
    <textarea
        name="misi"
        id="misi"
        rows="5"
        class="form-control {{ $errors->has('misi') ? 'is-invalid' : '' }}"
        placeholder="1. Meningkatkan transparansi...&#10;2. Mewujudkan lingkungan inklusif...&#10;3. Mengembangkan program inovatif..."
    >{{ old('misi', $candidate->misi ?? '') }}</textarea>
    <div class="form-text">Gunakan baris baru untuk memisahkan setiap butir misi.</div>
    @error('misi')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group" style="margin-top:10px;">
    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
        <input
            type="checkbox"
            name="aktif"
            value="1"
            {{ old('aktif', $candidate->aktif ?? true) ? 'checked' : '' }}
            style="width:16px; height:16px; accent-color:var(--accent);"
        >
        <span style="font-size:13.5px; font-weight:500; color:var(--text);">Aktifkan paslon ini agar tampil di bilik suara</span>
    </label>
</div>

<div style="display:flex; justify-content:flex-end; gap:10px; margin-top:28px; padding-top:18px; border-top:1px solid var(--border);">
    <a href="{{ route('admin.candidates.index') }}" class="btn btn-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Paslon' }}</span>
    </button>
</div>

<script>
(function() {
    const dropzone = document.getElementById('photoDropzone');
    const fileInput = document.getElementById('foto_ketua');
    const emptyState = document.getElementById('photoEmptyState');
    const previewState = document.getElementById('photoPreviewState');
    const previewImg = document.getElementById('photoPreviewImg');
    const fileName = document.getElementById('photoFileName');
    const fileMeta = document.getElementById('photoMeta');
    const btnChange = document.getElementById('btnChangePhoto');
    const btnCancel = document.getElementById('btnCancelPhoto');

    if (!dropzone || !fileInput) return;

    const originalSrc = previewImg ? previewImg.getAttribute('src') : '';
    const originalName = fileName ? fileName.textContent.trim() : '';
    const originalMeta = fileMeta ? fileMeta.textContent.trim() : '';
    const hasOriginal = Boolean(originalSrc);

    dropzone.addEventListener('click', function(e) {
        if (e.target.closest('#btnCancelPhoto')) return;
        fileInput.click();
    });

    if (btnChange) {
        btnChange.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });
    }

    if (btnCancel) {
        btnCancel.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.value = '';
            if (hasOriginal) {
                previewImg.src = originalSrc;
                fileName.textContent = originalName;
                fileMeta.textContent = originalMeta;
                btnCancel.style.display = 'none';
            } else {
                previewImg.src = '';
                previewState.style.display = 'none';
                emptyState.style.display = 'flex';
                btnCancel.style.display = 'none';
            }
        });
    }

    // Drag-and-drop support
    ['dragenter', 'dragover'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = 'var(--accent)';
            dropzone.style.background = 'var(--accent-bg)';
        });
    });

    ['dragleave', 'drop'].forEach(function(evt) {
        dropzone.addEventListener(evt, function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.style.borderColor = 'var(--border-hi)';
            dropzone.style.background = '#FAFBFC';
        });
    });

    dropzone.addEventListener('drop', function(e) {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(fileInput.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files.length > 0) {
            handleFiles(fileInput.files[0]);
        }
    });

    function handleFiles(file) {
        if (!file) return;
        if (!file.type.startsWith('image/')) {
            alert('Format berkas tidak didukung. Harap pilih gambar JPG, PNG, atau WebP.');
            fileInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            fileName.textContent = file.name;
            const sizeInKb = (file.size / 1024).toFixed(1);
            fileMeta.textContent = (file.size > 1048576)
                ? (file.size / 1048576).toFixed(2) + ' MB'
                : sizeInKb + ' KB';

            emptyState.style.display = 'none';
            previewState.style.display = 'flex';
            if (btnCancel) btnCancel.style.display = 'inline-flex';
        };
        reader.readAsDataURL(file);
    }
})();
</script>

