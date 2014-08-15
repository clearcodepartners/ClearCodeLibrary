<?php
	session_start();
    /** Require Init Script */
	require_once('init.php');
    /** Listen for Login */
    login_listen();
	/** Require Header */
	require_once('header.php');
	echo "<div class='row'><div class='twelve columns'>";
	if(!$auth->logged_in()){
        echo "<div class='section login'><h4>Login</h4><form method='post' class='login'>";
        if(!empty($_GET['loggedin']) && $_GET['loggedin'] != 'y') echo new Dom('div', ['class' => 'alert danger'], "<h5><i class='icon-attention'></i> Login Failed")."</h5>";
        echo "
            <div class='row'><div class='twelve columns'><div class='field metro'><input class='xxwide text input u' type='text' name='u' placeholder='Username'></div></div></div>
            <div class='row'>
                <div class='ten columns'><div class='field metro'><input class='xxwide password input p' type='password' name='p' placeholder='Password'></div></div>
                <div class='push_one one columns'>
                    <div class='primary medium btn' style='float:right;'><a href='#'><i class='icon-login'></i></a></div>
                </div>
                <input type='submit' value='Login' style='display: block; height: 1px; width: 1px; position: fixed; top: 0; left: -10000px;'>
            </div>
        </form></div>
            <div class='section'>
                <h4>Other Options</h4>
                <div class='row'>
                    <div class='six columns medium btn secondary'><a href='?register=y'>Register</a></div>
                    <div class='six columns medium btn primary'><a href='?forgot=y'>Forgot Password?</a></div>
                </div>
            </div>
        ";
    }
	else{
        echo "
        <h3 class='lead'>Demo Form</h3>
        <div class='section'>
                <h4 class='lead'>Basic Info</h4>
                <div class='ajax_auto_save' edit='Name' edit_id='{$user->person->name->id}'>
                    <div class='row'>
                        <div class='three columns'>".   style_dropdown('prefix', 'Prefix', $user->config['prefix_options'], $user->person->name->prefix).   "</div>
                        <div class='three columns'>".   style_field(style_text_input( 'first', 'First', $user->person->name->first, 'xxwide' )).            "</div>
                        <div class='three columns'>".   style_field(style_text_input( 'middle', 'Middle', $user->person->name->middle, 'xxwide')).          "</div>
                        <div class='three columns'>".   style_field(style_text_input( 'last', 'Last', $user->person->name->last, 'xxwide' )).               "</div>
                    </div>
                    <div class='row'>
                        <div class='three columns'>".   style_dropdown('suffix', 'Suffix', $user->config['suffix_options'], $user->person->name->suffix).   "</div>
                        <div class='five columns'>".    style_field(style_text_input( 'title', 'Title', $user->person->name->title, 'xxwide' )).            "</div>
                        <div class='four columns'>".    style_field(style_text_input( 'nickname', 'Nickname', $user->person->name->nickname, 'xxwide' )).   "</div>
                    </div>
                </div>
                <div class='row'>
                    <div class='five columns ajax_auto_save' edit='Person' edit_id='{$user->person->id}'>".style_dropdown('gender', "Gender", [ 'Male' => "Male", 'Female' => "Female" ], $user->person->gender)."</div>
                    <div class='two columns ajax_auto_save' edit='Social' edit_id='{$user->person->ssn->id}'>". style_field(style_text_input('block1', "XXX", $user->person->ssn->block1, 'xxwide')) ."</div>
                    <div class='two columns ajax_auto_save' edit='Social' edit_id='{$user->person->ssn->id}'>". style_field(style_text_input('block2', "XX", $user->person->ssn->block2, 'xxwide')) ."</div>
                    <div class='three columns ajax_auto_save' edit='Social' edit_id='{$user->person->ssn->id}'>". style_field(style_text_input('block3', "XXXX", $user->person->ssn->block3, 'xxwide')) ."</div>
                </div>
                <div class='row'>
                    <div class='twelve columns ajax_auto_save' edit='Person' edit_id='{$user->person->id}'>
                        ".style_dropdown('occupation', 'Occupation', $user->config['occupation_options'], $user->person->occupation)."
                    </div>
                </div>
            </div>
            <div class='section category' type='Address'  cat_id='{$user->person->addresses->id}' def_title='Other'>
                <div style='float:right; margin-top: 10px;' class='category_add medium primary btn'><a href='#'><i class='icon-list-add'></i></a></div>
                <h4 class='lead'>Addresses</h4>
                ";
                $address_template = "
                    <div class='ajax_auto_save %s' style='%s' edit='Address' edit_id='%s'>
                        <div class='row'>
                            <div class='ten columns'>".   style_field(style_text_input( '_title',        'Type', '%s', 'xxwide' ))."</div>
                            <div class='push_one one columns'><div class='primary medium btn delete' style='float:right;'><a href='#'><i class='icon-minus'></i></a></div></div>
                        </div>
                        <div class='row'><div class='twelve columns'>".style_field(style_text_input( 'street1', 'Street Address', '%s', 'xxwide' ))."</div></div>
                        <div class='row'><div class='twelve columns'>".style_field(style_text_input( 'street2', 'Street Address 2', '%s', 'xxwide' ))."</div></div>
                        <div class='row'>
                            <div class='four columns'>".   style_field(style_text_input( 'city', 'City', '%s', 'xxwide' )).             "</div>
                            <div class='four columns'>%s</div>
                            <div class='four columns'>".   style_field(style_text_input( 'zip', 'Zip', '%s', 'xxwide' )).                "</div>
                        </div>
                    </div>
                ";
                printf($address_template, 'template', 'display:none;', '', $title, '', '', '', style_dropdown('state', 'State', $user->config['state_options'], ''), '');
                foreach($user->person->addresses as $title => $address) printf($address_template, '', '', $address->id, $title, $address->street1, $address->street2, $address->city, style_dropdown('state', 'State', $user->config['state_options'], $address->state), $address->zip );
            echo "
            </div>
            <div class='section category' type='Phone'  cat_id='{$user->person->phone_numbers->id}' def_title='Other'>
                <div style='float:right; margin-top: 10px;' class='category_add medium primary btn'><a href='#'><i class='icon-list-add'></i></a></div>
                <h4 class='lead'>Phone Numbers</h4>";
                $phone_template = "<div class='ajax_auto_save %s' style='%s' edit='Phone' edit_id='%s'>
                    <div class='row'>
                        <div class='three columns'>".   style_field(style_text_input( '_title',        'Type', '%s', 'xxwide' ))."</div>
                        <div class='two columns'>".     style_field(style_text_input( 'area_code',     'XXX',  '%s', 'xxwide' ))."</div>
                        <div class='two columns'>".     style_field(style_text_input( 'prefix',        'XXX',  '%s', 'xxwide' ))."</div>
                        <div class='two columns'>".   style_field(style_text_input( 'line_number',   'XXXX', '%s', 'xxwide' ))."</div>
                        <div class='one columns'>".     style_field(style_text_input( 'extension',     'Ext',  '%s', 'xxwide' ))."</div>
                        <div class='push_one one columns'><div class='primary medium btn delete' style='float:right;'><a href='#'><i class='icon-minus'></i></a></div></div>
                    </div>
                </div>";
                printf($phone_template, 'template', 'display:none;', '', 'Other', '', '', '', '');
                foreach($user->person->phone_numbers as $title => $phone) printf($phone_template, '', '', $phone->id, $title, $phone->area_code, $phone->prefix, $phone->line_number, $phone->extension );
                echo "
            </div>
            <div class='section category' type='Email'  cat_id='{$user->person->email_addresses->id}' def_title='Other'>
                <div style='float:right; margin-top: 10px;' class='category_add medium primary btn'><a href='#'><i class='icon-list-add'></i></a></div>
                <h4 class='lead'>Email Addresses</h4>";
                $email_template = "<div class='ajax_auto_save %s' style='%s' edit='Email' edit_id='%s'>
                    <div class='row'>
                        <div class='five columns'>".   style_field(style_text_input( '_title',        'Type', '%s', 'xxwide' ))."</div>
                        <div class='five columns'>".     style_field(style_text_input( 'email',         'Email',  '%s', 'xxwide' ))."</div>
                        <div class='push_one one columns'><div class='primary medium btn delete' style='float:right;'><a href='#'><i class='icon-minus'></i></a></div></div>
                    </div>
                </div>";
        printf($email_template, 'template', 'display:none;', '', 'Other', '');
        foreach($user->person->email_addresses as $title => $email) printf($email_template, '', '', $email->id, $title, $email->email );
        echo "
            </div>
            <div class='section category' type='Question'  cat_id='{$user->survey->id}' def_title='Question'>
                <div style='float:right; margin-top: 10px;' class='category_add medium primary btn'><a href='#'><i class='icon-list-add'></i></a></div>
                <h4 class='lead'>Survey</h4>";
                $question_template = "<div class='ajax_auto_save %s' style='%s' edit='Question' edit_id='%s'>
                    <div class='row'>
                        <div class='three columns'>".   style_field(style_text_input( '_title',        'Type', '%s', 'xxwide' ))."</div>
                        <div class='two columns'>%s</div>
                        <div class='two columns'>".   style_field(style_text_input( 'frequency', 'Frequency', '%s', 'xxwide' ))."</div>
                        <div class='three columns'>%s</div>
                        <div class='push_one one columns'><div class='primary medium btn delete' style='float:right;'><a href='#'><i class='icon-minus'></i></a></div></div>
                        <div class='row'><div class='twelve columns'>".style_field(style_text_input( 'notes', 'Notes', '%s', 'xxwide' ))."</div></div>
                    </div>
                </div>";
        $units = [
            'second'    => 'Per Second',
            'minute'    => 'Per Minute',
            'hour'      => 'Per Hour',
            'day'       => 'Per Day',
            'week'      => 'Per Week',
            'month'     => 'Per Month',
            'year'      => 'Per Year'
        ];
        printf($question_template, 'template', 'display:none;', '', 'Other', style_dropdown('use', 'Uses',['true' => 'Yes', 'false' => 'No'], ''),'', style_dropdown('unit', 'Unit', $units, ''),'');
        foreach($user->survey as $title => $question) printf($question_template, '', '', $question->id, $title, style_dropdown('use', 'Uses',['true' => 'Yes', 'false' => 'No'], $question->use ? 'true' : 'false' ), $question->frequency, style_dropdown('unit', 'Unit', $units, $question->unit), $question->notes );
        echo "
            </div>
        ";
	}
    /*
    echo "<div class='section calendar'>";
    $month_done = false;
    $days = [ 7 => 'S', 1 => 'M', 2 => 'T', 3 => 'W', 4 => 'T', 5 => 'F', 6 => 'S'];
    $month = date('n');
    $day = 1;
    echo "<div class='row'><div class='two columns'></div><div class='one columns leftborder'></div>";
    foreach($days as $dayN => $dayL) echo "<div class='one columns ttl day '>{$dayL}</div>";
    echo "</div>";
    while(!$month_done){
        echo "<div class='row'><div class='two columns'></div><div class='one columns leftborder'></div> ";
        foreach($days as $dayN => $dayL){
            if($day === 1) {
                if( date('N', mktime(null, null, null, $month, $day, date('y'))) != $dayN) {
                    echo "<div class='one columns day blankz'></div>";
                    continue;
                }
            }
            if($day > date('t', mktime(null, null, null, $month, $day, date('y')) )){
                $month_done = true;
                echo "<div class='one columns day blankz'></div>";
                continue;
            }
            echo "<div class='one columns day'>{$day}</div>";
            $day++;
        }
        echo "</div>";
    }
    echo "</div></div></div>";
    */
	require_once('footer.php');