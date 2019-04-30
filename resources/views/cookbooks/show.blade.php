@extends('layout')
@section('title', $cookbook->name)

@section('content')
  <div class="row d-flex justify-content-center">
    <div class="col-md-9">
      <div class="box mb-4">
        <div class="cookbook-detail">
          <h3 class="box-heading">{{ $cookbook->name }}</h3>
          <div class="cover mb-4">
          <img src="{{ $cookbook->cover }}" alt="{{ $cookbook->name }}">
          </div>
          @if ($cookbook->description)
            <h5>简介</h5>
            <p class="text-muted">{{ $cookbook->description }}</p>
          @endif
          @if ($cookbook->tips)
            <h5>小提示</h5>
            <p class="text-muted">{{ $cookbook->tips }}</p>
          @endif
        </div>
      </div>
      @if (count($cookbook->foods))
        <div class="box mb-4">
          <h3 class="box-heading">食材清单</h3>
          <ul class="list-group">
            @foreach ($cookbook->foods as $item)
              <li class="list-group-item">
                <span class="float-right text-muted">{{ $item->number }}</span>
                {{ $item->food->name }}
              </li>
            @endforeach
          </ul>
        </div>
      @endif
      @if (count($cookbook->steps))
        <div class="box mb-4">
          <h3 class="box-heading">做法步骤</h3>

          <div class="step-list">
            @foreach ($cookbook->steps as $item)
              <div class="item mt-3">
                @if ($item->cover)
                  <div class="cover mb-2">
                    <img src="{{ $item->cover }}">
                  </div>
                @endif
                <div class="content">
                <span class="badge badge-primary">{{ $item->order }}</span>
                  {{ $item->content }}
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </div>
@stop
