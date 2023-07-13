<?php

namespace App\Http\Controllers\BotAbilities;

use App\Http\Controllers\BotFunctions\GeneralFunctions;
use App\Traits\GeneralAbilities;

class LocateStore extends GeneralFunctions implements AbilityInterface
{
    use GeneralAbilities;

    public $steps = ["begin_func", "getStoreLocation", "getConsent","CheckConsentSelection"];
    public const USER_REGION = "user_region";
    public const STORE_SELECTED = "store_selected";



    public function begin_func()
    {
        $this->set_session_route("LocateStore");
        $message = <<<MSG
        Let us help you! We currently only have stores located in 5 Provinces and growing.
        Please select your region to make your store selection easier. Your need only reply with the corresponding number:
        MSG;
        $this->listRegion($message);

        $this->returnHomeMessage();

        $this->go_to_next_step();
        $this->ResponsedWith200();
    }


    public function getStoreLocation()
    {
        // $this->send_post_curl($this->make_text_message($this->user_message_original,$this->userphone));
        // $this->ResponsedWith200();

        // check first for selection to be correct
        $user_selected = $this->user_message_original;
        $this->CheckRegionSelected($user_selected);

      
        // second check for selection of others
        if ($user_selected == "5" || $this->user_message_lowered == "others") {
            $message = <<<MSG
            We’re sorry you’re located in a province we currently do not have any stores, but we’re positive one will be opening soon.
            MSG;
            $this->send_post_curl($this->make_text_message($message, $this->userphone));
            $this->returnHomeMessage();
            $this->ResponsedWith200();
        }

          // store collected region
          $this->storeAnswerToSession(['store_as'=>self::USER_REGION]);

          $store_menu = $this->listStoreInRegion($user_selected);
          $message = <<<MSG
          Fantastic. Here is a list of our stores located in your chosen province.
          MSG;
          $this->sendstoreMenu($store_menu,$message);
          $this->returnHomeMessage();
          $this->go_to_next_step();
          $this->ResponsedWith200();


    }


    public function getConsent()
    {
        $answers = $this->user_session_data['answered_questions'];
        $region = $answers[self::USER_REGION];
        // check store location selected
        $store_menu = $this->listStoreInRegion($region);
        $this->checkStoreSelected($store_menu,$this->user_message_original);

        // check if need to be returned to province menu
        if($this->goBackToRegionSelection($region,$this->user_message_original))
        {
            return $this->begin_func();
        }

        // store the store-location selected 
        $store = $this->fetchStoreSelected($region,$this->user_message_original);
        $this->storeAnswerToSession(["store_as"=>self::STORE_SELECTED],$store->id);

        // ask for constent
        $this->connection_consent();
        $this->go_to_next_step();
        $this->ResponsedWith200();

    }

    public function CheckConsentSelection()
    {
        $answers = $this->user_session_data['answered_questions'];
        $store = $answers[self::STORE_SELECTED];

        $this->checkConnectionConsent($this->user_message_original,$store);
        $this->ResponsedWith200();
    }

    function call_method($key)
    {
        $method_name = $this->steps[$key];
        $this->$method_name();
    }
}
