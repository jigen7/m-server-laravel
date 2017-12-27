<?php

class ActivitiesTest extends TestCase {

    /**
     * Test case for getUserActivitiesAction
     *
     * @return void
     */
    public function testGetUserActivitiesAction()
    {
        $array = ['id' => 8];

        $response = $this->action('GET', 'Api\ActivitiesController@getUserActivitiesAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->status);
        $this->assertNotNull($array_response->app_version);
        $this->assertNotNull($array_response->app_store);
        $this->assertNotNull($array_response->google_store);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->activity);
            $this->assertNotNull($data->activity->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }

    /**
     * Test case for getRestaurantActivitiesAction
     *
     * @return void
     */
    public function testGetRestaurantActivitiesAction()
    {
        $array = ['id' => 1];

        $response = $this->action('GET', 'Api\ActivitiesController@getRestaurantActivitiesAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->activity);
            $this->assertNotNull($data->activity->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }

    /**
     * Test case for getFollowedActivitiesAction
     *
     * @return void
     */
    public function testGetFollowedActivitiesAction()
    {
        $array = ['id' => 5, 'restaurant_id' => 452];

        $response = $this->action('GET', 'Api\ActivitiesController@getFollowedActivitiesAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->activity);
            $this->assertNotNull($data->activity->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }

    /**
     * Test case for getAllActivitiesAction
     *
     * @return void
     */
    public function testgetAllActivitiesAction()
    {
        $response = $this->action('GET', 'Api\ActivitiesController@getAllActivitiesAction');

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->activity);
            $this->assertNotNull($data->activity->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }

    /**
     * Test case for getNearRestaurantActivitiesAction
     *
     * @return void
     */
    public function testGetNearRestaurantActivitiesAction()
    {
        $array = ['longitude' => 121.0489328, 'latitude' => 14.5509043, 'distance' => 300.0, 'viewer_id' => 73];

        $response = $this->action('GET', 'Api\ActivitiesController@getNearRestaurantActivitiesAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->activity);
            $this->assertNotNull($data->activity->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }

}

?>