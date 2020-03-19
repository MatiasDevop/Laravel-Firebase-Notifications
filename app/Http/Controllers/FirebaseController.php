<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseController extends Controller
{

    
    public function index(){

        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://firebas-message.firebaseio.com/')
        ->create();

        $database   =   $firebase->getDatabase();
        $createPost    =   $database
        ->getReference('blog/posts')
        ->push([
            'title' =>  'Laravel 6',
            'body'  =>  'This is really a cool database that is managed in real time.'

        ]);
            
        echo '<pre>';
        print_r($createPost->getvalue());
        echo '</pre>';
    }

    public function getData(){

        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');
        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        ->withDatabaseUri('https://firebas-message.firebaseio.com/')
        ->create();

        $database   =   $firebase->getDatabase();
        $createPost    =   $database->getReference('blog/posts')->getvalue();      
        return response()->json($createPost);
    }

    public function sendMessage(){

        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');

        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        // The following line is optional if the project id in your credentials file
        // is identical to the subdomain of your Firebase project. If you need it,
        // make sure to replace the URL with the URL of your project.
        //->withDatabaseUri('https://my-project.firebaseio.com')
        ->create();

        $messaging = $firebase->getMessaging();
        $deviceToken = 'cMuRaF8DQKKigO_n0p3ElT:APA91bFM10NGd6EPhR45exUb9ZsiQLH4BgWad2EpdchELw9Yg24HsFMPgZlmk5wtRsM9Xr2K5mDhYH8nmbheCRTsbMlpoqRYOnBC7ScOA3EmUuYUgRCz06XwPqjiOWLjdF-gJOhYBCSg';
        // $notification = Notification::create("test", "body");
        //     $data = [
        //     'first_key' => 'First Value',
        //     'second_key' => 'Second Value',
        //     ];
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create('Push notification', 'Hey dude'));
            //->withData(['key' => 'value']); Optional
        
        $messaging->send($message);
    
    }

    public function sendToTopic(){

        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');

        $firebase = (new Factory)
        ->withServiceAccount($serviceAccount)
        // The following line is optional if the project id in your credentials file
        // is identical to the subdomain of your Firebase project. If you need it,
        // make sure to replace the URL with the URL of your project.
        //->withDatabaseUri('https://my-project.firebaseio.com')
        ->create();
        $messaging = $firebase->getMessaging();

        $topic = 'afiliado';
        $title = 'My Notification Title';
        $body = 'My Notification Body';
        $imageUrl = 'https://api.androidhive.info/images/minion.jpg';

        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
            'image' => $imageUrl,
        ]);


        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification);
            //->withData(['key' => 'value']); Optional
        
            // $message = CloudMessage::fromArray([
            //     'topic' => $topic,
            //     'notification' => $notification//[/* Notification data as array */], // optional
            //     //'data' => [/* data array */], // optional
            // ]);

        $messaging->send($message);
    
    }
}
