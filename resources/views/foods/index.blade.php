@extends('layout')
@section('title', '食材列表')

@section('content')
  <div class="row">
    <div class="col=md-12">
      <div class="box">
        <div class="box-heading text-muted">食材</div>
        <div class="row food-list">
          @foreach ($foods as $item)
            <div class="col-md-3">
            <div class="item">#{{ $item->name }}</div>
            </div>
          @endforeach
        </div>
      </div>
      <div class="list-page d-flex justify-content-center align-items-center">
        {{ $foods->links() }}
      </div>
    </div>
  </div>
@stop
