<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CONSTANTS;
use App\Http\Models\Bookmarks;
use App\Http\Models\CheckIns;
use App\Http\Models\Cms\CategoriesCms;
use App\Http\Models\Cms\RestaurantsCategoryCms;
use App\Http\Models\Cms\RestaurantsCms;
use App\Http\Models\Cms\RestaurantsSuggestCms;
use App\Http\Models\Cms\PhotosCms;
use App\Http\Models\Restaurants;
use App\Http\Models\RestaurantsCategory;
use App\Http\Models\Reviews;
use App\Http\Models\RestaurantsSuggest;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class RestaurantCmsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {

    }

    /**
     * Displays index page
     *
     * @return \Illuminate\View\View
     */
    public function indexAction()
    {
        $data = array();
        $restaurants = Restaurants::all();
        $data = array(
            'restaurants' => ($restaurants->count()) ? $restaurants : array(),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'restaurant'
            ),
            'page_title' => 'Restaurants'
        );
        return view('cms.restaurant.index', $data);
    }

    /**
     * Display info of a restaurant
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function viewAction($id)
    {
        $data = array();

        if (!$id) {
            $data['error'] = 'Invalid ID';
        } else {
            $data['id'] = $id;
        }

        $restaurant = Restaurants::find($id);

        if ($restaurant) {
            $restaurant = $restaurant->toArray();
            $boolean_columns = array(
                'can_deliver',
                'can_dinein',
                'can_dineout',
                'is_24hours',
                'smoking',
                'credit_card',
                'status_close',
                'status_verify'
            );

            foreach ($restaurant as $key => $value) {
                if (in_array($key, $boolean_columns)) {
                    if ($value === 1) {
                        $restaurant[$key] = 'Yes';
                    } elseif ($value === 0) {
                        $restaurant[$key] = 'No';
                    }
                }
            }

            $data['restaurant'] = $restaurant;
        } else {
            $data['error'] = 'Restaurant found';
        }

        $data['page_title'] = $restaurant['name'];
        return view('cms.restaurant.view', $data);
    }

    /**
     * Edit a restaurant
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function editAction($id, Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = array();
            $restaurant = Restaurants::find($id);
            $data = array(
                'restaurant' => ($restaurant) ? $restaurant : array(),
                'id' => $id,
                'javascripts' => array(
                    'restaurant'
                ),
                'page_title' => 'Edit Restaurant'
            );
            return view('cms.restaurant.edit', $data);
        } elseif ($request->isMethod('POST')) {
            extract($request->all());
            $errors = array();

            if (!$restaurant_name) {
                $errors[] = 'Restaurant name is missing';
            }

            if (!$restaurant_address) {
                $errors[] = 'Restaurant address is missing';
            }

            if (!$restaurant_telephone) {
                $errors[] = 'Phone number is missing';
            }

            if (!$restaurant_budget) {
                $errors[] = 'Budget is missing';
            }

            if ($restaurant_budget && !is_numeric($restaurant_budget)) {
                $errors[] = 'Budget is invalid';
            }

            if (!$restaurant_latitude) {
                $errors[] = 'Latitude is missing';
            }

            if ($restaurant_latitude && !is_numeric($restaurant_latitude)) {
                $errors[] = 'Latitude is invalid';
            }

            if (!$restaurant_longitude) {
                $errors[] = 'Longitude is missing';
            }

            if ($restaurant_longitude && !is_numeric($restaurant_longitude)) {
                $errors[] = 'Longitude is invalid';
            }

            if ($errors) {
                \Session::flash('errors', $errors);
                return redirect('cms/restaurant/edit/' . $id);
            }

            try {
                $restaurant = new RestaurantsCms();
                $restaurant->editRestaurant(
                    $id,
                    $restaurant_name,
                    $restaurant_address,
                    $restaurant_telephone,
                    $restaurant_budget,
                    $restaurant_latitude,
                    $restaurant_longitude
                );
                \Session::flash('success', 'Restaurant has been updated');
                return redirect('cms/restaurant/view/' . $id);
            } catch (Exception $e) {
                \Session::flash('errors', array('An unexpected error occured while trying to update restaurant.'));
                return redirect('cms/restaurant/edit/' . $id);
            }
        }
    }

    /**
     * Checks TSV file for errors and displays them when found
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function convertCheckerAction(Request $request)
    {
        $success = array();
        $errors = array();

        if ($request->isMethod('post')) {
            $connection = null;

            try {
                $uploaded_files = \Input::file('convert');
                $columns = Schema::getColumnListing('restaurants');
                array_push($columns, 'category_id', 'cuisine1', 'cuisine2', 'cuisine3');
                $required_columns = array(
                    'id',
                    'name',
                    'address',
                    'telephone',
                    'budget',
                    'operating_time',
                    'latitude',
                    'longitude',
                    'category_id',
                    'is_excluded',
                    'tags'
                );
                $data = array();
                $current_id = 0;
                $file_content = '';

                foreach ($uploaded_files as $uploaded_file) {
                    $extension = explode('.', $uploaded_file->getClientOriginalName());
                    $extension = $extension[count($extension) - 1];

                    if (strtolower($extension) != 'tsv') {
                        throw new \Exception('Invalid file format. Must be TSV.');
                    }

                    $file_content = file_get_contents($uploaded_file->getPathname());
                    $file_content = explode("\n", $file_content);
                    $file_content = array_map('trim', $file_content);
                    $file_content = array_filter($file_content);

                    foreach ($file_content as $fc) {
                        $fc_data = explode("\t", $fc);
                        $fc_data = array_slice($fc_data, 0, 33);

                        if (!array_key_exists(31, $fc_data)) {
                            $fc_data[31] = 'N/A';
                        }

                        if ($fc_data) {
                            $data[] = $fc_data;
                        }

                        $fc_data = array();
                    }

                    $keys = array_shift($data);

                    foreach ($keys as $key => $value) {
                        $keys[$key] = str_replace('"', '', $keys[$key]);
                    }

                    foreach ($data as $key => $value) {
                        if (!$value) {
                            continue;
                        }

                        $current_id = $value[0];
                        $data[$key] = array_combine($keys, $value);
                        $data[$key]['name'] = str_replace('"', '', $data[$key]['name']);
                        $data[$key]['address'] = str_replace('"', '', $data[$key]['address']);
                        $data[$key]['telephone'] = str_replace('"', '', $data[$key]['telephone']);
                        $data[$key]['operating_time'] = str_replace('"', '', $data[$key]['operating_time']);
                        $data[$key]['thumbnail'] = str_replace('"', '', $data[$key]['thumbnail']);
                        $data[$key]['category_id'] = str_replace('"', '', $data[$key]['category_id']);
                        $data[$key]['cuisine1'] = str_replace('"', '', $data[$key]['cuisine1']);
                        $data[$key]['cuisine2'] = str_replace('"', '', $data[$key]['cuisine2']);
                        $data[$key]['cuisine3'] = str_replace('"', '', $data[$key]['cuisine3']);
                        $data[$key]['tags'] = str_replace('"', '', $data[$key]['tags']);

                        foreach ($data[$key] as $k => $v) {
                            if (in_array($k, $required_columns) && $v === '') {
                                $errors[] = $k . ' column is required [ID: '. $current_id . ']';
                            }

                            if (in_array($k, array('is_excluded', 'tags'))) {
                                continue;
                            }

                            if (!in_array($k, $columns)) {
                                unset($data[$key][$k]);
                            }
                        }
                    }

                    $file_content = '';
                }

                if ($errors) {
                    $data = array(
                        'errors' => $errors,
                        'success' => array()
                    );
                    return view('cms.restaurant.convert_checker', $data);
                }

                $connection = DB::connection();
                $connection->beginTransaction();
                $current_id = 0;
                RestaurantsCategory::truncate();

                foreach ($data as $d) {
                    $current_id = $d['id'];
                    $restaurant = new RestaurantsCms();
                    $restaurant = $restaurant->addRestaurant($d);
                    $restaurant_id = $restaurant->id;
                    $photo = null;
                    $category = null;
                    $restaurant_category = null;
                    $categories = explode(',', $d['category_id']);
                    $categories_count = count($categories);

                    // TODO: Add photo for restaurant

                    for ($i = 0; $i < $categories_count; $i++) {
                        if (!$categories[$i]) {
                            continue;
                        }

                        $cat = CategoriesCms::find($categories[$i]);

                        if (!$cat) {
                            $cat = new CategoriesCms();
                            $cat->addCategory('cuisine', $d['cuisine' . ($i + 1)], $categories[$i]);
                        }

                        $restaurant_category = RestaurantsCategoryCms::getByRestaurantCatId($restaurant_id, $cat->id);

                        if (!$restaurant_category) {
                            $restaurant_category = new RestaurantsCategoryCms();
                            $restaurant_category->addRestaurantCategory($restaurant_id, $cat->id);
                        }
                    }

                    $tags = explode(',', $d['tags']);
                    $tags = array_map('trim', $tags);
                    $tags_count = count($tags);

                    for ($i = 0; $i < $tags_count; $i++) {
                        if (!$tags[$i]) {
                            continue;
                        }

                        $cat = CategoriesCms::where('name',  $tags[$i])
                            ->where('type', CONSTANTS::CATEGORY_TAG)
                            ->first();

                        if (!$cat) {
                            $cat = new CategoriesCms();
                            $cat->addCategory('tag', $tags[$i]);
                        }

                        $restaurant_category = RestaurantsCategoryCms::getByRestaurantCatId($restaurant_id, $cat->id);

                        if (!$restaurant_category) {
                            $restaurant_category = new RestaurantsCategoryCms();
                            $restaurant_category->addRestaurantCategory($restaurant_id, $cat->id);
                        }
                    }
                }

                $restaurant = new RestaurantsCms();
                $restaurant->updateRestaurantSlugName();
                $connection->rollback();
                $success[] = 'TSV file verified';
            } catch (\Exception $e) {
                if ($connection) {
                    $connection->rollBack();
                }

                $errors[] = $e->getMessage();
                $errors[] = 'Error occured at ID ' . $current_id . ' of TSV file.';
                //dd($d);
            } catch (\PDOException $pe) {
                if ($connection) {
                    $connection->rollBack();
                }

                $errors[] = 'Error trying to add data';
            }
        }

        $data = array(
            'success' => isset($success) ? $success : array(),
            'errors' => isset($errors) ? $errors : array()
        );
        return view('cms.restaurant.convert_checker', $data);
    }

    /**
     * Add restaurant data via TSV file upload
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function convertAction(Request $request)
    {
        $success = array();
        $errors = array();

        if ($request->isMethod('post')) {
            $connection = null;

            try {
                $uploaded_files = \Input::file('convert');
                $columns = Schema::getColumnListing('restaurants');
                array_push($columns, 'category_id', 'cuisine1', 'cuisine2', 'cuisine3');
                $required_columns = array(
                    'id',
                    'name',
                    'address',
                    'telephone',
                    'budget',
                    'operating_time',
                    'latitude',
                    'longitude',
                    'category_id',
                    'is_excluded',
                    'tags'
                );
                $data = array();
                $current_id = 0;
                $file_content = '';

                foreach ($uploaded_files as $uploaded_file) {
                    $extension = explode('.', $uploaded_file->getClientOriginalName());
                    $extension = $extension[count($extension) - 1];

                    if (strtolower($extension) != 'tsv') {
                        throw new \Exception('Invalid file format. Must be TSV.');
                    }

                    $file_content = file_get_contents($uploaded_file->getPathname());
                    $file_content = explode("\n", $file_content);
                    $file_content = array_map('trim', $file_content);
                    $file_content = array_filter($file_content);
                    $fc_data = array();

                    foreach ($file_content as $fc) {
                        $fc_data = explode("\t", $fc);
                        $fc_data = array_slice($fc_data, 0, 33);

                        if (!array_key_exists(31, $fc_data)) {
                            $fc_data[31] = 'N/A';
                        }

                        if ($fc_data) {
                            $data[] = $fc_data;
                        }

                        $fc_data = array();
                    }

                    $keys = array_shift($data);

                    foreach ($keys as $key => $value) {
                        $keys[$key] = str_replace('"', '', $keys[$key]);
                    }

                    foreach ($data as $key => $value) {
                        if (!$value) {
                            continue;
                        }

                        $current_id = $value[0];
                        $data[$key] = array_combine($keys, $value);
                        $data[$key]['name'] = str_replace('"', '', $data[$key]['name']);
                        $data[$key]['address'] = str_replace('"', '', $data[$key]['address']);
                        $data[$key]['telephone'] = str_replace('"', '', $data[$key]['telephone']);
                        $data[$key]['operating_time'] = str_replace('"', '', $data[$key]['operating_time']);
                        $data[$key]['thumbnail'] = str_replace('"', '', $data[$key]['thumbnail']);
                        $data[$key]['category_id'] = str_replace('"', '', $data[$key]['category_id']);
                        $data[$key]['cuisine1'] = str_replace('"', '', $data[$key]['cuisine1']);
                        $data[$key]['cuisine2'] = str_replace('"', '', $data[$key]['cuisine2']);
                        $data[$key]['cuisine3'] = str_replace('"', '', $data[$key]['cuisine3']);
                        $data[$key]['tags'] = str_replace('"', '', $data[$key]['tags']);

                        foreach ($data[$key] as $k => $v) {
                            if (in_array($k, $required_columns) && $v === '') {
                                $errors[] = $k . ' column is required [ID: '. $current_id . ']';
                            }

                            if (in_array($k, array('is_excluded', 'tags'))) {
                                continue;
                            }

                            if (!in_array($k, $columns)) {
                                unset($data[$key][$k]);
                            }
                        }
                    }

                    $file_content = '';
                }

                if ($errors) {
                    $data = array(
                        'errors' => $errors,
                        'success' => array()
                    );
                    return view('cms.restaurant.convert', $data);
                }

                $connection = DB::connection();
                $connection->beginTransaction();
                $current_id = 0;
                RestaurantsCategory::truncate();

                foreach ($data as $d) {
                    $current_id = $d['id'];
                    $restaurant = new RestaurantsCms();
                    $restaurant = $restaurant->addRestaurant($d);
                    $restaurant_id = $restaurant->id;
                    $photo = null;
                    $category = null;
                    $restaurant_category = null;
                    $categories = explode(',', $d['category_id']);
                    $categories_count = count($categories);

                    // TODO: Add photo for restaurant

                    for ($i = 0; $i < $categories_count; $i++) {
                        if (!$categories[$i]) {
                            continue;
                        }

                        $cat = CategoriesCms::find($categories[$i]);

                        /* Disable adding categories in the meantime
                         if (!$cat) {
                            $cat = new CategoriesCms();
                            $cat->addCategory('cuisine', $d['cuisine' . ($i + 1)], $categories[$i]);
                        } */

                        $restaurant_category = RestaurantsCategoryCms::getByRestaurantCatId($restaurant_id, $cat->id);

                        if (!$restaurant_category) {
                            $restaurant_category = new RestaurantsCategoryCms();
                            $restaurant_category->addRestaurantCategory($restaurant_id, $cat->id);
                        }
                    }

                    $tags = explode(',', $d['tags']);
                    $tags = array_map('trim', $tags);
                    $tags_count = count($tags);

                    for ($i = 0; $i < $tags_count; $i++) {
                        if (!$tags[$i]) {
                            continue;
                        }

                        $cat = CategoriesCms::where('name',  $tags[$i])
                            ->where('type', CONSTANTS::CATEGORY_TAG)
                            ->first();

                        if (!$cat) {
                            $cat = new CategoriesCms();
                            $cat->addCategory('tag', $tags[$i]);
                        }

                        $restaurant_category = RestaurantsCategoryCms::getByRestaurantCatId($restaurant_id, $cat->id);

                        if (!$restaurant_category) {
                            $restaurant_category = new RestaurantsCategoryCms();
                            $restaurant_category->addRestaurantCategory($restaurant_id, $cat->id);
                        }
                    }
                }

                $restaurant = new RestaurantsCms();
                $restaurant->updateRestaurantSlugName();
                $connection->commit();
                $success[] = 'Data successfully added';
            } catch (\Exception $e) {
                if ($connection) {
                    $connection->rollBack();
                }

                $errors[] = $e->getMessage();
                $errors[] = 'Error occured at ID ' . $current_id . ' of TSV file.';
            } catch (\PDOException $pe) {
                if ($connection) {
                    $connection->rollBack();
                }

                $errors[] = 'Error trying to add data';
            }
        }

        $data = array(
            'success' => isset($success) ? $success : array(),
            'errors' => isset($errors) ? $errors : array()
        );
        return view('cms.restaurant.convert', $data);
    }

    /**
     * Delete a restaurant and all data associated with it
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        try {
            $errors = array();

            if (!$request->isMethod('POST')) {
                throw new Exception('Must be a POST request.');
            }

            $is_ajax = $request->ajax();
            $connection = DB::connection();
            $connection->beginTransaction();
            $restaurant_id = $request->input('id');
            $restaurant = Restaurants::find($restaurant_id);

            if (!$restaurant) {
                throw new Exception('Restaurant not found');
            }

            $bookmarks = Bookmarks::where('restaurant_id', $restaurant_id);

            if ($bookmarks->get()->count()) {
                $bookmarks->delete();
            }

            $check_ins = CheckIns::where('restaurant_id', $restaurant_id);

            if ($check_ins->get()->count()) {
                $check_ins->delete();
            }

            $photos = PhotosCms::where('restaurant_id', $restaurant_id);

            if ($photos->get()->count()) {
                $photos->delete();
            }

            $restaurants_category = RestaurantsCategory::where('restaurant_id', $restaurant_id);

            if ($restaurants_category->get()->count()) {
                $restaurants_category->delete();
            }

            $reviews = Reviews::where('restaurant_id', $restaurant_id);

            if ($reviews->get()->count()) {
                $reviews->delete();
            }

            $restaurant->delete();
            $connection->commit();

            if ($is_ajax) {
                header('Content-Type: application/json');
                $success[] = 'Restaurant ID ' . $restaurant_id . ' has been deleted';
                echo json_encode(
                    array(
                        'success' => $success
                    )
                );
                exit;
            } else {
                $success[] = 'Restaurant ID ' . $restaurant_id . ' has been deleted';
                \Session::flush('success', $success);
                return redirect()->back();
            }
        } catch (Exception $e) {
            $connection->rollBack();

            if ($is_ajax) {
                header('Content-Type: application/json');
                $errors[] = $e->getMessage();
                \Session::flush('errors', $errors);
                echo json_encode(
                    array(
                        'errors' => $e->getMessage()
                    )
                );
                exit;
            } else {
                $errors[] = $e->getMessage();
                \Session::flush('errors', $errors);
                return redirect()->back();
            }
        }
    }

    /**
     * Displays all suggested restaurants
     *
     * @return \Illuminate\View\View
     */
    public function indexSuggestedAction()
    {
        $restaurants = RestaurantsSuggest::where('status_verify', CONSTANTS::STATUS_UNVERIFIED)->get();
        $data = array(
            'restaurants' => ($restaurants->count()) ? $restaurants : array(),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'restaurant_suggest'
            ),
            'page_title' => 'Suggest A Restaurant',
            'status_unverified' => CONSTANTS::STATUS_UNVERIFIED,
            'status_approved' => CONSTANTS::STATUS_APPROVED,
            'status_rejected' => CONSTANTS::STATUS_REJECTED,
            'filter' => 'Unverified'
        );
        return view('cms.restaurant.suggested.index', $data);
    }

    /**
     * Displays filtered suggested restaurants
     *
     * @return \Illuminate\View\View
     */
    public function indexSuggestedFilterAction($filter)
    {

        if ($filter) {
            switch ($filter) {
                case 'approved':
                    $filter = 'Aprroved';
                    $restaurants = RestaurantsSuggest::where('status_verify', CONSTANTS::STATUS_APPROVED)->get();
                    break;
                case 'rejected':
                    $filter = 'Rejected';
                    $restaurants = RestaurantsSuggest::where('status_verify', CONSTANTS::STATUS_REJECTED)->get();
                    break;
                case 'unverified':
                    $filter = 'Unverified';
                    $restaurants = RestaurantsSuggest::where('status_verify', CONSTANTS::STATUS_UNVERIFIED)->get();
                    break;
            }
        }

        $data = array(
            'restaurants' => ($restaurants->count()) ? $restaurants : array(),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'restaurant_suggest'
            ),
            'page_title' => 'Suggest A Restaurant',
            'status_unverified' => CONSTANTS::STATUS_UNVERIFIED,
            'status_approved' => CONSTANTS::STATUS_APPROVED,
            'status_rejected' => CONSTANTS::STATUS_REJECTED,
            'filter' => $filter
        );
        return view('cms.restaurant.suggested.index', $data);
    }

    /**
     * Displays suggested restaurant info
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function viewSuggestedAction($id)
    {
        $restaurant = RestaurantsSuggest::find($id);

        if ($restaurant) {
            $restaurant = $restaurant->toArray();
            $boolean_columns = array(
                'can_deliver',
                'can_dinein',
                'can_dineout',
                'is_24hours',
                'smoking',
                'credit_card',
                'status_close',
                'status_verify',
            );

            foreach ($restaurant as $key => $value) {
                if (in_array($key, $boolean_columns)) {
                    if ($value === 1) {
                        $restaurant[$key] = 'Yes';
                    } elseif ($value === 0) {
                        $restaurant[$key] = 'No';
                    }
                }
            }
            $data['restaurant'] = $restaurant;
            $data['page_title'] = $restaurant['name'];
            $data['status_unverified'] = 'No';
        } else {
            $data['error'] = 'No record found';
        }
        return view('cms.restaurant.suggested.view', $data);
    }

    /**
     * Approve suggested restaurant
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function approveSuggestedAction(Request $request)
    {
        DB::transaction(function () use ($request) {
            $id = $request->input('id');

            $suggested_restaurant = RestaurantsSuggest::find($id);
            $suggested_restaurant->status_verify = CONSTANTS::STATUS_APPROVED;
            $suggested_restaurant->save();

            // Set the restaurant_id with initial value (90,000)
            $restaurant_id = CONSTANTS::SUGGESTED_RESTAURANT_INITIAL_ID;
            $restaurant_max_id = Restaurants::max('id');

            if ($restaurant_id <= $restaurant_max_id ) {
                $restaurant_id = ++$restaurant_max_id;
            }
            $suggested_restaurant->id = $restaurant_id;
            $restaurant_cms = new RestaurantsCms();
            $restaurant_cms->addRestaurant($suggested_restaurant);

            $cuisines = explode(', ', $suggested_restaurant->cuisines);

            foreach ($cuisines as $cuisine) {
                $categoriesCms = CategoriesCms::where('name', $cuisine)->first();
                if ($categoriesCms) {
                    $restaurant_category = RestaurantsCategoryCms::getByRestaurantCatId($restaurant_id, $categoriesCms['id']);
                }
                if (!$restaurant_category) {
                    $restaurant_category = new RestaurantsCategoryCms();
                    $restaurant_category->addRestaurantCategory($restaurant_id, $categoriesCms['id']);
                }
            }
            $restaurant_cms->updateRestaurantSlugName();
        });
        return redirect('cms/restaurant/suggested/index')->with('success', 'Successfully added new restaurant!');
    }

    /**
     * Reject suggested restaurant
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function rejectSuggestedAction(Request $request)
    {
        $id = $request->input('id');

        $suggested_restaurant = RestaurantsSuggest::find($id);
        $suggested_restaurant->status_verify = CONSTANTS::STATUS_REJECTED;
        $suggested_restaurant->save();

        return redirect('cms/restaurant/suggested/index')->with('success', 'Successfully marked restaurant as invalid!');
    }

    /**
     * Edit suggested restaurant
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function editSuggestedAction($id, Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = array();
            $restaurant = RestaurantsSuggest::find($id);
            $categories = CategoriesCms::where('type', 'cuisine')
                ->orderBy('name')
                ->get();

            $data = array(
                'restaurant' => ($restaurant) ? $restaurant : array(),
                'categories' => ($categories->toArray()) ? $categories : array(),
                'id' => $id,
                'javascripts' => array(
                    'restaurant_suggest'
                ),
                'page_title' => 'Edit Suggested Restaurant'
            );

            return view('cms.restaurant.suggested.edit', $data);
        } elseif ($request->isMethod('POST')) {
            extract($request->all());
            $errors = array();
            if (!$name) {
                $errors[] = 'Restaurant name is missing';
            }

            if (!$address) {
                $errors[] = 'Restaurant address is missing';
            }

            if (!$telephone) {
                $errors[] = 'Phone number is missing';
            }

            if (!$budget) {
                $errors[] = 'Budget is missing';
            }

            if ($budget && !is_numeric($budget)) {
                $errors[] = 'Budget is invalid';
            }

            if (!$latitude) {
                $errors[] = 'Latitude is missing';
            }

            if ($latitude && !is_numeric($latitude)) {
                $errors[] = 'Latitude is invalid';
            }

            if (!$longitude) {
                $errors[] = 'Longitude is missing';
            }

            if ($longitude && !is_numeric($longitude)) {
                $errors[] = 'Longitude is invalid';
            }

            if (!$operating_time) {
                $errors[] = 'Operating Time is missing';
            }

            if (!$cuisines) {
                $errors[] = 'Cuisines are missing';
            }

            if ($errors) {
                \Session::flash('errors', $errors);
                return redirect('cms/restaurant/suggested/edit/' . $id);
            }

            try {
                $restaurant = new RestaurantsSuggestCms();
                $restaurant->editRestaurantSuggest(
                    $id,
                    $name,
                    $address,
                    $telephone,
                    $budget,
                    $latitude,
                    $longitude,
                    $operating_time,
                    $credit_card,
                    $smoking,
                    $is_24hours,
                    $can_dinein,
                    $can_dineout,
                    $can_deliver,
                    $cuisines,
                    $other_details
                );
                \Session::flash('success', 'Suggested Restaurant has been updated');
                return redirect('cms/restaurant/suggested/view/' . $id);
            } catch (Exception $e) {
                \Session::flash('errors', array('An unexpected error occured while trying to update suggested restaurant.'));
                return redirect('cms/restaurant/suggested/edit/' . $id);
            }
        }
    }
}
