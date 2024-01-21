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

    public function test(Request $r, $category_name)
    {

        $cat = Category::where('name', $category_name)->first();

        if($cat) {
            return view('main', ['category_name' => $category_name]);
        }
        return view('404');

    }
}
