<?php
namespace App\Traits;

use App\Http\Controllers\BotFunctions\TextMenuSelection;
use App\Models\Region;
use App\Models\StoreInfo;

trait GeneralAbilities
{


    // this will house methods that will be general in any project

    public function listRegion($message)
    {
        $regions_Arr = [];
        $region_model = new Region();
        $regions = $region_model->select("region")->get();
        foreach ($regions as $region => $value) {
            array_push($regions_Arr,$value['region']);
        }
        $region_obj = $this->MenuArrayToObj($regions_Arr);
        $txt_menu = new TextMenuSelection($region_obj);
       
        $txt_menu->send_menu_to_user($message);

    }


    public function CheckRegionSelected($response)
    {
        $regions_Arr = [];
        $region_model = new Region();
        $regions = $region_model->select("region")->get();
        foreach ($regions as $region => $value) {
            array_push($regions_Arr,$value['region']);
        }
        $region_obj = $this->MenuArrayToObj($regions_Arr);
        $txt_menu = new TextMenuSelection($region_obj);
       
        $check = $txt_menu->check_expected_response($response);
        if($check)
        {
            return true;
        }

    }

    public function listStoreInRegion($region)
    {
        if(!is_numeric($region))
        {
            $region_model = new Region();
            $region_select = $region_model->select("id")->where('region',$region)->first();
            $region = $region_select->id;

        }
        $store_model = new StoreInfo();
        $stores = $store_model->select("location")->where("region_id",$region)->get();
        $stores_Arr = [];

        foreach ($stores as $store => $value) {
            array_push($stores_Arr,$value['location']);
        }
        // extra data that's not saved in db 
        array_push($stores_Arr,"Go Back to Province selection");

        $store_obj = $this->MenuArrayToObj($stores_Arr);
        return $store_obj;


    }

    public function sendstoreMenu($store_obj,$message)
    {
        $txt_menu = new TextMenuSelection($store_obj);
        $txt_menu->send_menu_to_user($message);
       
    }

    public function returnHomeMessage ()
    {
        $this->send_post_curl($this->make_text_message("NOTE: Reply MENU at any time to return to our main menu.",$this->userphone));

    }
}
