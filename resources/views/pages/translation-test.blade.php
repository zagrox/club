@extends('layouts.app')

@section('title', 'Translation Test')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-md-12">
      <div class="card mb-4">
        <h5 class="card-header">{{ __('messages.language') }} {{ __('messages.settings') }}</h5>
        <div class="card-body">
          <div class="mb-3">
            <h2>{{ __('messages.welcome') }}</h2>
            <p>{{ __('messages.current_language') }}: <strong>{{ App::getLocale() }}</strong></p>
          </div>
          
          <div class="mb-3">
            <h4>{{ __('messages.available_languages') }}:</h4>
            <ul class="list-group">
              @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <strong>{{ $properties['native'] }}</strong>
                    <span class="text-muted">({{ $properties['name'] }})</span>
                  </div>
                  <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" 
                    class="btn btn-sm {{ App::getLocale() == $localeCode ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ App::getLocale() == $localeCode ? __('messages.current') : __('messages.switch') }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
          
          <div class="mt-4">
            <h4>{{ __('messages.translation_examples') }}:</h4>
            <table class="table">
              <thead>
                <tr>
                  <th>{{ __('messages.key') }}</th>
                  <th>{{ __('messages.translation') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>messages.home</td>
                  <td>{{ __('messages.home') }}</td>
                </tr>
                <tr>
                  <td>messages.about</td>
                  <td>{{ __('messages.about') }}</td>
                </tr>
                <tr>
                  <td>messages.contact</td>
                  <td>{{ __('messages.contact') }}</td>
                </tr>
                <tr>
                  <td>messages.login</td>
                  <td>{{ __('messages.login') }}</td>
                </tr>
                <tr>
                  <td>messages.register</td>
                  <td>{{ __('messages.register') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 