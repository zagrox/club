@extends('layouts.app')

@section('title', 'Tools')

@section('page-css')
<style>
  .tools-card {
    height: 100%;
    transition: all 0.3s ease;
  }
  .tools-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  }
  .card-subtitle {
    color: #697a8d;
    font-weight: 400;
  }
  .achievement-icon {
    height: 100%;
    max-height: 220px;
    object-fit: contain;
  }
  .metric {
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
  }
  .metric-value {
    color: #696cff;
  }
  .action-btn {
    margin-top: 1rem;
  }
  .metric-rocket {
    color: #ff3e1d;
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-6 col-12 mb-4">
    <div class="card tools-card">
      <div class="card-body d-flex flex-column justify-content-between">
        <div class="row">
          <div class="col-8">
            <h5 class="card-title">Congratulations Katie! 🎉</h5>
            <p class="card-subtitle mb-3">Your first campaign has been created!</p>
            
            <div class="metric">
              <span class="metric-value">18%</span> Click rate
            </div>
            <div class="metric">
              <span class="metric-value">35%</span> of Open rate <span class="metric-rocket">🚀</span>
            </div>
            
            <button type="button" class="btn btn-primary action-btn">Create New Campaign</button>
          </div>
          <div class="col-4 text-center">
            <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="achievement-icon" alt="Achievement">
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6 col-12 mb-4">
    <div class="card tools-card">
      <div class="card-body">
        <div class="row">
          <div class="col-8">
            <h5 class="card-title text-primary">Contacts Management</h5>
            <p class="card-subtitle mb-3">You have done <strong>72%</strong> more sales today.</p>
            <p class="card-subtitle mb-3">Check your new badge in your profile.</p>
            
            <button type="button" class="btn btn-outline-primary action-btn">My Audiences</button>
          </div>
          <div class="col-4 text-center">
            <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" class="achievement-icon" alt="Contact Management">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-7 col-12 mb-4">
    <div class="card tools-card">
      <div class="card-body">
        <div class="row">
          <div class="col-8">
            <h5 class="card-title text-primary">Start Design</h5>
            <p class="card-subtitle mb-3">Create your first email template or newsletter signup for with ease and one click by using our wizards and tools</p>
            
            <button type="button" class="btn btn-outline-primary action-btn">New Template</button>
          </div>
          <div class="col-4 text-center">
            <img src="{{ asset('assets/img/illustrations/woman-with-laptop.png') }}" class="achievement-icon" alt="Start Design">
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-5 col-12 mb-4">
    <div class="card tools-card">
      <div class="card-body">
        <div class="row">
          <div class="col-8">
            <h5 class="card-title">Get Reports</h5>
            <p class="card-subtitle mb-3">All Activities log dashboard</p>
            
            <div class="metric">
              <span class="metric-value">1890</span>
            </div>
            <div class="metric">
              <span class="metric-value">75%</span> delivery
            </div>
            
            <button type="button" class="btn btn-info action-btn">Check Reports</button>
          </div>
          <div class="col-4 text-center">
            <img src="{{ asset('assets/img/illustrations/superman.png') }}" class="achievement-icon" alt="Reports">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 