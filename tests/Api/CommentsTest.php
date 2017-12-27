<?php

class CommentsTest extends TestCase {

    /**
     * Test case for viewAction
     *
     * @return void
     */
    public function testViewAction()
    {
        $array = ['id' => 1];

        $response = $this->action('GET', 'Api\CommentController@viewAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->comment);
        $this->assertNotNull($array_response->data->comment->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for viewByUserIdAction
     *
     * @return void
     */
    public function testViewByUserIdAction()
    {
        $array = ['id' => 56];

        $response = $this->action('GET', 'Api\CommentController@viewByUserIdAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->comment);
            $this->assertNotNull($data->comment->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
        }
    }

    /**
     * Test case for viewByReviewIdAction
     *
     * @return void
     */
    public function testViewByReviewIdAction()
    {
        $array = ['id' => 24];

        $response = $this->action('GET', 'Api\CommentController@viewByReviewIdAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->restaurant_likes);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->comment);
            $this->assertNotNull($data->comment->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
        }

    }

    /**
     * Test case for viewByCheckinAction
     *
     * @return void
     */
    public function testViewByCheckinAction()
    {
        $array = ['id' => 1];

        $response = $this->action('GET', 'Api\CommentController@viewByCheckinAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->comment);
            $this->assertNotNull($data->comment->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
        }

    }

    /**
     * Test case for viewByPhotoIdAction
     *
     * @return void
     */
    public function testViewByPhotoIdAction()
    {
        $array = ['id' => 15];

        $response = $this->action('GET', 'Api\CommentController@viewByPhotoIdAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->comment);
            $this->assertNotNull($data->comment->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
        }

    }

    /**
     * Test case for addCommentAction
     *
     * @return void
     */
    public function testAddCommentAction()
    {
        $json = '{"Comment": {"text": "Test comment from PHPUnit", "type" : 1, "type_id": 2, "user_id": 44}}';

        $response = $this->action('POST', 'Api\CommentController@addCommentAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->comment);
        $this->assertNotNull($array_response->data->comment->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }


    /**
 * Test case for editCommentAction
 *
 * @return void
 */
    public function testEditCommentAction()
    {
        $json = '{"Comment": {"id": 23,	"text": "[Edited] Test comment from PHPUnit", "user_id": 44}}';

        $response = $this->action('POST', 'Api\CommentController@editCommentAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->comment);
        $this->assertNotNull($array_response->data->comment->id);
        $this->assertNotNull($array_response->data->user);
        $this->assertNotNull($array_response->data->user->id);
    }

    /**
     * Test case for deleteCommentAction
     *
     * @return void
     */
    public function testDeleteCommentAction()
    {
        $array = ['id' => 30];

        $response = $this->action('DELETE', 'Api\CommentController@deleteCommentAction', $array);

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