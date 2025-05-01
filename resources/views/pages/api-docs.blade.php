@extends('layouts.app')

@section('title', 'API Documentation')

@section('styles')
<style>
  .endpoint {
    border-left: 4px solid #696cff;
    padding-left: 1rem;
    margin-bottom: 2rem;
  }
  
  .endpoint-title {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
  }
  
  .method {
    font-weight: bold;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    margin-right: 0.75rem;
    font-size: 0.875rem;
    color: white;
  }
  
  .method-post {
    background-color: #28c76f;
  }
  
  .method-get {
    background-color: #00cfe8;
  }
  
  .method-delete {
    background-color: #ea5455;
  }
  
  .endpoint-url {
    font-family: monospace;
    font-size: 1rem;
  }
  
  .description {
    color: #697a8d;
    margin-bottom: 0.5rem;
  }
  
  .params-title {
    font-weight: 600;
    margin-top: 0.75rem;
    margin-bottom: 0.25rem;
  }
  
  .params-list {
    list-style-type: none;
    padding-left: 0;
    margin-bottom: 0.75rem;
  }
  
  .param-name {
    font-family: monospace;
    font-weight: 600;
  }
  
  .param-required {
    color: #ea5455;
    font-size: 0.75rem;
    margin-left: 0.25rem;
  }
  
  .param-optional {
    color: #697a8d;
    font-size: 0.75rem;
    margin-left: 0.25rem;
  }
  
  .code-example {
    background-color: #f8f8f8;
    padding: 1rem;
    border-radius: 0.375rem;
    font-family: monospace;
    margin-top: 0.5rem;
    margin-bottom: 1rem;
    overflow-x: auto;
  }
  
  .response-example {
    background-color: #f8f8f8;
    padding: 1rem;
    border-radius: 0.375rem;
    font-family: monospace;
    margin-top: 0.5rem;
    overflow-x: auto;
  }
  
  .tab-content {
    padding-top: 1rem;
  }
