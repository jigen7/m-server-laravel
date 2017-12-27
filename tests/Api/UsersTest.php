<?php

class UsersTest extends TestCase {

    /**
     * Test case for viewAction
     *
     * @return void
     */
    public function testViewAction()
    {
        $array = ['user_id' => 3];

        $response = $this->action('GET', 'Api\UserController@viewAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for addAction
     *
     * @return void
     */
    public function testAddAction()
    {
        $array = ['id' => 44];
        $json = '{"User": {"firstname": "APITest", "lastname": "APITest", "gender": "female", "age": 25, "email": "rachel.luna@klab.com", "income": 0, "nationality": "philippines", "facebook_id": 10153356057108066, "device_id": "", "device_type": "Unknown", "fb_access_token": "CAAV2bSYcn3MBAPyB486QKjXGCSnx5bv32rdDyZB86aj5foimhiFNY7ZCZAmmmTuY8TjVmfJ77UjBUpCW6WzTSz0oT0BZCzZBUDo0tFYnQwzfVbad1unVZBGbXaEOYfBSsFlLyZCqGGjEn5fLXpjyScbama91mZA59gzV0KdLN0jZCBOZA50ZAkywYzxqjeXpZBsENvsZD"}}';

        $response = $this->action('POST', 'Api\UserController@addAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertNotNull($array_response->data->message);
        $this->assertNotNull($array_response->data->unsent_notifications);
    }

    /**
     * Test case for editAction
     *
     * @return void
     */
    public function testEditAction()
    {
        $array = ['id' => 44];
        $json = '{"User": {"firstname": "APITest", "lastname": "APITest", "gender": "female", "age": 25, "email": "rachel.luna@klab.com", "income": 0, "nationality": "philippines", "facebook_id": 10153356057108066, "device_id": "", "device_type": "Unknown"}}';

        $response = $this->action('POST', 'Api\UserController@editAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for searchAction
     *
     * @return void
     */
    public function testSearchAction()
    {
        $array = ['key' => 'adrian', 'viewer_id', 3];

        $response = $this->action('GET', 'Api\UserController@searchAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->users);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data->users as $user) {
            $this->assertNotNull($user->id);
        }
    }

    /**
     * Test case for viewStatisticsAction
     *
     * @return void
     */
    public function testViewStatisticsAction()
    {
        $array = ['user_id' => 13, 'viewer_id' => 1];

        $response = $this->action('GET', 'Api\UserController@viewStatisticsAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for enableNotificationAction
     *
     * @return void
     */
    public function testEnableNotificationAction()
    {
        $array = ['id' => 44];
        $json = '{"User": {"user_id": 4, "device_id": "d33d67bfedaa3baee58ee710e6da3b09bf756d6311decbc5b062fb920c06cea7", "device_type": "iOS"}}';

        $response = $this->action('POST', 'Api\UserController@enableNotificationAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for disableNotificationAction
     *
     * @return void
     */
    public function testDisableNotificationAction()
    {
        $array = ['id' => 44];
        $json = '{"User": {"user_id": 4, "device_id": "d33d67bfedaa3baee58ee710e6da3b09bf756d6311decbc5b062fb920c06cea7", "device_type": "iOS"}}';

        $response = $this->action('POST', 'Api\UserController@disableNotificationAction', $array, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

}

?>