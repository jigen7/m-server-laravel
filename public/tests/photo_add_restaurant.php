<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once(dirname(__FILE__) . '/core/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/core/request_utils.php');
require_once(dirname(__FILE__) . '/core/xml_parser.php');

class PhotoTests extends UnitTestCase {
    
    var $addedPhotoId = 0;
    
    function testAdd() {
        echo "Test Case: Photo Add Restaurant <br /><br />";
        $url = 'http://homestead.app/photos/upload/restaurant';

        $params = array(
            'Restaurant' => array(
                'restaurant_id' => 1,
                'user_id' => 4,
            ),
            'Photos' => array(
                array(
                    'Photo' => array(
                        'filename' => 'testimage1.jpg',
                        'text' => 'Sample Description 1 Again'
                    )
                ),
                array(
                    'Photo' => array(
                        'filename' => 'testimage2.png',
                        'text' => 'Sample Description 2 Again'
                    )
                )
            )
        );


        $filepath = dirname(__FILE__);
        $attachments = array(
            '@'.$filepath.'/testimage1.jpg',
            '@'.$filepath.'/testimage2.png'
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