</style>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Developer /</span> API Documentation
</h4>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">Authentication</h5>
  </div>
  <div class="card-body">
    <p>
      This API uses token-based authentication with Laravel Sanctum. To access protected endpoints, include the following header in your requests:
    </p>
    <div class="code-example">
      <code>Authorization: Bearer YOUR_ACCESS_TOKEN</code>
    </div>
    
    <div class="alert alert-info">
      <div class="d-flex">
        <i class="bx bx-info-circle fs-3 me-2"></i>
        <div>
          <h6 class="fw-bold mb-1">Token Abilities</h6>
          <p class="mb-0">
            Some endpoints require specific token abilities. You can request tokens with specific abilities when creating them.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">API Endpoints</h5>
  </div>
  <div class="card-body">
    <ul class="nav nav-tabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#auth-endpoints" type="button" role="tab">Authentication</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#user-endpoints" type="button" role="tab">Users</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#token-endpoints" type="button" role="tab">Tokens</button>
      </li>
    </ul>
    
    <div class="tab-content">
      <!-- Authentication Endpoints -->
      <div class="tab-pane fade show active" id="auth-endpoints" role="tabpanel">
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-post">POST</span>
            <span class="endpoint-url">/api/login</span>
          </div>
          <div class="description">Authenticate user and receive access token</div>
          
          <div class="params-title">Parameters:</div>
          <ul class="params-list">
            <li><span class="param-name">email</span> <span class="param-required">required</span> - User's email address</li>
            <li><span class="param-name">password</span> <span class="param-required">required</span> - User's password</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "access_token": "1|laravel_sanctum_wM7y5NnLI2BqEZ...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "email_verified_at": null,
    "created_at": "2025-04-15T10:30:00.000000Z",
    "updated_at": "2025-04-15T10:30:00.000000Z"
  }
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-post">POST</span>
            <span class="endpoint-url">/api/register</span>
          </div>
          <div class="description">Register a new user and receive access token</div>
          
          <div class="params-title">Parameters:</div>
          <ul class="params-list">
            <li><span class="param-name">name</span> <span class="param-required">required</span> - User's full name</li>
            <li><span class="param-name">email</span> <span class="param-required">required</span> - User's email address</li>
            <li><span class="param-name">password</span> <span class="param-required">required</span> - User's password (min 8 characters)</li>
            <li><span class="param-name">password_confirmation</span> <span class="param-required">required</span> - Password confirmation</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "access_token": "2|laravel_sanctum_xH7yQWnLI9BqEZ...",
  "token_type": "Bearer",
  "user": {
    "id": 5,
    "name": "New User",
    "email": "newuser@example.com",
    "created_at": "2025-05-01T14:22:33.000000Z",
    "updated_at": "2025-05-01T14:22:33.000000Z"
  }
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-post">POST</span>
            <span class="endpoint-url">/api/logout</span>
          </div>
          <div class="description">Revoke the current access token</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "message": "Logged out successfully"
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-get">GET</span>
            <span class="endpoint-url">/api/user</span>
          </div>
          <div class="description">Get the authenticated user's details</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "email_verified_at": null,
    "created_at": "2025-04-15T10:30:00.000000Z",
    "updated_at": "2025-04-15T10:30:00.000000Z"
  },
  "roles": ["admin"],
  "permissions": [
    "users.view",
    "users.create",
    "users.edit",
    "users.delete",
    "roles.view",
    "roles.create"
  ]
}</pre>
          </div>
        </div>
      </div>
      
      <!-- User Endpoints -->
      <div class="tab-pane fade" id="user-endpoints" role="tabpanel">
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-get">GET</span>
            <span class="endpoint-url">/api/users</span>
          </div>
          <div class="description">Get a list of all users (requires users.view permission)</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "users": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "created_at": "2025-04-15T10:30:00.000000Z",
      "updated_at": "2025-04-15T10:30:00.000000Z",
      "roles": [
        {
          "id": 9,
          "name": "admin",
          "pivot": { ... }
        }
      ]
    },
    {
      "id": 2,
      "name": "Editor User",
      "email": "editor@example.com",
      "created_at": "2025-04-15T10:30:00.000000Z",
      "updated_at": "2025-04-15T10:30:00.000000Z",
      "roles": [
        {
          "id": 10,
          "name": "editor",
          "pivot": { ... }
        }
      ]
    }
  ]
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-get">GET</span>
            <span class="endpoint-url">/api/users/{user_id}</span>
          </div>
          <div class="description">Get a specific user's details (requires users.view permission)</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "created_at": "2025-04-15T10:30:00.000000Z",
    "updated_at": "2025-04-15T10:30:00.000000Z",
    "roles": [
      {
        "id": 9,
        "name": "admin",
        "pivot": { ... }
      }
    ]
  },
  "roles": ["admin"],
  "permissions": [
    "users.view",
    "users.create",
    "users.edit",
    "users.delete",
    "roles.view",
    "roles.create"
  ]
}</pre>
          </div>
        </div>
      </div>
      
      <!-- Token Endpoints -->
      <div class="tab-pane fade" id="token-endpoints" role="tabpanel">
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-get">GET</span>
            <span class="endpoint-url">/api/tokens</span>
          </div>
          <div class="description">Get a list of all your personal access tokens</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "tokens": [
    {
      "id": 1,
      "tokenable_type": "App\\Models\\User",
      "tokenable_id": 1,
      "name": "Mobile App",
      "abilities": ["*"],
      "last_used_at": "2025-05-01T18:30:00.000000Z",
      "created_at": "2025-05-01T10:30:00.000000Z",
      "updated_at": "2025-05-01T18:30:00.000000Z"
    },
    {
      "id": 2,
      "tokenable_type": "App\\Models\\User",
      "tokenable_id": 1,
      "name": "Third-party Integration",
      "abilities": ["read:users", "read:orders"],
      "last_used_at": null,
      "created_at": "2025-05-01T14:45:00.000000Z",
      "updated_at": "2025-05-01T14:45:00.000000Z"
    }
  ]
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-post">POST</span>
            <span class="endpoint-url">/api/tokens</span>
          </div>
          <div class="description">Create a new personal access token</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="params-title">Parameters:</div>
          <ul class="params-list">
            <li><span class="param-name">name</span> <span class="param-required">required</span> - A name for your token</li>
            <li><span class="param-name">abilities</span> <span class="param-optional">optional</span> - Array of token abilities</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "token": "3|laravel_sanctum_mL9pQRnWI3BzXY...",
  "message": "Token created successfully"
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-delete">DELETE</span>
            <span class="endpoint-url">/api/tokens/{token_id}</span>
          </div>
          <div class="description">Delete a specific personal access token</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "message": "Token deleted successfully"
}</pre>
          </div>
        </div>
        
        <div class="endpoint">
          <div class="endpoint-title">
            <span class="method method-delete">DELETE</span>
            <span class="endpoint-url">/api/tokens</span>
          </div>
          <div class="description">Delete all personal access tokens except the current one</div>
          
          <div class="params-title">Headers:</div>
          <ul class="params-list">
            <li><span class="param-name">Authorization</span> <span class="param-required">required</span> - Bearer token</li>
          </ul>
          
          <div class="response-example">
            <pre>{
  "message": "All tokens deleted successfully"
}</pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  // Any specific JavaScript for this page
</script>
@endsection 