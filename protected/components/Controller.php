<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/default';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    public function returnCode($table, $pattern, $zeros = 5, $select = "code", $orderby = "id") {

        //1. Get Last Code From DB
        $lastCode = Yii::app()->db->createCommand("SELECT $select FROM $table WHERE code LIKE '$pattern%' ORDER BY  $orderby DESC LIMIT 1 ")->queryAll();

        //2. Explode the Last Code if Existed and Assign new code
        if (count($lastCode) > 0) {

            $exploded = explode($pattern, $lastCode[0][$select]);

            if (count($exploded) <= 1) {
                $newcode = str_pad(1, $zeros, '0', STR_PAD_LEFT);
                $code = $pattern . $newcode;
            } else {
                $newcode = str_pad(intval($exploded[1]) + 1, $zeros, '0', STR_PAD_LEFT);
                $code = $pattern . $newcode;
            }
        } else {
            $newcode = str_pad(1, $zeros, '0', STR_PAD_LEFT);
            $code = $pattern . $newcode;
        }
        return $code;
    }

    public function returnInvCode() {

        //1. Get Last Code From DB
        $lastCode = Yii::app()->db->createCommand("SELECT code FROM invoice ORDER BY code DESC LIMIT 1 ")->queryRow();

        //2. Explode the Last Code if Existed and Assign new code
        if (count($lastCode) > 0) {
            $newcode = str_pad(intval($lastCode['code']) + 1, 5, '0', STR_PAD_LEFT);
            $code = $newcode;
        } else {
            $newcode = str_pad(1, 5, '0', STR_PAD_LEFT);
            $code = $newcode;
        }
        return $code;
    }

    public function returnDevice() {

        $users_id = Yii::app()->user->getId();
        $list = Yii::app()->db->createCommand("SELECt device_id FROM users_devices WHERE users_id = '$users_id' AND online = 1")->queryAll();

        $data = "";
        foreach ($list as $value) {
            $data .= $value['device_id'] . ",";
        }

        return rtrim($data, ",");
    }

    public function returnDeviceByUsersId($users_id) {

        $list = Yii::app()->db->createCommand("SELECt device_id FROM users_devices WHERE users_id = '$users_id' AND online = 1")->queryAll();

        $data = "";
        foreach ($list as $value) {
            $data .= $value['device_id'] . ",";
        }

        return rtrim($data, ",");
    }

    public function returnDeviceOptions($sellectAll = true, $allselected = false) {

        $users_id = Yii::app()->user->getId();
        $list = Yii::app()->db->createCommand("SELECT users_devices.device_id FROM users_devices,device WHERE device.id = users_devices.device_id AND users_devices.users_id = '$users_id' AND users_devices.online = 1")->queryAll();

        if ($sellectAll) {
            echo "<option value=''>Select a Location</option>";
        }

        if ($allselected) {
            $selected = " selected ";
        } else {
            $selected = "";
        }

        foreach ($list as $value) {
            $device = Device::model()->findByPk($value['device_id']);
            echo "<option $selected value='" . $device->id . "'>" . $device->code . "-" . $device->name . "</option>";
        }
    }

    public function returnStatus($int) {

        if ($int == 1) {
            return "DRAFT";
        }
        if ($int == 2) {
            return "APPROVAL PENDING";
        }
        if ($int == 3) {
            return "CONFIRMED";
        }
        if ($int == 5) {
            return "GRN DONE";
        }
        if ($int == 9) {
            return "REJECT";
        }
    }

    public function returnStatusGRN($int) {

        if ($int == 1) {
            return "DRAFT";
        }
        if ($int == 3) {
            return "CONFIRMED";
        }
        if ($int == 9) {
            return "REJECT";
        }
    }

    public function returnSRTypes($val) {

        switch ($val) {
            case "S":
                return "Sellable";
                break;
            case "NS":
                return "Non-Sellable";
                break;
            case "EX":
                return "Expired Goods";
                break;

            default:
                break;
        }
    }

    public function returnTransactionStatus($device_id) {
        //CHECK FOR OPEN DOCS        

        $pos = Yii::app()->db->createCommand("SELECT * FROM po WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $grn = Yii::app()->db->createCommand("SELECT * FROM grn WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $sr = Yii::app()->db->createCommand("SELECT * FROM sr WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $tn = Yii::app()->db->createCommand("SELECT * FROM tn WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $adju = Yii::app()->db->createCommand("SELECT * FROM adj WHERE device_id = '$device_id' AND online < 3")->query()->count();

        if ($pos > 0) {
            throw new Exception("Please Complete the Open PO before this");
        }
        if ($grn > 0) {
            throw new Exception("Please Complete the Open GRN before this");
        }
        if ($sr > 0) {
            throw new Exception("Please Complete the Open Supplier Return before this");
        }
        if ($adju > 0) {
            throw new Exception("Please Complete the Open Adjustment before this");
        }
        if ($tn > 0) {
            throw new Exception("Please Complete the Open Transfer Note before this");
        }
    }

    public function byMillions($n) {
        // first strip any formatting;
        $n = (0 + str_replace(",", "", $n));
        // is this a number?
        if (!is_numeric($n))
            return false;
        // now filter it;
        if ($n > 1000000000000)
            return round(($n / 1000000000000), 2) . ' T';
        elseif ($n > 1000000000)
            return round(($n / 1000000000), 2) . ' B';
        elseif ($n > 1000000)
            return round(($n / 1000000), 2) . ' M';
        elseif ($n > 1000)
            return round(($n / 1000), 2) . ' K';
        return number_format($n);
    }

    public function checkStockAvl($device_id) {
        $pos = Yii::app()->db->createCommand("SELECT * FROM po WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $grn = Yii::app()->db->createCommand("SELECT * FROM grn WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $sr = Yii::app()->db->createCommand("SELECT * FROM sr WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $tn = Yii::app()->db->createCommand("SELECT * FROM tn WHERE device_id = '$device_id' AND online < 3")->query()->count();
        $adju = Yii::app()->db->createCommand("SELECT * FROM adj WHERE device_id = '$device_id' AND online < 3")->query()->count();

        if ($pos > 0) {
            throw new Exception("Please Complete the Open PO before this");
        }
        if ($grn > 0) {
            throw new Exception("Please Complete the Open GRN before this");
        }
        if ($sr > 0) {
            throw new Exception("Please Complete the Open Supplier Return before this");
        }

        if ($tn > 0) {
            throw new Exception("Please Complete the Open Tranfer Note before this");
        }

        if ($adju > 0) {
            throw new Exception("Please Complete the Open Adjustment before this");
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {

            $kms = round(($miles * 1.609344), 2);
            echo $kms < 250 && $kms > 0 ? $kms . " KM" : "N/A";
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

}
