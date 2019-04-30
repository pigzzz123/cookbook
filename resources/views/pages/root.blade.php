@extends('layout')
@section('title', '首页')

@section('content')
  <div class="row">
    <div class="col-md-9">
      <div class="box">
        <div class="row cookbook-list">
          @foreach ($cookbooks as $item)
            <div class="col-md-4">
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
        {{ $cookbooks->appends(['query' => $query])->links() }}
      </div>
    </div>
    <div class="col-md-3">
      <div class="box mb-3">
        <div class="box-heading text-muted">食材</div>
        <div class="food-list">
          @foreach ($foods as $item)
            <div class="item">#{{ $item->name }}</div>
          @endforeach
        </div>
        <div class="text-right">
        <a href="{{ route('foods') }}">查看更多</a>
        </div>
      </div>
    </div>
  </div>
@stop
