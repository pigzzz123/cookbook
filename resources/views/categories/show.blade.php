@extends('layout')
@section('title', $category->name)

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-heading">{{ $category->name }}</div>
        <div class="row cookbook-list">
          @foreach ($cookbooks as $item)
            <div class="col-md-3">
              <div class="card mb-4">
                <a href="{{ route('cookbooks.show', $item->id) }}" title="{{ $item->name}}"><img class="card-img-top" src="{{ $item->cover ?? asset('images/nopic.jpg') }}" alt="{{ $item->name}}"></a>
                <div class="card-body">
                <a href="{{ route('cookbooks.show', $item->id) }}" class="card-title h4">{{ $item->name}}</a>
                  <div class="tags">
                    @foreach ($item->foods as $food)
                      <span class="badge badge-primary">{{ $food->food->name }}</span>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="list-page d-flex justify-content-center align-items-center">
        {{ $cookbooks->links() }}
      </div>
    </div>
  </div>
@stop
