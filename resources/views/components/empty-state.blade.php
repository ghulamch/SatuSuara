@props([
    'title' => 'Belum Ada Data',
    'description' => null,
])

<div class="empty-state" style="text-align:center; padding:48px 20px;">
    @isset($icon)
        <div style="width:52px; height:52px; border-radius:50%; background:var(--accent-bg); color:var(--accent); display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
            {{ $icon }}
        </div>
    @endisset

    <h3 style="font-size:16px; font-weight:600; color:var(--text); margin-bottom:6px;">
        {{ $title }}
    </h3>

    @if ($description)
        <p style="font-size:13.5px; color:var(--text-sec); margin-bottom:18px; max-width:440px; margin-left:auto; margin-right:auto;">
            {{ $description }}
        </p>
    @endif

    @if ($slot->isNotEmpty())
        <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap; margin-top:14px;">
            {{ $slot }}
        </div>
    @endif
</div>

