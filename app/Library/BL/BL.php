<?php

namespace App\Library\BL;

use App\Library\BL\AuthSdk;
use Exception;

// $a = new AuthSdk('824d7852-8c17-4725-82c8-eb26b52c99fb', 'SZP89Sc3lte3pdVZKw4lHpbo645SG3tZZ2FaoJGs');
// $url = "https://blacklabel.naiadsystems.com/v3/search/get-list";
// $url2 = 'https://blacklabel.naiadsystems.com/v1/performer/details';


// $params = [
//   "language" => "en",
//   //"country" => "GB",
//   "size" => 500,
//   "from" => 0,
//   //"page_number" => 1,
//   //"results_per_page" => 500,
//   "new_models" => false,
//   "filters" => "age:21-25;gender:f;build:average,curvaceous"
// ];


class BL
{
    private $params = [
        "from" => 0,
        "size" => 10000,
    ];

    private $offset = 0;

    private $instance;

    public const LIST_URL = "https://blacklabel.naiadsystems.com/v3/search/get-list";
    public const DETAILS_URL = 'https://blacklabel.naiadsystems.com/v1/performer/details';
    public const APP_ID = "824d7852-8c17-4725-82c8-eb26b52c99fb";
    public const TOKEN = "SZP89Sc3lte3pdVZKw4lHpbo645SG3tZZ2FaoJGs";

    public const SM_OK = "SM_OK";

    public function __construct($params = [])
    {
        $this->params = array_merge($this->params, $params);
        $this->instance = new AuthSdk(self::APP_ID, self::TOKEN);
        ini_set('memory_limit', '256M');
    }
    //private
    private function getListData()
    {
        return $this->instance->post(self::LIST_URL, $this->params);
    }

    private function mapValue($key, $raw_value)
    {
        $map = [
            'Gender' => [
                [
                    'value' => "female",
                    'alias' => ['f', 'female', 'Female']
                ],
                [
                    'value' => "male",
                    'alias' => ['m', 'Male', 'male']
                ]
            ],
        ];

        if (isset($map[$key])) {
            if ($value = current(array_filter($map[$key], function ($i) use ($raw_value) {
                return in_array($raw_value, $i['alias']);
            }))) {
                return $value['value'];
            }
        }

        if (filter_var($raw_value, FILTER_VALIDATE_BOOLEAN)) {
            return boolval($raw_value) ? 1 : 0;
        }

        return is_array($raw_value) || is_object($raw_value) ? json_encode($raw_value) : htmlspecialchars(stripslashes($raw_value));
    }

