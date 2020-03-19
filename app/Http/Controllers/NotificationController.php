<?php

namespace App\Http\Controllers;


use App\Libraries\Push;
//use Firebase;

use App\Libraries\Firebase;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function notify(){

        $data = json_decode(\request()->getContent());
        
        //$sender                 = $data->sender_user;
        $receiver               = $data->receiver_user;
       // $notification_payload   = $data->payload;
        $notification_title     = $data->title;
        $notification_message   = $data->message;
        $notification_push_type = $data->push_type;
        $notification_image = $data->image;

        try{
           // $sender_id = $sender;
            
            $receiver_id = $receiver;

            $firebase = new Firebase();
            $push = new Push();

            // optional payload

            //$payload = $notification_payload;

            $title = $notification_title ?? '';

            //notification message
            $message = $notification_message ?? '';

            //push type - single user / topic
            $push_type = $notification_push_type ?? '';

            $push->setTitle( $title );
            $push->setMessage( $message );
           // $push->setPayload( $payload );

            $datajson=[
                "to" => $receiver_id,
                "notification" => [
                    "title" => $notification_title ,
                    "body" => $notification_message,
                    "image" => $notification_image
                ],
                // "data" => [
                //     "ANYTHING EXTRA HERE"
                // ]
                ];

            $json     = '';
            $response = '';

            if( $push_type === 'topic' ){
                $json     = $datajson;//$push->getPush();
                $response = $firebase->sendToTopic($receiver_id, $json["notification"]);//'global', $json);
                return response()->json([
                    'response' => $response
                ]);
            }else if($push_type === 'individual' ){
                $json     = $push->getPush();
                $regId    = $receiver_id ?? '';
                $response = $firebase->send($regId, $json);

                return response()->json([
                    'response' => $response
                ]);
            }


            

        }catch ( \Exception $ex ) {
            return response()->json( [
               'error'   => true,
               'message' => $ex->getMessage()
            ] );
         }
    }
}
