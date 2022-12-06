<?php

use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

function get_default_lang(){
    return   Config::get('app.locale');
}

function limit_words($text, $limit) {
    $words = explode(" ",$text);
    return implode(" ",array_splice($words,0,$limit));
}

function generateForgetPasswordNumber() {
    $number = mt_rand(10000, 99999); // better than rand()

    // call the same function if the barcode exists already
    if (User::where('forgetCode', $number)->exists()) {
        return $this->generateForgetPasswordNumber();
    }
    // otherwise, it's valid and can be used
    return $number;
}

function uploadImage($folder, $image)
{
    $filename = $image->hashName();
    $path2 = base_path("images/".$folder);
    $image->move($path2,$filename);
    $path = 'images/' . $folder . '/' . $filename;
    return $path;
}

function sendmessage( $user, $title , $body)
{

    $token = $user->firebase_token;
    $from = "AAAATM8SrYc:APA91bEgIIexLUbRu9rEeP5nKhnfgSBJRV_NXB9PQoWXVURv1sVHwa4ajwTrr_zdEeFS_6CFFwMTNIKB0EOPAj8mzmBLTTjngn1TEImeF1WnIBRfXcD9x74Za8Aw7nCBjjVALOuzs9ux";
    $msg = array
            (
            'body'     => $body,
            'title'    => $title,
            'receiver' => 'erw',
            'icon'     => "https://salon-eljoker.com/images/joker.jpg",/*Default Icon*/
            'vibrate'	=> 1,
            'sound'		=> "http://commondatastorage.googleapis.com/codeskulptor-demos/DDR_assets/Kangaroo_MusiQue_-_The_Neverwritten_Role_Playing_Game.mp3",
            );

    $fields = array
            (
                'to'        => $user->firebase_token,
                'notification'  => $msg
            );

    $headers = array
            (
                'Authorization: key=' . $from,
                'Content-Type: application/json'
            );
    //#Send Reponse To FireBase Server
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    $result = curl_exec($ch );
    curl_close( $ch );

    //store notifications
    Notification::create([
        'user_id'   => $user->id,
        'title'     => $title,
        'body'      => $body,
        'is_active' => true,
    ]);
    //$res = json_decode($result);
    // return $result;
}

