@extends('layouts.app')

@section('title', 'Translation Management')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Admin /</span> Translation Management
  </h4>

  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">Translation Files</h5>
        <div class="card-body">
          <div class="alert alert-info">
            <p>
              <i class="bx bx-info-circle"></i> 
              This page shows all translation files in your application. You can edit translations for all supported languages:
              <strong>{{ implode(', ', $languages) }}</strong>
            </p>
          </div>
          
          @if(session('extraction_stats'))
          <div class="alert alert-success">
            <h6><i class="bx bx-check-circle"></i> Translation Extraction Report</h6>
            <p>
              Successfully extracted <strong>{{ session('extraction_stats')['unique'] }}</strong> unique translatable strings
              across <strong>{{ session('extraction_stats')['languages'] }}</strong> languages.
              Total translations: <strong>{{ session('extraction_stats')['total'] }}</strong>
            </p>
            
            @if(isset(session('extraction_stats')['details']))
            <div class="mt-2">
              <strong>Translation files:</strong>
              <ul class="mb-0">
                @foreach(session('extraction_stats')['details'] as $lang => $files)
                  <li>
                    {{ strtoupper($lang) }}:
                    @foreach($files as $file => $count)
                      <span class="badge bg-label-primary">{{ $file }}.php ({{ $count }})</span>
                    @endforeach
                  </li>
                @endforeach
              </ul>
            </div>
            @endif
          </div>
          @endif
          
          <div class="row">
            <div class="col-md-12">
              <div class="mb-3">
                <a href="{{ url('admin/translations/extract') }}" class="btn btn-primary">
                  <i class="bx bx-refresh me-1"></i> Extract Translations
                </a>
              </div>
              
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th width="50%">File</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($translationFiles as $file)
                      <tr>
                        <td>{{ $file }}.php</td>
                        <td>
                          <a href="{{ url("admin/translations/{$file}") }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-edit-alt"></i> Edit
                          </a>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="2" class="text-center">No translation files found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 