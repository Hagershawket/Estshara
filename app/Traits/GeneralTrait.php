<?php

namespace App\Traits;
use App\Models\Notification;

trait GeneralTrait
{

    public function returnError($errNum, $msg)
    {
        return response()->json([
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg
        ]);
    }


    public function returnSuccessMessage($msg = "OK", $errNum = "200")
    {
        return [
            'status' => true,
            'errNum' => $errNum,
            'msg' => $msg
        ];
    }

    public function returnData($key, $value, $msg = "تمت العملية بنجاح")
    {
        return response()->json([
            'status' => true,
            'errNum' => "200",
            'msg' => $msg,
            $key => $value
        ]);
    }


    //////////////////
    public function returnValidationError($code = "E001", $validator)
    {
        return $this->returnError($code, $validator->errors()->first());
    }


    public function returnCodeAccordingToInput($validator)
    {
        $inputs = array_keys($validator->errors()->toArray());
        $code = $this->getErrorCode($inputs[0]);
        return $code;
    }

    public function getErrorCode($input)
    {
       if ($input == "name")
           return 'E001';
       else if ($input == "email")
       return 'E002';

      else if ($input == "phone")
            return 'E003';

      else if ($input == "password")
            return 'E004';

      else
            return "";
    }



}