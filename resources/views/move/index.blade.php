@extends('layouts.app')
@section('content')
    <h1>电影座位预定</h1>
    @foreach($seat as $k=>$v)
        @if($v==0)
        <button type="button" class="bttt btn btn-info"  position="{{$k}}">座位{{$k}}</button>
        @else
         <button type="button" class=" bttt btn btn-warning">已预订</button>
        @endif
    @endforeach
@endsection
@section('footer')
    @parent
    <script src="{{ URL::asset('js/move/order.js') }}"></script>
@endsection