<?php

class ReviewsTest extends TestCase {

    /**
     * Test case for viewAction
     *
     * @return void
     */
    public function testViewAction()
    {
        $array = ['id' => 2];

        $response = $this->action('GET', 'Api\ReviewController@viewAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->review);
        $this->assertNotNull($array_response->data->review->id);
        $this->assertNotNull($array_response->data->restaurant);
        $this->assertNotNull($array_response->data->restaurant->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertNotNull($array_response->data->photos);

        foreach ($array_response->data->photos as $photos) {
            $this->assertNotNull($photos->id);
        }
    }

    /**
     * Test case for userAction
     *
     * @return void
     */
    public function testUserAction()
    {
        $array = ['id' => 4];

        $response = $this->action('GET', 'Api\ReviewController@userAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->review);
            $this->assertNotNull($data->review->id);
            $this->assertNotNull($data->review->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->photos);

            foreach ($data->photos as $photo) {
                $this->assertNotNull($photo->id);
            }
        }
    }

    /**
     * Test case for restaurantAction
     *
     * @return void
     */
    public function testRestaurantAction()
    {
        $array = ['id' => 4];

        $response = $this->action('GET', 'Api\ReviewController@restaurantAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->review);
            $this->assertNotNull($data->review->id);
            $this->assertNotNull($data->review->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->photos);

            foreach ($data->photos as $photo) {
                $this->assertNotNull($photo->id);
            }
        }
    }

    /**
     * Test case for addAction
     *
     * @return void
     */
    public function testAddAction()
    {
        $json = '{"Review": {"user_id": 44, "restaurant_id": 1, "title": "Test Review", "text": "Test review from APITest", "rating": 5}, "Photos": []}';

        $response = $this->action('POST', 'Api\ReviewController@addAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->review);
        $this->assertNotNull($array_response->data->review->id);
        $this->assertNotNull($array_response->data->restaurant);
        $this->assertNotNull($array_response->data->restaurant->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertNotNull($array_response->data->photos);

        foreach ($array_response->data->photos as $photo) {
            $this->assertNotNull($photo->id);
        }
    }

    /**
     * Test case for editAction
     *
     * @return void
     */
    public function testEditAction()
    {
        $array = ['id' => 46];
        $json = '{"Review": {"user_id": 44, "restaurant_id": 1, "title": "[EDIT] Test Review", "text": "[EDIT] Test review from APITest", "rating": 5}, "Photos": []}';

        $response = $this->action('POST', 'Api\ReviewController@editAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->review);
        $this->assertNotNull($array_response->data->review->id);
        $this->assertNotNull($array_response->data->restaurant);
        $this->assertNotNull($array_response->data->restaurant->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertNotNull($array_response->data->photos);

        foreach ($array_response->data->photos as $photo) {
            $this->assertNotNull($photo->id);
        }
    }

    /**
     * Test case for deleteAction
     *
     * @return void
     */
    public function testDeleteAction()
    {
        $array = ['id' => 46];

        $response = $this->action('DELETE', 'Api\ReviewController@deleteAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->id);
        $this->assertEquals(1, $array_response->data->is_success);
    }

}

?>