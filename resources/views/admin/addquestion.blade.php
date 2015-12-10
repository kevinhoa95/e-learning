@extends('layouts.main')
@section('head.title')
ADD QUESTION
@endsection
@section('body.content')
<div class="container">

        {!! Form::open(['name' => 'addQuestionForm', 'url' => '/admin/addquestion/' . $PostID, 'class'=>'form-horizontal', 'files' => true]) !!}
            <div class="col-sm-offset-3">
                <h1>Thêm bài viết mới</h1>
            </div>

            <div class="form-group">
                {!! Form::label('Question','Question : ',['class' => 'col-md-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('Question','',['class'=>'form-control']) !!}
                </div>
            </div>
             <div class="form-group">
                {!! Form::label('Photo', 'Photo : ',['class' => 'col-md-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::file('Photo', ['accept' => 'image/jpeg, image/png, image/gif']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('Description', 'Description : ',['class' => 'col-md-3 control-label']) !!}
                <div class="col-sm-6">
                    {!! Form::text('Description','',['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-6">
                    {!! Form::label('', '',['class' => 'col-md-3 control-label']) !!}
                    {!! Form::label('Error', '',['id' => 'error', 'class' => 'col-md-3 control-label', 'style' => 'display: none;']) !!}
                </div>
            </div>
            <div class="col-sm-offset-3 col-sm-10">
                <script type="text/javascript">
                    function ob(x){
                        return document.getElementById(x);
                    }
                    function submitForm(){
                        var acceptedType = ['image/jpeg', 'image/png', 'image/gif'];
//                        console.log('clicked');
                        var photo = ob('Photo');
                        var type = ob('error').innerHTML = photo.files[0].type;
                        var check = false;
                        for(i = 0; i < acceptedType.length; i++){
                            if (type == acceptedType[i]){
                                check = true;
                                break;
                            }
                        }
                        if (!check){
//                            console.log('not ok');
                            ob('error').style.display = 'block';
                            ob('error').innerHTML = 'Chỉ chọn file ảnh.';
                        }
                        else{
//                            console.log('ok');
                            ob('error').style.display = 'none';
                            document.addQuestionForm.submit();
                        }
//                        ob('error').innerHTML = photo.value;

                    }
                </script>
                {!! Form::button('Thêm',['class' => 'btn btn-default', 'onClick' => 'submitForm()']) !!}
            </div>
        {!! Form::close() !!}
</div>

@endsection