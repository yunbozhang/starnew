<?php


if (!defined('IN_CONTEXT')) die('access violation error!');

/**
 * Message object
 * 
 */
class Message extends RecordObject {
    protected $no_validate = array(
        'isEmpty' => array(
            array('username', 'Missing nickname!'),
            array('email', 'Missing E-mail!'),
            array('tele', 'Missing telephone!'),
            array('message', 'Missing message!'),
            array('create_time','Missing create time!')
        )
    );
    
    protected $yes_validate = array(
        '_regexp_' => array(
    		array('/^[ _a-z0-9- ]+(\.[a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/','email', 'Invalid email address!')
        )
    );
}
?>