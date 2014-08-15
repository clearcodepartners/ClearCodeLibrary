<?php
/**
 * ClearCodeLibrary Testing Init
 * @package ClearCode
 */
    require_once('config.php');
    $metadata_info = [];
    if($config['debug']){
        /** Error Handling */
        ini_set("display_errors", 'on');
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR);
    }
    else{
        ini_set("display_errors", 'off');
        error_reporting(E_ERROR);
    }

	/** Root Directory */
	define("ROOT_DIR", dirname(__FILE__));

	/** Get Init Functions */
	require_once('functions.php');

    if(!$db->select_var('user', 'id', [])){
        $user = new User('admin', 'password');
        $user->user_level = 20;
        $user->person->name->prefix     = 'Dr';
        $user->person->name->first      = 'Test';
        $user->person->name->middle     = 'T';
        $user->person->name->last       = 'Tester';
        $user->person->name->suffix     = 'MD';
        $user->person->name->title      = 'Ear, Nose, and Throat Facial Plastic Surgeon';
        $user->person->name->nickname   = 'Testy';

        $user->person->addresses->add('Home');

        $user->person->addresses->Home->street1 = '50 Test St';
        $user->person->addresses->Home->street2 = 'APT 2';
        $user->person->addresses->Home->city    = 'Test City';
        $user->person->addresses->Home->state   = 'ME';
        $user->person->addresses->Home->zip     = '04055';

        $user->person->phone_numbers->add('Personal');
        $user->person->phone_numbers->Personal->area_code   = 123;
        $user->person->phone_numbers->Personal->prefix      = 456;
        $user->person->phone_numbers->Personal->line_number = 7890;
        $user->person->phone_numbers->Personal->extension   = 123;

        $user->person->email_addresses->add('Personal');
        $user->person->email_addresses->Personal->email = "test@tester.com";

        $user->person->gender = "Male";

        $user->person->ssn->block1 = "123";
        $user->person->ssn->block2 = "45";
        $user->person->ssn->block3 = "6789";

        $user->person->occupation = "Plastic Surgeon";

        $user->survey->add('Question 1');
        $user->survey->{'Question 1'}->use          = true;
        $user->survey->{'Question 1'}->frequency    = "5";
        $user->survey->{'Question 1'}->unit         = "day";
        $user->survey->{'Question 1'}->notes        = "Test";
        $user->survey->add('Question 2');
        $user->survey->{'Question 2'}->use          = false;
        $user->survey->{'Question 2'}->frequency    = "0";
        $user->survey->{'Question 2'}->unit         = "week";
        $user->survey->{'Question 2'}->notes        = "Test 2";
        $user->survey->add('Question 3');
        $user->survey->{'Question 3'}->use          = true;
        $user->survey->{'Question 3'}->frequency    = "10";
        $user->survey->{'Question 3'}->unit         = "year";
        $user->survey->{'Question 3'}->notes        = "Test 3";
        $user->survey->add('Question 4');
        $user->survey->{'Question 4'}->use          = true;
        $user->survey->{'Question 4'}->frequency    = "1";
        $user->survey->{'Question 4'}->unit         = "month";
        $user->survey->{'Question 4'}->notes        = "Test 4";

    }