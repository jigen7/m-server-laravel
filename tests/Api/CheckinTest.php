<?php

class CheckinTest extends TestCase {

    /**
     * Test case for viewAction
     *
     * @return void
     */
    public function testViewAction()
    {
        $array = ['id' => 1];

        $response = $this->action('GET', 'Api\CheckinController@viewAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->checkin);
        $this->assertNotNull($array_response->data->checkin->id);
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
     * Test case for restaurantAction
     *
     * @return void
     */
    public function testRestaurantAction()
    {
        $array = ['id' => 187];

        $response = $this->action('GET', 'Api\CheckinController@restaurantAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->checkin);
            $this->assertNotNull($data->checkin->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->photos);

            foreach ($data->photos as $photos) {
                $this->assertNotNull($photos->id);
            }
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

        $response = $this->action('GET', 'Api\CheckinController@userAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->checkin);
            $this->assertNotNull($data->checkin->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->photos);

            foreach ($data->photos as $photos) {
                $this->assertNotNull($photos->id);
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
        $json = '{"CheckIn": {"user_id": 44, "restaurant_id": 1, "message": "Test checkin with APITest"}, "Photos": []}';

        $response = $this->action('POST', 'Api\CheckinController@addAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->checkin);
        $this->assertNotNull($array_response->data->checkin->id);
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
        $array = ['id' => 12];
        $json = '{"CheckIn": {"message": "[EDIT] Test edit from APITest", "user_id": 24}}';

        $response = $this->action('POST', 'Api\CheckinController@editAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->checkin);
        $this->assertNotNull($array_response->data->checkin->id);
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
        $array = ['id' => 11];

        $response = $this->action('DELETE', 'Api\CheckinController@deleteAction', $array);

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