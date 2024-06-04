<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PerformerController extends Controller
{
    public function index(Request $r)
    {

        //  var settings = {
        //   "url": "localhost:8000/api/performer",
        //   "method": "GET",
        //   "timeout": 0,
        //   "headers": {
        //     "Content-Type": "application/json",
        //     "Cookie": "x-clockwork=%7B%22requestId%22%3A%221703992478-2145-614230562%22%2C%22version%22%3A%225.1.12%22%2C%22path%22%3A%22%5C%2F__clockwork%5C%2F%22%2C%22webPath%22%3A%22%5C%2Fclockwork%5C%2Fapp%22%2C%22token%22%3A%2216ae2349%22%2C%22metrics%22%3Atrue%2C%22toolbar%22%3Atrue%7D"
        //   },
        //   "data": JSON.stringify({
        //     "search": "tes",
        //     "category": [
        //       1,
        //       2,
        //       4
        //     ],
        //     "types": [
        //       {
        //         "type": "Age",
        //         "value": 26
        //       },
        //       {
        //         "type": "HD",
        //         "value": 1
        //       }
        //     ]
        //   }),
        // };

        // $.ajax(settings).done(function (response) {
        //   console.log(response);
        // });

        $per_page = $r->per_page ?? 35;
        $page = ($r->page ?? 1);
        $offset = ($per_page * $page) - $per_page;

        $this->validate($r, [
            'search' => 'nullable|max:255',
            'category' => 'nullable|array',
            'category.*' => 'numeric',
            'types' => 'nullable|array',
            'types.*.type' => 'in:' . implode(',', \App\Models\PerformerData::$types),
            'types.*.value' => 'max:255|numeric',
        ]);

        $search_params = $r->only(['search', 'category', 'types']);

        $query = \App\Models\Performer::where('is_active', 1);

        if(isset($search_params['search'])) {
            $query->where('nick', "like", "%" . escape_like($search_params['search']) . "%");
        }
        if(isset($search_params['category'])) {
            $query->whereHas('categories', function ($q) use ($search_params) {
                $q->whereIn('category.id', $search_params['category']);
            });
        }
        if(isset($search_params['types'])) {
            $query->whereHas('data', function ($q) use ($search_params) {
                $count = 0;
                foreach($search_params['types'] as $v) {
                    if(!$count) {
                        $q->where(function ($_q) use ($v) {
                            $_q->where('model_data.type', $v['type'])->where('model_data.value', $v['value']);
                        });
                    } else {
                        $q->orWhere(function ($_q) use ($v) {
                            $_q->where('model_data.type', $v['type'])->where('model_data.value', $v['value']);
                        });
                    }
                    $count++;
                }
            });
        }


        $count = $query->count('model.id');

        $data = $query->orderBy('external_sort_order')->with(['data' => function ($q) {
            $q->whereIn('type', ['Age', 'HD']);
        },'categories'])->offset($offset)->limit($per_page)->get();
        return $this->success(
            new LengthAwarePaginator($data, $count, $per_page, $page)
        );
    }

    public function public(Request $r, $category_name = null)
    {

        $per_page = $r->per_page ?? 15;
        $page = ($r->page ?? 1);
        $offset = ($per_page * $page) - $per_page;

        $this->validate($r, [
            'search' => 'nullable|max:255',
            'category' => 'nullable|array',
            'category.*' => 'numeric',
            'types' => 'nullable|array',
            'types.*.type' => 'in:' . implode(',', \App\Models\PerformerData::$types),
            'types.*.value' => 'max:255|numeric',
        ]);

        if($category_name && !\App\Models\Category::where('name', $category_name)->first()) {
            return view('404', ['category_name' => $category_name, 'error' => "Category not found"]);
        }

        $selects = ["model_id"];
        foreach(["Age", "Gender", "Stars", "Country", "Thumbnail", "GoldShow", "PreGoldSHow"] as $field) {
            $selects[] = sprintf(' max(case when type = "%s" then value end) AS %s', $field, $field);
        }

        $mdSub = \DB::table('model_data')
            ->selectRaw(implode(",", $selects))
            ->groupBy('model_id');

        $models = \App\Models\Performer::leftJoinSub($mdSub, 'md', function ($join) {
            $join->on('md.model_id', '=', 'model.id');
        })->where('is_active', 1)
            ->whereHas('categories', function ($q) use ($category_name) {
                if($category_name) {
                    $q->where('category.name', $category_name);
                }
            })->select('model.*', 'md.*')->limit($per_page)->get();

        return view('main', ['category_name' => $category_name, 'models' => $models]);
    }

    public function test(Request $r, $category_name)
    {

        $cat = Category::where('name', $category_name)->first();
        if($cat) {
            return view('category', ['category_name' => $category_name]);
        }
        return view('404');

    }

    public function model(Request $r, $model_name)
    {

        $model = Category::where('name', $model_name)->first();
        if($model) {
            return view('model', ['model_name' => $model_name]);
        }
        return view('404');

    }
}
