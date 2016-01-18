@extends('layouts.main')
@section('head.title')
    Question {{$Question['id']}}
@endsection
@section('body.content')
    <h2 class="title">Câu hỏi: {{$Question['Question']}}</h2>
    @if ($Question['FormatID'] == 1)
        @if ($Question['Photo'] != null)
            <li class="list-group-item ">
                <img class= "img-responsive" src="{{'/images/imageQuestion/' . $Question['Photo']}}"/>
            </li>
        @endif
    @elseif($Question['FormatID'] == 2)
        @if ($Question['Video'] != null)
            <div class="embed-responsive embed-responsive-4by3">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{$Question['Video']}}" frameborder="0" allowfullscreen></iframe>
            </div>
        @endif
    @endif

    <h2 class="title">{{$Question['Description']}}</h2>
    <h2 class="title">Đáp án</h2>
    <ul class="list-group">
        @foreach($Answers as $a)
          <li class="list-group-item list-group-item-info">
              {!! $a['Detail'] !!}
          </li>
          <div class="clear"></div>
        @endforeach
    </ul>
    @if ((auth()->user()) && (auth()->user()->admin == 1))
    <a class="btn btn-primary col-xs-12" href="{{route('question.edit', $Question['id'])}}">Sửa câu hỏi</a>
    <a class="btn btn-primary col-xs-12" href="{{route('answer.edit', $Question['id'])}}">Sửa đáp án</a>
    <a class="btn btn-primary col-xs-12" href="{{route('user.viewpost',$Question['PostID'])}}">Quay lại bài đăng</a>

    <a class="col-xs-12 btn btn-danger" data-toggle="modal" href='#modal-id'>Xóa câu hỏi này</a>
        <div class="modal fade" id="modal-id">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Cảnh báo:</h4>
                    </div>
                    <div class="modal-body">
                        <h6>Xác nhận xóa?</h6>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <a class ="btn btn-primary" href="{{route('admin.destroyquestion',$Question['id'])}}">Xóa</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection