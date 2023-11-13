<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PerformerController extends Controller
{
    public function index(Request $r)
    {
        $per_page = $r->per_page ?? 15;
        $page = ($r->page ?? 1);
        $offset = ($per_page * $page) - $per_page;

        $this->validate($r, [
            'search' => 'nullable|max:255',
            'category' => 'nullable|array',
            'category.*' => 'numeric',
            'types' => 'nullable|array',
            'types.*.types' => 'in:' . implode(',', \App\Models\PerformerData::$types),
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
        $data = $query->with(['data','categories'])->offset($offset)->limit($per_page)->get();
        return $this->success(
            new LengthAwarePaginator($data, $count, $per_page, $page)
        );


        // return $this->fail("error");
    }
}
