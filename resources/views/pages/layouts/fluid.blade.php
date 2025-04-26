@extends('layouts.app')

@section('title', 'Fluid Layout')

@section('content')
<h4 class="py-3">
  <span class="text-muted fw-light">Layouts /</span> Fluid
</h4>

<div class="card mb-4">
  <h5 class="card-header">Fluid Layout</h5>
  <div class="card-body">
    <p>
      A fluid layout is a layout that spans the entire width of the viewport. It's helpful for admin dashboards, data tables, and other types of layouts where you need to display a large amount of data.
    </p>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <p>
      In this layout, the container is fluid, which means it will take the full width of the screen. This is contrary to the boxed layout where the container has specific width at each responsive breakpoint.
    </p>
    <p>
      The fluid container is useful when you want to use the full width of the screen irrespective of the responsive breakpoint.
    </p>
    <p>
      By using a fluid layout, you ensure that your interface can display as much information as possible at once. However, on large screens, you might want to be careful with line lengths as they can become quite long, which may decrease readability.
    </p>
  </div>
</div>
@endsection 