    public function upsertModelData($vendor_id = null)
    {
        try {
            $now = date('Y-m-d H:i:s');

            $list = $this->getListData();

            $model_categories = [];
            $model_data = [];
            $all_categories = [];

            if (isset($list['status']) && $list['status'] == self::SM_OK) {
                try {

                    $values = [];

                    $columns = ["nick", "external_id", "created_at", "updated_at", "vendor_id", "external_sort_order"];

                    $keys = !empty($list['Results'][0]) ? array_keys($list['Results'][0]) : [];

                    foreach ($list['Results'] as $index => $r) {

                        $values = array_merge(
                            $values,
                            [
                                $r['Nickname'], $r['PerformerId'], "$now", "$now", $vendor_id, $index
                            ]
                        );

                        $model_categories[] = [
                            'nick' => $r['Nickname'],
                            'categories' => $r['Categories'] // [2,4,5]
                        ];
                        // var_dump($keys);
                        $model_data[$r['Nickname']] = array_reduce($keys, function ($acc, $k) use ($r) {
                            if (!in_array($k, ['Nickname', 'PerformerId', 'CategoryName', 'Categories', "Languages"])) {
                                $acc[$k] = $this->mapValue($k, $r[$k]);
                            }
                            return $acc;
                        });

                        foreach (["Languages"] as $k) {
                            if (in_array($k, $keys)) {
                                if (is_array($r[$k])) {
                                    foreach ($r[$k] as $v) {
                                        $model_data[$r['Nickname']][] = [$k => $v];
                                    }
                                }
                            }
                        }

                        $all_categories = array_merge($all_categories, array_combine($r['CategoryName'], $r['Categories'])); // ['1' => "cat_name"]

                    }

                    $stored_models = null;

                    if (count($list['Results'])) {

                        $rowPlaces = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
                        $placeholder = implode(', ', array_fill(0, count($list['Results']), $rowPlaces));

                        $start = microtime(1);

                        $sql = "INSERT INTO `model` (" . implode(',', $columns) . ") VALUES $placeholder ON DUPLICATE KEY UPDATE `updated_at`=\"$now\"";

                        $result = \DB::statement($sql, $values);

                        if (!$result) {
                            return ['success' => false, 'message' => "Insert Error"];
                        }

                        /**
                         *
                         */

                        $stored_models = $this->getModels(array_keys($model_data));

                        if(count($stored_models)) {
                            \DB::table('model')
                                ->whereNotIn('nick', array_keys($model_data))
                                ->update(['external_sort_order' => null]);
                        }

                        $values = [];
                        $columns = ['model_id', 'type', 'value'];
                        $rowPlaces = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';

                        try {

                            $model_nicks = array_column($stored_models, 'nick');

                            foreach ($model_data as $mc => $value) {

                                $idx = array_search($mc, $model_nicks);

                                if ($idx !== false) {

                                    foreach ($value as $key => $val) {
                                        if (is_array($val)) {
                                            foreach ($val as $k => $v) {
                                                $a =  sprintf("(%d, \"%s\", \"%s\")", $stored_models[$idx]['id'], $k, $v);
                                                $values[] = $a;
                                            }
                                        } else {
                                            $a =  sprintf("(%d, \"%s\", \"%s\")", $stored_models[$idx]['id'], $key, $val);
                                            $values[] = $a;
                                        }

                                    }
                                }
                            }
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }

                        $placeholder = implode(', ', array_fill(0, count($values), $rowPlaces));

                        $sql = "INSERT INTO `model_data` (" . implode(',', $columns) . ") VALUES " . implode(',', $values) . "ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), updated_at = NOW()";

                        $result = \DB::statement($sql);

                        if (!$result) {
                            return ['success' => false, 'message' => "Model Data Insert Error"];
                        }
                    }

                    unset($values);
                    $this->upsertCategories($all_categories);
                    try {
                        $this->syncCategories($model_categories, $stored_models);
                    } catch (Exception $e) {
                    }

                    return true;
                } catch (\PDOException $e) {
                    var_dump($e->getCode());
                }
            }

            return (array)$list;
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * @params array categories ['']
     */
    private function upsertCategories($all_categories)
    {

        $columns = ['external_id', 'name'];
        $rowPlaces = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $placeholder = implode(', ', array_fill(0, count($all_categories), $rowPlaces));


        $values = [];

        foreach ($all_categories as $cat_name => $ext_id) {

            $values = array_merge(
                $values,
                [
                    "$ext_id", "$cat_name"
                ]
            );
        }

        $sql = "INSERT IGNORE INTO `category` (" . implode(',', $columns) . ") VALUES $placeholder";

        $result = \DB::statement($sql, $values);

        if (empty($result)) {
            return ['success' => false, 'message' => "Category Upsert Error"];
        } else {
            return true;
        }
    }

    private function syncCategories($model_categories, $stored_models)
    {

        $columns = ['model_id', 'category_id'];
        $existing_categories = $this->getCategories();
        $models = $stored_models;

        $values = [];

        try {

            $external_ids = array_column($existing_categories, 'external_id');
            $model_nicks = array_column($models, 'nick');

            foreach ($model_categories as $mc) {

                $idx = array_search($mc['nick'], $model_nicks);

                foreach ($mc['categories'] as $ext_id) {
                    if (($cat_idx = array_search($ext_id, $external_ids)) !== false) {
                        $values[] = '("' . $models[$idx]['id'] . '", "' . $existing_categories[$cat_idx]['id'] . '")';
                    }
                }
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }

        $sql = "INSERT IGNORE INTO `model_to_category` (" . implode(',', $columns) . ") VALUES " . implode(',', $values) . "";
        $result = \DB::statement($sql);

        if (!$result) {
            return ['success' => false, 'message' => "Model To Category Insert Error"];
        } else {
            return true;
        }
    }

    private function getModels($nicknames = [])
    {
        if (!empty($nicknames)) {
            return \App\Models\Performer::whereIn('nick', $nicknames)->get()->toArray();
        } else {
            return \App\Models\Performer::get()->toArray();
        }
    }

    private function getCategories()
    {
        return \App\Models\Category::all()->toArray();
    }
}
