<?php

class BookmarksTest extends TestCase {

    /**
     * Test case for addBookmarkAction
     *
     * @return void
     */
    public function testAddBookmarkAction()
    {
        $json = '{"Bookmark": {"user_id": 44, "restaurant_id": 1}}';

        $response = $this->action('POST', 'Api\BookmarkController@addBookmarkAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->bookmark_id);
        $this->assertNotNull($array_response->data->date_created);
        $this->assertEquals(1, $array_response->data->is_bookmarked);
        $this->assertNotNull($array_response->data->restaurant_id);
        $this->assertEquals('saved', $array_response->data->status);
        $this->assertNotNull($array_response->data->user_id);
    }

    /**
     * Test case for deleteBookmarkAction
     *
     * @return void
     */
    public function testDeleteBookmarkAction()
    {
        $array = ['restaurant_id' => 1, 'user_id' => 44];

        $response = $this->action('DELETE', 'Api\BookmarkController@deleteBookmarkAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->id);
        $this->assertEquals(1, $array_response->data->is_success);
    }

    /**
     * Test case for userBookmarkListAction
     *
     * @return void
     */
    public function testUserBookmarkListAction()
    {
        $array = ['user_id' => 24];

        $response = $this->action('GET', 'Api\BookmarkController@userBookmarkListAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->bookmark);
            $this->assertNotNull($data->bookmark->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
        }
    }
}

?>