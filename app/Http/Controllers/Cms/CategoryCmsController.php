<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Categories;
use App\Http\Models\Cms\CategoriesCms;
use App\Http\Models\Restaurants;
use App\Http\Models\RestaurantsCategory;
use DB;
use Illuminate\Http\Request;

class CategoryCmsController extends Controller
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
        $categories = Categories::all();
        $data = array(
            'page_title' => 'Categories',
            'categories' => ($categories->count()) ? $categories : array(),
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'category'
            )
        );
        return view('cms.category.index', $data);
    }

    /**
     * Display restaurants having a particular category
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function viewAction($id)
    {
        $data = array();

        if (!$id) {
            $data['errors'][] = 'Invalid ID';
        } else {
            $data['id'] = $id;
        }

        $category = Categories::find($id);

        if (!$category) {
            $data['errors'][] = 'Category not found.';
            return view('cms.category.view', $data);
        }

        $restaurants_category = RestaurantsCategory::where('category_id', $id)->get();

        if ($restaurants_category) {
            $restaurants = array();

            foreach ($restaurants_category as $restaurant_category) {
                $restaurant = Restaurants::find($restaurant_category->restaurant_id);

                if (!$restaurant) {
                    $data['errors'][] = 'Cannot find restaurant ID ' . $restaurant->id . '.';
                    return view('cms.category.view', $data);
                }

                $restaurants[] = array(
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'address' => $restaurant->address,
                    'telephone' => $restaurant->telephone,
                    'rating' => $restaurant->rating,
                    'budget' => $restaurant->budget
                );
                $restaurant = null;
            }

            $data['restaurants'] = $restaurants;
        }

        $data['page_title'] = ': ' . $category->name;
        return view('cms.category.view', $data);
    }

    /**
     * Add new category
     *
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $data = array();
        $errors = array();
        $success = '';

        if ($request->isMethod('post')) {
            $params = $request->all();

            if (!$params['category_type']) {
                $errors[] = 'Category Type is required';
            } else {
                if (!in_array($params['category_type'], array('city', 'cuisine', 'mall'))) {
                    $errors[] = 'Invalid Category Type';
                }
            }

            if (!$params['category_name']) {
                $errors[] = 'Category Name is required';
            }

            if (!$errors) {
                $connection = DB::connection();

                try {
                    $connection->beginTransaction();
                    $category = new CategoriesCms();
                    $category->addCategory($params['category_type'], $params['category_name']);
                    $connection->commit();
                    $success = 'Category has been successfully added';
                } catch (Exception $e) {
                    $connection->rollBack();
                    $errors[] = $e->getMessage();
                }
            }
        }

        $data = array(
            'errors' => $errors,
            'success' => $success
        );
        return view('cms.category.new', $data);
    }
}