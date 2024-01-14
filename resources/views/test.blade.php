<div>Hi</div>


{{ print_r(['d' => 'g'])}}

{{ $name; }}

<!-- <pre>
{{print_r(\App\Models\Performer::where('is_active', 1)->whereHas('categories', function ($q) {
                $q->where('category.name', 'asian');
            })->limit(5)->get()); }}
</pre> -->
@foreach(\App\Models\Performer::where('is_active', 1)->whereHas('categories', function ($q) {
                $q->where('category.name', 'asian');
            })->limit(5)->get() as $model )
    <div>{{$model->nick}}</div>


@endforeach;

<script>

</script>