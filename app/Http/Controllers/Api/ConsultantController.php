<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

//Models
use App\Models\User;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\Reservation;
use App\Models\Subcategory;

//Resources
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\SubcategoryResource;


class ConsultantController extends Controller
{
    use GeneralTrait;
    public function __construct() {
        $this->middleware(['api', 'jwt.verify'], ['except' => ['login', 'register']]);
    }

    public function login(Request $request){

    	$validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        if ($token = Auth::guard('api')->attempt(['email' => $request->email, 'password' => $request->password , 'type' => 1, 'is_active' => 1])) 
        {
            $user = Auth::guard('api')->user();
            $input['firebase_token'] = $request->firebase_token;
            $user->update($input);
            $user->token = $token;
            $user = new UserResource($user);
            return $this->returnData('user', $user);
        }
        else
        {
            return $this->returnError('E001', 'بيانات الدخول غير صحيحة');
        }
    }
    

    public function register(Request $request) 
    {
        try
        {
            //validation
            $rules = [
                'name'            => 'required',
                'email'           => 'required|email|unique:users,email',
                'password'        => 'required|confirmed',
                'phone'           => 'required',
            ];
            $validator = Validator::make($request->all(), $rules, [
                'name.required'         => 'اسم المستخدم مطلوب',
                'email.required'        => 'البريد الالكترونى مطلوب',
                'email.email'           => 'صيغة الايميل غير صحيحة',
                'email.unique'          => 'البريد الالكترونى موجود من قبل',
                'password.required'     => 'كلمة المرور مطلوبة',
                'password.confirmed'    => 'كلمة المرور التأكيدية لا تطابق مع كلمة المرور',
                'phone.required'        => 'رقم الجوال مطلوب',
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $image = '';
            if ($request->has('image')) {
                $image = uploadImage('consultants', $request->image);
            }
            $education_certificates = [];
            if ($request->hasfile('education_certificate')) {
                foreach ($request->file('education_certificate') as $file) 
                {
                    $education_certificates[] = uploadImage('consultants', $file);
                }
            }
            $experience_certificates = [];
            if ($request->hasfile('experience_certificate')) {
                foreach ($request->file('experience_certificate') as $file) 
                {
                    $experience_certificates[] = uploadImage('consultants', $file);
                }
            }
            
            $user = User::create([
                'type'                    => 1,
                'name'                    => $request->name,
                'email'                   => $request->email,
                'password'                => bcrypt($request->password),
                'phone'                   => $request->phone,
                'additional_phone'        => $request->additional_phone,
                'birth_of_year'           => $request->birth_of_year,
                'gender'                  => $request->gender,
                'firebase_token'          => $request->firebase_token,
                'years_of_experience'     => $request->years_of_experience,
                'education'               => $request->education,
                'experience'              => $request->experience,
                'image'                   => $image,
                'subcatgory_id'           => $request->subcatgory_id,
                'education_certificate'   => implode(',', $education_certificates),
                'experience_certificate'  => implode(',', $experience_certificates),
            ]);
            return $this->returnSuccessMessage('تم انشاء الحساب بنجاح');
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
        
    }
    
    public function subcategories() 
    {
        try
        {
            $subcategories = SubcategoryResource::collection(Subcategory::Active()->get());
            return $this->returnData('subcategories', $subcategories);
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function userProfile()
    {
        try
        {
            $consultant = Auth::guard('api')->user();
            $consultant = new ProfileResource($consultant);
            return $this->returnData('consultantProfile', $consultant);
        }
        catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }


    public function waitingReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['consultant_id' => Auth::guard('api')->user()->id , 'status' => 'waiting'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function oldReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['consultant_id' => Auth::guard('api')->user()->id , 'status' => 'old'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function comingReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['consultant_id' => Auth::guard('api')->user()->id , 'status' => 'coming'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function rejectedReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['consultant_id' => Auth::guard('api')->user()->id , 'status' => 'reject'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function reject($id)
    {
        try
        {
            $reservation = Reservation::find($id);
            $reservation->update([
                'status'             => 'reject',
            ]);
            return $this->returnSuccessMessage('تم رفض طلب الاستشارة ');            
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function accept($id)
    {
        try
        {
            $reservation = Reservation::find($id);
            $reservation->update([
                'status'             => 'coming',
            ]);
            return $this->returnSuccessMessage('شكرا لقبولك طلب الاستشارة نتمنى لك تجربة ممتعة');
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    // public function consultantData()
    // {
    //     try
    //     {
    //         $user = Auth::guard('api')->user();
    //         $user = new ProfileResource($user);
    //         return $this->returnData('userProfile', $user);
    //     }catch (\Exception $e) {
    //         return $this->returnError(404, $e->getMessage());
    //     }
    // }

}
