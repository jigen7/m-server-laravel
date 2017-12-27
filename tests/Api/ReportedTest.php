<?php

class ReportedTest extends TestCase {

    /**
     * Test case for addAction
     *
     * @return void
     */
    public function testAddAction()
    {
        $json = '{"Report": {"type": 6, "type_id": 1, "reason": "name", "user_id": 44}}';

        $response = $this->action('POST', 'Api\ReportedController@addAction', null, array(), array(), array(), array(), $json);

        $this->assertEquals(200, $response->getStatusCode());
        $this->isJson($response);

        $array_response = json_decode($response->getContent());

        $this->assertNotNull($array_response);
        $this->assertNotNull($array_response->data);
        $this->assertNotNull($array_response->data->id);
    }

}

?>