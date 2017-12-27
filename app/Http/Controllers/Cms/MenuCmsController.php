<?php
namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Http\Models\Cms\MenuCms;
use App\Http\Models\Restaurants;
use DB;
use Illuminate\Support\Facades\Session;
use Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MenuCmsController extends Controller
{
    /**
     * Add new menu record
     *
     * @param $restaurant_id
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function addAction($restaurant_id, Request $request)
    {
        $errors = [];
        $success = '';
        $restaurant = Restaurants::find($restaurant_id);

        if ($request->isMethod('post')) {
            extract($request->all());

            if (!$name) {
                $errors[] = 'Name is required';
            }

            if (!$category) {
                $errors[] = 'Category is required';
            }

            if (!$price) {
                $errors[] = 'Price is required';
            }

            if ($price && !is_numeric($price)) {
                $errors[] = 'Price is invalid';
            }

            if ($errors) {
                $data = array(
                    'restaurant' => $restaurant,
                    'success' => $success,
                    'errors' => $errors
                );

                return view('cms.menu.add', $data);
            }

            $connection = DB::connection();

            try {
                $connection->beginTransaction();
                $menu = new MenuCms();
                $menu->addMenu(
                    $restaurant_id,
                    $category,
                    $name,
                    $size,
                    $price,
                    $description
                );
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                $errors[] = $e->getMessage();
            }

            if (!$errors) {
                $success = 'Menu has been successfully added';
            }
        }

        $data = array(
            'restaurant' => $restaurant,
            'success' => $success,
            'errors' => $errors
        );

        return view('cms.menu.add', $data);
    }

    /**
     * Display the list of menu
     *
     * @param $restaurant_id
     * @return \Illuminate\View\View
     */
    public function viewAction($restaurant_id)
    {
        $restaurant = Restaurants::find($restaurant_id);
        $menu = MenuCms::where('restaurant_id', $restaurant_id)->get();
        $data = array(
            'menu' => $menu->count() ? $menu : [],
            'restaurant' => $restaurant,
            'stylesheets' => array(
                'data_table'
            ),
            'javascripts' => array(
                'data_table',
                'menu'
            ),
        );
        return view('cms.menu.view', $data);
    }

    /**
     * Display menu details
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function viewDetailsAction($id)
    {
        $data = [];

        if (!$id) {
            $data['error'] = 'Invalid ID';
        } else {
            $data['id'] = $id;
        }

        $menu = MenuCms::find($id);

        if ($menu) {
            $menu = $menu->toArray();
            $data['menu'] = $menu;
            $data['page_title'] = $menu['name'];
        } else {
            $data['error'] = 'Menu not found';
        }

        return view('cms.menu.view_details', $data);
    }

    /**
     * Edit menu details
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function editAction($id, Request $request)
    {
        if ($request->isMethod('GET')) {
            $data = array();
            $menu = MenuCms::find($id);
            $data = array(
                'menu' => ($menu) ? $menu : array(),
                'id' => $id,
            );
            return view('cms.menu.edit', $data);
        } elseif ($request->isMethod('POST')) {
            extract($request->all());
            $errors = array();

            if (!$name) {
                $errors[] = 'Name is missing';
            }

            if (!$category) {
                $errors[] = 'Category is missing';
            }

            if (!$price) {
                $errors[] = 'Price is missing';
            }

            if ($price && !is_numeric($price)) {
                $errors[] = 'Price is invalid';
            }

            if ($errors) {
                \Session::flash('errors', $errors);
                return redirect('cms/menu/edit/' . $id);
            }

            try {
                $menu = new MenuCms();
                $menu->editMenu(
                    $id,
                    $name,
                    $category,
                    $size,
                    $price,
                    $description
                );
                \Session::flash('success', 'Menu has been updated');
                return redirect('cms/menu/view/details/' . $id);
            } catch (Exception $e) {
                \Session::flash('errors', array('An unexpected error occured while trying to update menu.'));
                return redirect('cms/menu/edit/' . $id);
            }
        }
    }

    /**
     * Delete a menu
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
            $id = $request->input('id');
            $menu = MenuCms::find($id);

            if (!$menu) {
                throw new Exception('Menu not found');
            }

            $menu->delete();
            $connection->commit();

            if ($is_ajax) {
                header('Content-Type: application/json');
                $success[] = 'Menu with ID: ' . $id . ' has been deleted';
                echo json_encode(
                    array(
                        'success' => $success
                    )
                );
                exit;
            } else {
                $success[] = 'Menu with ID: ' . $id . ' has been deleted';
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
     * Convert tsv file to menu data
     *
     * @param Request $request
     * @return array|\Illuminate\View\View|mixed
     */
    public function convertAction(Request $request)
    {
        $errors = [];
        $success = [];

        if ($request->isMethod('post')) {
            $connection = null;
            try {
                $uploaded_files = \Input::file('convert');
                $columns = Schema::getColumnListing('menu');
                array_push($columns, 'category_id', 'cuisine1', 'cuisine2', 'cuisine3');
                $required_columns = [
                    'name',
                    'category'
                ];

                foreach ($uploaded_files as $uploaded_file) {
                    $filename = explode('.', $uploaded_file->getClientOriginalName());
                    $restaurant_id = $filename[0];
                    $extension = $filename[count($filename) - 1];

                    if (strtolower($extension) != 'tsv') {
                        throw new \Exception('Invalid file format. Must be TSV.');
                    }

                    $file_content = file_get_contents($uploaded_file->getPathname());
                    $file_content = explode("\n", $file_content);
                    $file_content = array_map('trim', $file_content);

                    foreach ($file_content as $fc) {
                        $fc= explode("\t", $fc);

                        if ($fc) {
                            $fc_data[] = $fc;
                        }
                    }
                    $keys = array_shift($fc_data);

                    foreach ($keys as $key => $value) {
                        $keys[$key] = str_replace('"', '', $keys[$key]);
                    }

                    foreach ($fc_data as $key => $value) {
                        if (!$value) {
                            continue;
                        }
                        $current_id = $value[0];
                        $fc_data[$key] = array_combine($keys, $value);
                        $fc_data[$key]['restaurant_id'] = $restaurant_id;
                        $fc_data[$key]['name'] = str_replace('"', '', $fc_data[$key]['name']);
                        $fc_data[$key]['description'] = str_replace('"', '', $fc_data[$key]['description']);
                        $fc_data[$key]['price'] = str_replace('"', '', $fc_data[$key]['price']);
                        $fc_data[$key]['serving'] = str_replace('"', '', $fc_data[$key]['serving']);
                        $fc_data[$key]['category'] = str_replace('"', '', $fc_data[$key]['category']);

                        foreach ($fc_data[$key] as $k => $v) {
                            if (in_array($k, $required_columns) && $v === '') {
                                $errors[] = $k . ' column is required [ID: '. $current_id . ']';
                            }

                            if (!in_array($k, $columns)) {
                                unset($fc_data[$key][$k]);
                            }
                        }
                    }
                    $data[] = $fc_data;
                    $fc_data = [];
                }

                if ($errors) {
                    $data = [
                        'errors' => $errors,
                        'success' => $success
                    ];
                    return view('cms.menu.convert', $data);
                }

                $connection = DB::connection();
                $connection->beginTransaction();
                foreach ($data as $key => $value) {
                    foreach ($value as $v) {
                        $menu = new MenuCms();
                        $menu->addMenu(
                            $v['restaurant_id'],
                            $v['name'],
                            $v['category'],
                            $v['serving'],
                            $v['price'],
                            $v['description']
                        );
                    }
                }

                $connection->commit();
                $success[] = 'Data successfully added';

            }  catch (\Exception $e) {
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
            'success' => $success,
            'errors' => $errors
        );
        return view('cms.menu.convert', $data);
    }
}
