@extends('layout')
@section('title', '菜谱分类')

@section('content')
  <div class="row">
    <div class="col-md-12">
      @foreach ($categories as $item)
        <div class="box mb-3">
          <div class="box-heading">{{ $item->name }}</div>
          <div class="box-body">
            <div class="category-list">
              @foreach ($item->children as $child)
            <a href="{{ route('categories.show', $child->id) }}" class="btn btn-sm btn-outline-primary mr-1 mb-2">{{ $child->name }}</a>
              @endforeach
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
@stop
