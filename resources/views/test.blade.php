


@foreach($models as $model )
    <div class="col"><div class="mod_box_wrap"><a href="/model/{{$model->nick}}"><img class="img-fluid w-100" alt="{{$model->nick}}" src="{{$model->Thumbnail}}">
    <div class="star_rating rating4_half">
<i class="star"></i>
<i class="star"></i>
<i class="star"></i>
<i class="star"></i>
<i class="star"></i>
</div>
<div class="mod_box"><div>{{$model->nick}} {{$model->Stars}}</div><div><div title="{{$model->Country}}" class="flg flg-{{$model->Country}}"></div><div class="separator"></div>{{$model->Age}}</div></div>
@if($model->StatusKey=="pregoldshow"||$model->StatusKey=="goldshow")<div class="gold_show">gold show</div>@endif</div></a></div>


@endforeach

<script>

</script>
