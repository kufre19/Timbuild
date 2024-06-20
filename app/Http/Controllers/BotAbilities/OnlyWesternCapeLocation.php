<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions;
use App\Traits\GeneralAbilities;

class OnlyWesternCapeLocation extends GeneralFunctions implements AbilityInterface
{
    use GeneralAbilities;

    public $steps = ["begin_func", "getStoreLocationInfo", "getConsent","CheckConsentSelection"];
    public const USER_REGION = "user_region";
    public const STORE_SELECTED = "store_selected";
    // public $main_menu = [
    //     "TimBuild Woodstock",
    //     "TimBuild Somerset West",
    //     "TimBuild Stellenbosch",
    // ];



    public function begin_func()
    {
        $this->set_session_route("OnlyWesternCapeLocation");
       
        $this->go_to_next_step();
        $this->ResponsedWith200();
    }


    public function getStoreLocationInfo()
    {
        // $answers = $this->user_session_data['answered_questions'];
        $user_selected = $this->user_message_original;

        if($user_selected == "1" || $this->user_message_lowered ==  "timbuild woodstock")
        {
            $this->showStoreInfoOnlyWesternCape($this->fetchStore(1));
            $this->returnHomeMessage();
            $this->ResponsedWith200();


        }else{
            // return a please select an option from the menu
            $message = "Please select from the menu given!";
            $this->send_menu_to_user($message);
            return $this->ResponsedWith200();
        }

        



    }


   
   

    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }
}
