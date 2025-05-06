@extends('layouts.app')

@section('title', 'Edit Language')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin / <a href="{{ route('translations.index') }}">Translations</a> / <a href="{{ route('translations.languages') }}">Languages</a> /</span> Edit Language
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header d-flex justify-content-between align-items-center">
          <span>Edit Language: {{ $language->name }}</span>
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
          
          @if($language->code === 'en')
            <div class="alert alert-warning">
              <p class="mb-0">
                <i class="bx bx-info-circle"></i> 
                English is the default language and some settings cannot be changed.
              </p>
            </div>
          @endif
        
          <form action="{{ route('translations.update_language', $language->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="code" class="form-label">Language Code <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $language->code) }}" placeholder="en, es, fr, de, etc." required {{ $language->code === 'en' ? 'readonly' : '' }}>
                  <div class="form-text">Use ISO 639-1 two-letter language code (e.g., 'en', 'fr') or add regional variant (e.g., 'en-GB').</div>
                  @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="name" class="form-label">Language Name (English) <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $language->name) }}" placeholder="English, Spanish, French, etc." required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <label for="native" class="form-label">Native Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('native') is-invalid @enderror" id="native" name="native" value="{{ old('native', $language->native) }}" placeholder="English, Español, Français, etc." required>
                  <div class="form-text">The language name as written in the language itself.</div>
                  @error('native')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                
                <div class="mb-3">
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_rtl" name="is_rtl" {{ old('is_rtl', $language->is_rtl) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_rtl">Right-to-Left (RTL) Language</label>
                  </div>
                  <div class="form-text">Enable for languages like Arabic, Hebrew, Persian, etc.</div>
                </div>
                
                <div class="mb-3">
                  <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $language->is_active) ? 'checked' : '' }} {{ $language->code === 'en' ? 'disabled' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                  </div>
                  <div class="form-text">Inactive languages won't be shown in the language selector.</div>
                  @if($language->code === 'en')
                    <input type="hidden" name="is_active" value="1">
                    <div class="form-text text-warning">English is the default language and cannot be deactivated.</div>
                  @endif
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card mb-4 shadow-none border">
                  <div class="card-header">
                    <h5 class="mb-0">Language Information</h5>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <p><strong>Created:</strong> {{ $language->created_at->format('F j, Y, g:i a') }}</p>
                      <p><strong>Last Updated:</strong> {{ $language->updated_at->format('F j, Y, g:i a') }}</p>
                    </div>
                    
                    <div class="alert alert-info">
                      <p class="mb-1"><strong>Tips when changing language settings:</strong></p>
                      <ul class="mb-0">
                        <li>Changing the language code will move all translation files</li>
                        <li>Deactivating a language will hide it from users but keep the files</li>
                        <li>Setting a language as RTL will apply right-to-left styling</li>
                      </ul>
                    </div>
                    
                    @if($language->code !== 'en')
                      <div class="alert alert-danger mt-3">
                        <p class="mb-0"><strong>Danger Zone:</strong></p>
                        <p class="mb-0">
                          <a href="#" onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this language? All translations will be lost. This action cannot be undone.')) document.getElementById('delete-language-form').submit();" class="alert-link text-danger">
                            <i class="bx bx-trash"></i> Delete this language
                          </a>
                        </p>
                        <form id="delete-language-form" action="{{ route('translations.delete_language', $language->id) }}" method="POST" class="d-none">
                          @csrf
                          @method('DELETE')
                        </form>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-save me-1"></i> Update Language
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