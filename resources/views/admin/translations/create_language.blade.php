@extends('layouts.app')

@section('title', 'Add New Language')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="{{ route('translations.index') }}">Translations</a> / <a href="{{ route('translations.languages') }}">Languages</a> /</span> Add New Language
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Add New Language</span>
          <a href="{{ route('translations.languages') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> Back to Languages
          </a>
        </h5>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        
          <form action="{{ route('translations.store_language') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="code" class="form-label">Language Code <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="en, es, fr, de, etc." required>
                  <div class="form-text">Use ISO 639-1 two-letter language code (e.g., 'en', 'fr') or add regional variant (e.g., 'en-GB').</div>
                  @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="name" class="form-label">Language Name (English) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="English, Spanish, French, etc." required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="native" class="form-label">Native Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('native') is-invalid @enderror" id="native" name="native" value="{{ old('native') }}" placeholder="English, Español, Français, etc." required>
                  <div class="form-text">The language name as written in the language itself.</div>
                  @error('native')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_rtl" name="is_rtl" {{ old('is_rtl') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_rtl">Right-to-Left (RTL) Language</label>
                  </div>
                  <div class="form-text">Enable for languages like Arabic, Hebrew, Persian, etc.</div>
                </div>
                
                <div class="mb-3">
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                  </div>
                  <div class="form-text">Inactive languages won't be shown in the language selector.</div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card mb-4 shadow-none border">
                  <div class="card-header">
                    <h5 class="mb-0">Instructions</h5>
                  </div>
                  <div class="card-body">
                    <p>Adding a new language will:</p>
                    <ul>
                      <li>Create a language directory in the <code>resources/lang</code> folder</li>
                      <li>Copy all translation files from English</li>
                      <li>Mark all strings as untranslated in the new language</li>
                      <li>Add the language to the system settings</li>
                      <li>Make it available in the language selector (if active)</li>
                    </ul>
                    
                    <div class="alert alert-info mt-3">
                      <p class="mb-0"><strong>Common Language Codes:</strong></p>
                      <div class="row">
                        <div class="col-6">
                          <ul class="mb-0">
                            <li>English: <code>en</code></li>
                            <li>Spanish: <code>es</code></li>
                            <li>French: <code>fr</code></li>
                            <li>German: <code>de</code></li>
                            <li>Italian: <code>it</code></li>
                          </ul>
                        </div>
                        <div class="col-6">
                          <ul class="mb-0">
                            <li>Arabic: <code>ar</code> (RTL)</li>
                            <li>Chinese: <code>zh</code></li>
                            <li>Russian: <code>ru</code></li>
                            <li>Japanese: <code>ja</code></li>
                            <li>Persian: <code>fa</code> (RTL)</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-plus-circle me-1"></i> Add Language
              </button>
              <a href="{{ route('translations.languages') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 