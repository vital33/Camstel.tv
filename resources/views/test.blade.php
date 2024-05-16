<div>Hi</div>


<!-- {{ print_r(['d' => 'g'])}}-->

{{ $name; }}
{{ $category_name; }}
<!-- <pre>
{{print_r(\App\Models\Performer::where('is_active', 1)->whereHas('categories', function ($q) {
                $q->where('category.name', 'asian');
            })->limit(5)->get()); }}
</pre> -->
@foreach(\App\Models\Performer::where('is_active', 1)->whereHas('categories', function ($q) use ($category_name){
                $q->where('category.name', $category_name);
            })->limit(15)->get() as $model )
    <div>{{$model->nick}}-{{$model->id}}</div>


@endforeach;

<script>

</script>