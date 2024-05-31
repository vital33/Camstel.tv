

@foreach($models as $model )
    <div class="col"><div class="mod_box_wrap"><a href="/model/{{$model->nick}}"><img class="img-fluid w-100" alt="{{$model->nick}}" src="{{$model->Thumbnail}}"><div class="mod_box"><div>{{$model->nick}} {{$model->Rating}}</div><div><div title="{{$model->Country}}" class="flg flg-{{$model->Country}}"></div><div class="separator"></div>{{$model->Age}}</div></div></div></a></div>


@endforeach

<script>

</script>
