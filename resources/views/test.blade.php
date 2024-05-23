<div>Hi</div>


<!-- {{ print_r(['d' => 'g'])}}-->

{{ $name; }}
{{ $category_name }}
<!-- <pre>
{{print_r(\App\Models\Performer::where('is_active', 1)->whereHas('categories', function ($q) {
                $q->where('category.name', 'asian');
            })->limit(5)->get()); }}
</pre> -->


@foreach($models as $model )
    <div><img src="{{$model->Thumbnail}}"> {{$model->nick}}-{{$model->id}}</div>


@endforeach;

<script>

</script>
