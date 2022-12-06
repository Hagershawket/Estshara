<?php

namespace App\Http\Controllers\Api;

use Auth;
use JWTAuth;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use App\Models\ConsultantCategory;
use App\Http\Controllers\Controller;
//Models
use App\Models\User;
use App\Models\Review;
use App\Models\Slider;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Appointment;
use App\Models\Subcategory;
use App\Models\Reservation;
use App\Models\ConsultantType;
//Resorces
use App\Http\Resources\UserResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\ConsultantResource;
use App\Http\Resources\SubcategoryResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ConsultantReviewResource;
use App\Http\Resources\ConsultantProfileResource;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ReservationResource;


use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use GeneralTrait;
    public function __construct() {
        $user = Auth::guard('api')->user();
        $this->middleware(['api', 'jwt.verify'], ['except' => ['login', 'register']]);
    }

    public function login(Request $request){

    	try
        {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
    
            if ($token = Auth::guard('api')->attempt(['email' => $request->email, 'password' => $request->password, 'type' => 0, 'is_active' => 1])) 
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
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }
    

    public function register(Request $request) 
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|string|email|max:100|unique:users,email',
                'phone'    => 'required|max:100|unique:users,phone',
                'password' => 'required|string',
            ]);
            if($validator->fails()){
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
                
                if($user = User::where(['phone'=> $request->phone, 'is_active' => 0])->orWhere(['email'=> $request->email, 'is_active' => 0])->first())
                {
                    // return $this->returnData('userProfile', $user);
                    $user = User::create([
                        'type'                    => 0,
                        'name'                    => $request->name,
                        'email'                   => $request->email,
                        'password'                => bcrypt($request->password),
                        'phone'                   => $request->phone,
                        'birth_of_year'           => $request->birth_of_year,
                        'gender'                  => $request->gender,
                        // 'firebase_token'          => $request->firebase_token,
                    ]);
                    return $this->returnSuccessMessage('تم انشاء الحساب بنجاح');
                }
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
        
    }  


    public function userProfile() 
    {
        try
        {
            $user = Auth::guard('api')->user();
            $user = new ProfileResource($user);
            return $this->returnData('userProfile', $user);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function UpdateUserProfile(Request $request) 
    {
        try
        {
            $user = Auth::guard('api')->user();
            if ($request->has('image')) {
                $image = uploadImage('users', $request->image);
                $user->update([
                    'image'       => $image,
                ]);
            }
            $user->update([
                'name'             =>  $request->name,
                'email'            =>  $request->email,
                'phone'            =>  $request->phone,
                'birth_of_year'    =>  $request->birth_of_year,
            ]);
            // return $this->returnSuccessMessage('تم التعديل بنجاح');
            $token = auth('api')->tokenById($user->id);
            $user->token = $token;
            $user = new UserResource($user);
            return $this->returnData('user', $user);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function addReview(Request $request,$id) 
    {
        try
        {
            //validation
            $rules = [
                'consultant_id'    => 'required',
                'comment'          => 'required',
                'star_num'         => 'required',            
            ];
    
            $validator = Validator::make($request->all(), $rules, [
                'consultant_id.required'  => 'حقل اسم المستشار مطلوب',
                'comment.required'        => 'حقل التعليق مطلوب',
                'star_num.required'       => 'حقل التقييم مطلوب',
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            Review::create([
                'user_id'         =>  Auth::guard('api')->user()->id,
                'consultant_id'   =>  $id,
                'comment'         =>  $request->comment,
                'star_num'        =>  $request->star_num,
                'is_active'       =>  1,
            ]);
            return $this->returnSuccessMessage('تم التقييم بنجاح');
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function subcategories($id) 
    {
        try
        {
            $subcategories = SubcategoryResource::collection(Subcategory::where('category_id', $id)->get());
            return $this->returnData('subcategories', $subcategories);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function getConsultants($id) 
    {
        try
        {
            $consultantCategory = ConsultantCategory::where('subcategory_id', $id)->get();
            $consultantCategory->flag = 0;
            $consultants = ConsultantResource::collection($consultantCategory);
            return $this->returnData('consultants', $consultants);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function blogs($id) 
    {
        try
        {
            $blogs = Blog::active()->where('category_id',$id)->get();
            $blogs->flag = 0;
            $blogs = BlogResource::collection($blogs);
            return $this->returnData('blogs', $blogs);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function blogDetails($id) 
    {
        try
        {
            $blog = Blog::where('id',$id)->first();
            $blog->flag = 1;
            $blog = new BlogResource($blog);
            return $this->returnData('blog', $blog);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function consultantReviews($id) 
    {
        try
        {
            $data = new ConsultantReviewResource(User::where('id', $id)->first());
            return $this->returnData('data', $data);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function consultantProfile($id)
    {
        try
        {
            $data = new ConsultantProfileResource(User::where('id', $id)->first());
            return $this->returnData('data', $data);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function consultantInfo($id)
    {
        try
        {
            $user = User::where('id', $id)->first();
            $user->flag = 1;
            $data = new ConsultantResource($user);
            return $this->returnData('data', $data);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function availableAppointment(Request $request)
    {
        try
        {
            $appointments = Appointment::where('consultant_id' , $request->consultant_id)->get();
            $type = ConsultantType::find($request->type_id);
            foreach($appointments as $appointment)
            {
                $appointment->duration = $type->duration;
            }
            $appointments = AppointmentResource::collection($appointments);
            return $this->returnData('appointments', $appointments);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function makeReservation(Request $request)
    {
        try
        {
            //validation
            $rules = [
                'consultant_id'    => 'required',
                'type_id'          => 'required',
                'appointment_id'   => 'required',            
            ];
    
            $validator = Validator::make($request->all(), $rules, [
                'consultant_id.required'  => 'حقل اسم المستشار مطلوب',
                'type_id.required'        => 'حقل نوع الاستشارة مطلوب',
                'appointment_id.required' => 'حقل الوقت المتاح مطلوب',
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            //store reservation data
            $reservation = Reservation::create([
                'consultant_id'      => $request->consultant_id,
                'user_id'            => Auth::guard('api')->user()->id,
                'type_id'            => $request->type_id,
                'appointment_id'     => $request->appointment_id,
                'status'             => 'waiting',
                'payment_status'     => 1,
            ]);
            //decrease num of minuits from available appointment
            $appointment = Appointment::find($request->appointment_id);
            $type = ConsultantType::find($request->type_id);
            $appointment->min_remain = $appointment->min_remain - $type->duration;
            $appointment->save();
            
            return $this->returnSuccessMessage('تم الحجز بنجاح');
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function waitingReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['user_id' => Auth::guard('api')->user()->id , 'status' => 'waiting'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function oldReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['user_id' => Auth::guard('api')->user()->id , 'status' => 'old'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function comingReservation()
    {
        try
        {
            $reservaions = ReservationResource::collection(Reservation::where(['user_id' => Auth::guard('api')->user()->id , 'status' => 'coming'])->get());
            return $this->returnData('reservaions', $reservaions);
        }catch (\Exception $e) {
            return $this->returnError(404, $e->getMessage());
        }
    }

    public function home()
    {
        $data['slider']                         = SliderResource::collection(Slider::active()->get());
        $data['categories']                     = CategoryResource::collection(Category::active()->get());
        return $this->returnData('data', $data);
    }

    // public function slider(Request $request) 
    // {
    //     try
    //     {
    //         $image = '';
    //         if ($request->has('image')) {
    //             $image = uploadImage('categories', $request->image);
    //         }
    //         Category::create([
    //             'image'           =>  $image,
    //             'name'            => $request->name,
    //             'is_active'       =>  1,
    //         ]);
    //         return $this->returnSuccessMessage('تم حفظ البيانات بنجاح');
    //     }catch (\Exception $e) {
    //         return $this->returnError(404, $e->getMessage());
    //     }
    // }

}
