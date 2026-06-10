@extends('layout')

@section('title')
    | Admin Settings
@endsection

@section('content')
<div class="container py-4" style="max-width:700px">
    @include('admin.partials.nav')

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <h4 class="fw-semibold mb-4">Site Settings</h4>

    <form method="POST" action="/admin/settings" class="dl-form-panel">
        @csrf
        <div class="mb-3">
            <label class="form-label">Site Name</label>
            <input type="text" class="form-control" name="site_name" value="{{ $settings['site_name'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Tagline</label>
            <input type="text" class="form-control" name="site_tagline" value="{{ $settings['site_tagline'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Support Email</label>
            <input type="email" class="form-control" name="support_email" value="{{ $settings['support_email'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Hero Title (Homepage)</label>
            <input type="text" class="form-control" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Hero Subtitle</label>
            <textarea class="form-control" name="hero_subtitle" rows="2">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Allow Registration</label>
            <select class="form-select" name="allow_registration">
                <option value="1" {{ ($settings['allow_registration'] ?? '1') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ ($settings['allow_registration'] ?? '1') == '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Maintenance Mode</label>
            <select class="form-select" name="maintenance_mode">
                <option value="0" {{ ($settings['maintenance_mode'] ?? '0') == '0' ? 'selected' : '' }}>Off</option>
                <option value="1" {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'selected' : '' }}>On</option>
            </select>
        </div>
        <button class="regbtn w-100">Save Settings</button>
    </form>
</div>
@endsection
