<?php

namespace App\Traits;

use App\Http\Controllers\BotAbilities\Main;
use App\Models\User;
use App\Models\WaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Stichoza\GoogleTranslate\GoogleTranslate;

trait HandleText
{
    use HandleButton, SendMessage;

    public $text_intent;

    public function text_index()
    {
        $this->find_text_intent();
        if ($this->text_intent == "greetings") {
            $this->send_greetings_message($this->userphone);
            $main_ability = new Main();
            $main_ability->begin_func();
            $this->run_action_session();
        }
        if ($this->text_intent == "run_action_steps") {
            $this->continue_session_step();
        }
        

        
    }

    public function show_menu_message()
    {
    }

    public function register_user(array $data)
    {
        $model = new User();
    }

    public function determin_text()
    {
    }

    public function find_text_intent()
    {
        $message = $this->user_message_lowered;

        $greetings = Config::get("text_intentions.greetings");
        $menu = Config::get("text_intentions.menu");
      
        if (in_array($message, $greetings)) {
            $this->text_intent = "greetings";
        } elseif (in_array($message, $menu)) {
            $this->text_intent = "menu";
        }
         elseif (isset($this->user_session_data['run_action_step'])) {
            if ($this->user_session_data['run_action_step'] == 1 ) {
                $this->text_intent = "run_action_steps";
            }
        } else {
            $this->text_intent = "others";
        }
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

    public function  runtest(array $data)
    {
        return $this->test_response($data);
    }
}
