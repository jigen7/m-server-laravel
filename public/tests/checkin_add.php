<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once(dirname(__FILE__) . '/core/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/core/request_utils.php');
require_once(dirname(__FILE__) . '/core/xml_parser.php');

class CheckinTests extends UnitTestCase {
    
    var $addedPhotoId = 0;
    
    function testAdd() {
        echo "Test Case: Checkin Add<br /><br />";
        $url = 'http://homestead.app/checkins/add';

        $params = array(
            'CheckIn' => array(
                'user_id' => 4,
                'restaurant_id' => 5,
                'message' => 'Test Check-In Text with Photo',
            ),
            'Photos' => array(
                array(
                    'Photo' => array(
                        'filename' => 'testimage1.jpg',
                        'category' => 'interior',
                        'description' => 'Sample Description 1'
                    )
                ),
                array(
                    'Photo' => array(
                        'filename' => 'testimage2.jpg',
                        'category' => 'interior',
                        'description' => 'Sample Description 2'
                    )
                )
            )
        );


        $filepath = dirname(__FILE__);
        $attachments = array(
            '@'.$filepath.'/testimage1.jpg',
            '@'.$filepath.'/testimage2.jpg'
        );

        // We send an empty array for attachments 
        // since the server is always expecting a JSON file to be uploaded
        // regardless whether there is an attached photo or not.

        $data = RequestUtils::post($url, $params, $attachments);
        echo "<pre>";
        print_r($data['content']);
        echo "</pre>";

    }


}
