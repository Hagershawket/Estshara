<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Models\User;
use App\Models\Contact;
use App\Models\Setting;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\AboutResource;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    use GeneralTrait;
    public function __construct() {
        $this->middleware(['api', 'jwt.verify'], ['except' => ['sendForgetCode', 'verifyCode', 'resetPassword','logout']]);
    }

    public function sendForgetCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone'  => 'required',
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                return $this->returnError('E005','رقم الهاتف  غير صحيح ');
            }
            $user->update([
                'forgetCode'    => generateForgetPasswordNumber(),
            ]);
            // $link = "https://www.hisms.ws/api.php?send_sms&username=966555990393&password=gSDr@56@@QL5ghW&numbers=966" . $user->phone . "&sender=AZmarketing&message=كود اعادة تعيين كلمة المرور  : " . $user->forgetCode;
            // $res = Http::get($link);
            return $this->returnSuccessMessage('تم ارسال الكود الي  الهاتف');
        }catch (\Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function verifyCode(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'forgetCode'     => 'required',
                'phone'          => 'required',
            ]);
    
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            
            $user = User::where(['phone' => $request->phone, 'forgetCode' => $request->forgetCode])->first(); 
            if($user)
                return $this->returnSuccessMessage('الكود صحيح');
            else
                return $this->returnError('001','الكود غير صحيح');
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function resetPassword(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'phone'     => 'required',
                'password'  => 'confirmed',
            ]);
    
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
    
                $user = User::where('phone', $request->phone)->first();
                if(!$user)
                {
                    return $this->returnError('001','رقم الهاتف غير صحيح');
                }
    
                $user->update([
                    'password'    => bcrypt($request->password),
                    'forgetCode'  => Null,
                ]);
                return $this->returnSuccessMessage('تم تغيير كلمة المرور بنجاح');
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
        
    }
    

    public function logout(Request $request) 
    {
        $token = $request ->token;
            try {
                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('', 'حدث شىء ما خطأ');
            }
            return $this->returnSuccessMessage('تم تسجيل الخروج بنجاح');
    }

    public function delAccount() 
    {
        try
        {
            $user = Auth::guard('api')->user();
            $user->update([
                'is_active'  => 0,
            ]);
            return $this->returnSuccessMessage('تم حذف الحساب بنجاح');
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }
    
    public function termsConditions() 
    {
        $termsConditions = Setting::first()->terms_conditions ?? '';
        return $this->returnData('termsConditions', $termsConditions);
    }
    public function aboutUs() 
    {
        $data = new AboutResource(Setting::first());
        return $this->returnData('data', $data);
    }
    public function contactUs(Request $request) 
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name'     => 'required',
                'email'    => 'required|email',
                'message'  => 'required',
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            Contact::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'message'      => $request->message,
            ]);
            return $this->returnSuccessMessage('تم ارسال الرسالة بنجاح');
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }
}
