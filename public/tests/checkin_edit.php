<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once(dirname(__FILE__) . '/core/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/core/request_utils.php');
require_once(dirname(__FILE__) . '/core/xml_parser.php');

class CheckinTests extends UnitTestCase {
    
    var $addedPhotoId = 0;
    
    function testAdd() {
        echo "Test Case: Checkin Edit<br /><br />";
        $url = 'http://homestead.app/checkins/edit/62';

        $params = array(
            'CheckIn' => array(
                'user_id' => 4,
                'message' => 'Test Edit',
            ),
        );


        $filepath = dirname(__FILE__);
        $attachments = array();

        // We send an empty array for attachments 
        // since the server is always expecting a JSON file to be uploaded
        // regardless whether there is an attached photo or not.

        $data = RequestUtils::post($url, $params, $attachments);
        echo "<pre>";
        print_r($data['content']);
        echo "</pre>";

    }


}
