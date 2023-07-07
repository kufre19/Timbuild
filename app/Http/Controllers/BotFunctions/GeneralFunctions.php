<?php
namespace App\Http\Controllers\BotFunctions;

use App\Http\Controllers\BotAbilities\Main;
use App\Http\Controllers\BotController;
use App\Models\WaUser;
use Illuminate\Http\Request;
use stdClass;
use Stichoza\GoogleTranslate\GoogleTranslate;


class GeneralFunctions extends BotController {
    public $username;
    public $user_session_data;
    public $userphone;
    public $user_message_original;
    

    // public function __construct($phone,$username,$user_message_original="")
    // {
    //     $this->userphone = $phone;
    //     $this->username = $username;
    //     info($user_message_original);
    //     $this->user_message_original = $user_message_original;
        
    // }

    public function __construct()
    {
        
        parent::__construct(session()->get("request_stored"));
        $this->fetch_user_session();

    }

   


    public function set_properties($value,$property)
    {
        $this->$property = $value;
    }

   
    public function message_user($message,$phone="")
    {
        if($phone=="")
        {
            $phone = $this->userphone;
        }

        $text = $this->make_text_message($message,$phone);
        $this->send_post_curl($text);
    }

    public function MenuArrayToObj($menu_items_arr)
    {
        $obj = new stdClass();
        foreach ($menu_items_arr as $value) {
            $obj->{$value} = ['name' => $value];
        }
        return $obj;
    }

    public function backToMainMenu()
    {
        $main_obj = new Main;
        $main_obj->begin_func();
    }


    public function saveUserSettingsToDb()
    {
        $user_model = new WaUser();
        $stored_answers = $this->user_session_data['answered_questions'];

        $user_model->where("whatsapp_id",$this->userphone)->update([

            "name"=>$stored_answers['name'],
            "number"=>$stored_answers['number'],
            "country"=>$stored_answers['country'],
            "city"=>$stored_answers['city'],
            "lang"=>$stored_answers['lang'],

        ]);
        

    }

    public function getLanguageTrans($text)
    {
        $userModel = new WaUser();
        $user = $userModel->where("whatsapp_id",$this->userphone)->first();

        $language =  $user->lang ?? $this->user_session_data['language'] ?? "";

        if($language =="")
        { 
            return $text;
        }
        if($language != "en"){
            $googleTrans = new GoogleTranslate();
            $result = $googleTrans->setTarget($language)->translate($text);
            return $result;
        }else{
            return $text;
        }
        

    }

    

}