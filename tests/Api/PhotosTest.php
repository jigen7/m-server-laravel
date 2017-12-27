<?php

class PhotosTest extends TestCase {

    /**
     * Test case for viewPhotosByTypeAction
     * Get by types: 'review', 'checkin', 'restaurant', 'user'
     *
     * @return void
     */
    public function testViewPhotosByTypeAction()
    {
        $array = ['type' => 'review', 'type_id' => 21];

        $response = $this->action('GET', 'Api\PhotosController@viewPhotosByTypeAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
        }
    }

    /**
     * Test case for viewPhotosByTypeAction
     * Get by types: 'photo'
     *
     * @return void
     */
    public function testViewPhotosByTypePhotoAction()
    {
        $array = ['type' => 'photo', 'type_id' => 1];

        $response = $this->action('GET', 'Api\PhotosController@viewPhotosByTypeAction', $array);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->page->current);
        $this->assertNotNull($array_response->page->number);

        foreach ($array_response->data as $data) {
            $this->assertNotNull($data->id);
            $this->assertNotNull($data->user);
            $this->assertNotNull($data->user->id);
            $this->assertNotNull($data->restaurant);
            $this->assertNotNull($data->restaurant->id);
            $this->assertNotNull($data->like_count);
        }
    }

}

?>