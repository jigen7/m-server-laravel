<?php

class RestaurantsTest extends TestCase {

    /**
     * Test case for viewAction
     *
     * @return void
     */
    public function testViewAction()
    {
        $array = ['id' => 5, 'viewer_id' => 2];

        $response = $this->action('GET', 'Api\RestaurantController@viewAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->restaurant);
        $this->assertNotNull($array_response->data->restaurant->id);
        $this->assertNotNull($array_response->data->photos);
        $this->assertNotNull($array_response->data->categories);
        $this->assertNotNull($array_response->data->activity);
        $this->assertNotNull($array_response->data->activity->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertNotNull($array_response->data->checkin);
        $this->assertNotNull($array_response->data->checkin->id);

        foreach ($array_response->data->photos as $photo) {
            $this->assertNotNull($photo->id);
        }

        foreach ($array_response->data->categories as $category) {
            $this->assertNotNull($category->id);
        }

    }

    /**
     * Test case for nearAction
     *
     * @return void
     */
    public function testNearAction()
    {
        $array = ['longitude' => 121.0489283, 'latitude' => 14.5509135, 'distance' => 300];

        $response = $this->action('GET', 'Api\RestaurantController@nearAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->categories);

            foreach ($data->categories as $category) {
                $this->assertNotNull($category->id);
            }
        }
    }

    /**
     * Test case for nearbyCuisineAction
     *
     * @return void
     */
    public function testNearbyCuisineAction()
    {
        $array = ['longitude' => 121.0489283, 'latitude' => 14.5509135, 'distance' => 0.621371];

        $response = $this->action('GET', 'Api\RestaurantController@nearbyCuisineAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->category);
            $this->assertNotNull($data->category->id);
        }
    }

    /**
     * Test case for nearbyRestaurantsCuisineAction
     *
     * @return void
     */
    public function testNearbyRestaurantsCuisineAction()
    {
        $array = ['longitude' => 121.0489283, 'latitude' => 14.5509135, 'distance' => 0.621371, 'cuisine' => 'Korean'];

        $response = $this->action('GET', 'Api\RestaurantController@nearbyRestaurantsCuisineAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->categories);

            foreach ($data->categories as $category) {
                $this->assertNotNull($category->id);
            }
        }
    }

    /**
     * Test case for searchAction
     * No parameters, search all
     *
     * @return void
     */
    public function testSearchAllAction()
    {
        $response = $this->action('GET', 'Api\RestaurantController@searchAction');

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->categories);
            $this->assertNotNull($data->photo_count);

            if (isset($data->photos)) {
                foreach ($data->photos as $photo) {
                    $this->assertNotNull($photo->id);
                }
            }

            if (isset($data->categories)) {
                foreach ($data->categories as $category) {
                    $this->assertNotNull($category->id);
                }
            }

            if (isset($data->activity)) {
                $this->assertNotNull($data->activity->id);
            }

            if (isset($data->user)) {
                $this->assertNotNull($data->user->id);
            }

            if (isset($data->bookmarks)) {
                $this->assertNotNull($data->bookmarks->id);
            }
        }
    }

    /**
     * Test case for searchAction
     * With parameters
     *
     * @return void
     */
    public function testSearchAction()
    {
        $array = ['name' => 'o', 'orderby' => 'checkins'];

        $response = $this->action('GET', 'Api\RestaurantController@searchAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->categories);
            $this->assertNotNull($data->photo_count);

            if (isset($data->photos)) {
                foreach ($data->photos as $photo) {
                    $this->assertNotNull($photo->id);
                }
            }

            if (isset($data->categories)) {
                foreach ($data->categories as $category) {
                    $this->assertNotNull($category->id);
                }
            }

            if (isset($data->activity)) {
                $this->assertNotNull($data->activity->id);
            }

            if (isset($data->user)) {
                $this->assertNotNull($data->user->id);
            }

            if (isset($data->bookmarks)) {
                $this->assertNotNull($data->bookmarks->id);
            }
        }
    }

    /**
     * Test case for restaurantsAutoCompleteAction
     *
     * @return void
     */
    public function testRestaurantsAutoCompleteAction()
    {
        $array = ['search_key' => 'mcdo'];

        $response = $this->action('GET', 'Api\RestaurantController@restaurantsAutoCompleteAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
    }

    /**
     * Test case for recentActivitySearch
     *
     * @return void
     */
    public function testRecentActivitySearchAction()
    {
        $array = ['user_id' => 4, 'search_key' => 'noli'];

        $response = $this->action('GET', 'Api\RestaurantController@recentActivitySearchAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->category);

            foreach ($data->category as $category) {
                $this->assertNotNull($category->id);
            }
        }
    }

}

?>