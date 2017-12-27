<?php

class FollowTest extends TestCase {

    /**
     * Test case for followAction
     *
     * @return void
     */
    public function testFollowAction()
    {
        $json = '{"Follow": {"follower_id" : 4, "following_id" : 3}}';

        $response = $this->action('POST', 'Api\FollowController@followAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertEquals('Success', $array_response->data->message);
    }

    /**
     * Test case for followManyAction
     *
     * @return void
     */
    public function testFollowManyAction()
    {
        $json = '{"Follow": {"follower_id" : 4, "following_fb_ids": [795455113834832,122,10152507227043870,777,963276043686549]}}';

        $response = $this->action('POST', 'Api\FollowController@followManyAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->users);
        $this->assertNotNull($array_response->data->failed_users);

        if (!$this->isEmpty($array_response->data->users)) {
            foreach ($array_response->data->users as $user) {
                $this->assertNotNull($user->id);
            }
        }
    }

    /**
     * Test case for unfollowAction
     *
     * @return void
     */
    public function testUnfollowAction()
    {
        $json = '{"Follow": {"follower_id" : 4, "following_id" : 3}}';

        $response = $this->action('POST', 'Api\FollowController@unfollowAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
        $this->assertEquals('Success', $array_response->data->message);
    }

    /**
     * Test case for unfollowManyAction
     *
     * @return void
     */
    public function testUnfollowManyAction()
    {
        $json = '{"Follow": {"follower_id" : 4, "following_fb_ids": [795455113834832,122,10152507227043870,777,963276043686549]}}';

        $response = $this->action('POST', 'Api\FollowController@unfollowManyAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->users);
        $this->assertNotNull($array_response->data->failed_users);

        if (!$this->isEmpty($array_response->data->users)) {
            foreach ($array_response->data->users as $user) {
                $this->assertNotNull($user->id);
            }
        }
    }

    /**
     * Test case for followersAction
     *
     * @return void
     */
    public function testFollowersAction()
    {
        $array = ['user_id' => 71, 'viewer_id' => 5];

        $response = $this->action('GET', 'Api\FollowController@followersAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->users);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        if (!$this->isEmpty($array_response->data->users)) {
            foreach ($array_response->data->users as $user) {
                $this->assertNotNull($user->id);
            }
        }
    }

    /**
     * Test case for followingAction
     *
     * @return void
     */
    public function testFollowingAction()
    {
        $array = ['user_id' => 71, 'viewer_id' => 5];

        $response = $this->action('GET', 'Api\FollowController@followingAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->users);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        if (!$this->isEmpty($array_response->data->users)) {
            foreach ($array_response->data->users as $user) {
                $this->assertNotNull($user->id);
            }
        }
    }

}

?>