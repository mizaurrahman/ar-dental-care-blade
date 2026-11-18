@php
    $id = $id ?? ($name ?? 'phone');
    $label = $label ?? 'Phone';
    $name = $name ?? null;
    $required = (bool) ($required ?? false);
    $placeholder = $placeholder ?? '1XXXXXXXXX';
    $wireModel = $wireModel ?? null;
    $class = $class ?? '';
    $rawValue = $value ?? '';
    $display = \App\Support\BdPhone::subscriber((string) $rawValue) ?? (string) $rawValue;
@endphp
@if ($label !== false)
    <label class="label" @if($id) for="{{ $id }}" @endif>{{ $label }}@if($required) <span class="text-red-500">*</span>@endif</label>
@endif
<div class="phone-input flex overflow-hidden rounded-[10px] border border-[var(--line)] bg-white transition focus-within:border-[var(--blue)] focus-within:shadow-[0_0_0_3px_rgba(47,111,237,0.18)] {{ $class }}">
    <span class="flex shrink-0 items-center border-r border-[var(--line)] bg-slate-100 px-3 text-sm font-semibold text-slate-500 select-none" title="Country code" aria-hidden="true">+880</span>
    <input
        type="tel"
        inputmode="numeric"
        autocomplete="tel-national"
        class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2.5 text-[0.9rem] outline-none"
        placeholder="{{ $placeholder }}"
        maxlength="10"
        @if($id) id="{{ $id }}" @endif
        @if($required) required @endif
        @if($wireModel)
            wire:model="{{ $wireModel }}"
        @else
            @if($name) name="{{ $name }}" @endif
            value="{{ $name ? old($name, $display) : $display }}"
        @endif
    >
</div>
