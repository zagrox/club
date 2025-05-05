@extends('layouts.app')

@section('title', 'Container Layout')

@section('content')
<div class="container">
  <h4 class="py-3">
    <span class="text-muted fw-light">Layouts /</span> Container
  </h4>

  <div class="card mb-4">
    <h5 class="card-header">Container Layout</h5>
    <div class="card-body">
      <p>Container layout sets a <code>max-width</code> at each responsive breakpoint.</p>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-body">
      <p>
        This is a layout with container. Container layout is the most common layout. It has a responsive width based on the screen size.
        It has a boxed layout with header, content, and footer sections.
      </p>
      <p>
        In this layout, sections are boxed on large screens, and the background color fills the screen. On small screens they will be full width.
      </p>
      <p>
        The content has a min-height to keep the footer at the bottom of the page, with a container class.
        This layout is useful when you want to make the content boxed and limit the width of the content.
      </p>
    </div>
  </div>
</div>
@endsection 