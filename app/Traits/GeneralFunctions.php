<?php

namespace App\Traits;

use App\Exports\IncidentReportExport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\SessionConstants;

trait GeneralFunctions
{



    public function go_to_next_step($value = "")
    {
        $session = $this->user_session_data;
        $current_step_count = $session['current_step'];
        $current_step_count += 1;
        $this->user_session_data['current_step'] = $current_step_count;
        $this->update_session($this->user_session_data);
    }


    public function go_to_next_step_on_form($value = "")
    {
        $session = $this->user_session_data;
        $current_step_count = $session['form_counter'];
        $current_step_count += 1;
        $this->user_session_data['form_counter'] = $current_step_count;
        $this->update_session($this->user_session_data);
    }

    public function go_to_previous_step($value = "")
    {
        $session = $this->user_session_data;
        $current_step_count = $session['current_step'];
        $current_step_count -= 1;
        $this->user_session_data['current_step'] = $current_step_count;
        $this->update_session($this->user_session_data);
    }

    public function ask_user($value = "")
    {
        $message = $this->make_text_message($value);
        $this->send_post_curl($message);
        $this->go_to_next_step();
        die;
    }
    
   

    public function storeAnswerToSession($value = "",$data="")
    {
        if (isset($value['expect'])) {

            if ($value['expect']['type'] == "image") {
                if (isset($this->wa_image_id)) {
                    $image = $this->upload_file_from_bot();
                    $user_response = $image;
                } else {
                    $user_response = $this->user_message_original;
                }
            }
        } else {
            $user_response = $this->user_message_original;
        }
        if($data != "")
        {
            $user_response = $data;
        }

        $session = $this->user_session_data;
        $answered_question = $session['answered_questions'];
        $key = $value['store_as'];
        $this->user_session_data['answered_questions'][$key] = $user_response;
        $this->update_session($this->user_session_data);
        // $this->go_to_next_step();
        // $this->continue_session_step();
    }

  

    public function log_last_operation($value = "")
    {
        $this->user_session_data['last_operation_status'] = $value;
        $this->update_session($this->user_session_data);
    }

    public function check_last_operation($value = "")
    {
        $check_for = $value['check_for'];
        $last_operation = $this->user_session_data['last_operation_status'];
        if ($check_for == $last_operation) {
            $follow_up_action = $value['follow_up']['action_type'];
            $follow_up_action_value = $value['follow_up']['value'];

            $this->$follow_up_action($follow_up_action_value);
            $this->continue_session_step();
        } else {
            $alternate_action = $value['else']['action_type'];
            $alternate_action_value = $value['else']['value'];
            $this->$alternate_action($alternate_action_value);
            $this->continue_session_step();
        }
    }

    public function restart_this_steps($value = "")
    {

        $session = $this->user_session_data;
        $this->say_to_user($value);
        $this->user_session_data['current_step'] = 0;
        $this->update_session($this->user_session_data);
        $this->continue_session_step();
    }
    // public function load_new_action_session($value="")
    // {


    // }

    public function send_interactive_menu($value = "")
    {
        $menu_list = Config::get("interactive_menu." . $value);
        $menu_text = Config::get("interactive_menu_text." . $value) ?? "Choose Item from the Menu";

        $menu_message = $this->make_menu_message($menu_list, $this->userphone, $menu_text);
        $this->send_post_curl($menu_message);
        die;
    }

  


    public function upload_file_from_bot($value = "")
    {
        if (!isset($this->wa_image_id)) {
            $this->say_to_user("Please send an image!");
            die;
        }

        $wa_image_url_request = $this->send_get_curl_wa_media($this->wa_image_id);
        $wa_image_url = $wa_image_url_request['url'];

        // $downloaded_image = $this->send_get_curl_wa_media("",$wa_image_url);



        $data = explode("/", $wa_image_url_request['mime_type']);
        $ext = end($data);
        $image_new_name = time() . "." . $ext;
        $downloaded_image = $this->download_image($wa_image_url, $ext);

        $destination = public_path() . "/incident_report/";
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }
        File::put($destination . $image_new_name, $downloaded_image);
        $file = asset("incident_report/" . $image_new_name);

        return $file;
    }

    /**
     * this will first ask user for details to be stored
     * 
     * @param int $a nothing
     * 
     * 
     * 
     */

    public function run_the_following($value=null)
    {
        foreach ($value as $key => $function) {
            $this->$function();
        }

    }

    /**
     * this will accept an array value to be sent as menu to be selected with text response and 
     * then list of option stored to session
     * @param array $value 
     *  */

    public function send_option_menu($value=null)
    {
       
        $menu_message = $value['menu'];
        $expected_options = $value['expected_options'];
        $this->user_session_data[HandleSession::$EXPECTED_RESPONSES] = $expected_options;
        $this->update_session($this->user_session_data);
        $this->say_to_user($menu_message,2);

        // $this->continue_session_step();

    }


    public function check_for_expected_response($value=null)
    {
        // dd("ere");

        $expected_options =  $this->user_session_data[HandleSession::$EXPECTED_RESPONSES];
        $user_response = $this->user_message_original;

        if(is_array($expected_options))
        {
            if(!in_array($user_response,$expected_options))
            {
                $text = "Please select an option from the menu!";
                $this->say_to_user($text,0);
            }else{
                // do the select option
                $this->go_to_next_step();
                $this->continue_session_step();
            }
        }

    }

    public function end_steps($value = "")
    {
        if($value['text']!= "")
        {
            $this->say_to_user($value['text']);
        }
        if($value['next_journey'] != "")
        {
            $next_journey = $value['next_journey'];
            $this->$next_journey();
            $this->run_action_session();

        }else{
            $this->update_session();
            die;

        }
        
    }
}
