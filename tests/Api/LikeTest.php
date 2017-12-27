<?php

class LikeTest extends TestCase {

    /**
     * Test case for addAction
     * Like review
     *
     * @return void
     */
    public function testAddAction()
    {
        $json = '{"Like": {"type": 1, "type_id": 20, "user_id": 44}}';

        $response = $this->action('POST', 'Api\LikeController@addAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->type);
        $this->assertNotNull($array_response->data->type_id);
        $this->assertNotNull($array_response->data->user_id);
        $this->assertEquals(0, $array_response->data->is_existing);
        $this->assertGreaterThan(0, $array_response->data->like_count);
    }

    /**
     * Test case for deleteAction
     *
     * @return void
     */
    public function testDeleteAction()
    {
        $array = ['user_id' => 44, 'type' => 1, 'type_id' => 20];

        $response = $this->action('DELETE', 'Api\LikeController@deleteAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->type);
        $this->assertNotNull($array_response->data->type_id);
        $this->assertNotNull($array_response->data->user_id);
        $this->assertNotNull($array_response->data->like_count);
    }

    /**
     * Test case for checkLikeAction
     *
     * @return void
     */
    public function testCheckLikeAction()
    {
        $array = ['user_id' => 44, 'type' => 1, 'type_id' => 24];

        $response = $this->action('GET', 'Api\LikeController@checkLikeAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->status);
        $this->assertNotNull($array_response->data->type);
        $this->assertNotNull($array_response->data->info);
        $this->assertNotNull($array_response->data->info->id);
        $this->assertNotNull($array_response->data->total_likes);
    }

    /**
     * Test case for likerListAction
     *
     * @return void
     */
    public function testLikerListAction()
    {
        $array = ['type' => 1, 'type_id' => 24, 'viewer_id' => 56];

        $response = $this->action('GET', 'Api\LikeController@likerListAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->id);
        }
    }

}

?>