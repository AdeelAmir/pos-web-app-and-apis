<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class SiteHelper
{
    public static $success_status = 200;
    public static $error_status = 500;
    public static $bad_request_status = 400;
    public static $unauthorized_status = 401;

    public static function settings()
    {
        $Settings = array();
        $Settings['Email'] = ''; // Email
        $Settings['Phone'] = ''; // Phone
        $Settings['Address'] = ''; // Address
        $Settings['PrimaryColor'] = '#3f8cff';
        $Settings['SecondaryColor'] = '#1059BB';
        $Settings['Currency'] = 'KZT';
        $Settings['Currency_Icon'] = '₸';
        $Settings['PageTitle'] = 'Pos App';
        $Settings['Limit'] = 6;
        $Settings['Pagination'] = 15;
        $Settings['GoogleAPIKey'] = base64_encode('AIzaSyBYy47owS11_JcssLpUjdALlzeECHFeDOY');
        return $Settings;
    }

    public static function generateOTP()
    {
        $otp_code = rand(100000, 999999);
        return $otp_code;
    }

    public static function getResponse($status, $message)
    {
        $data = array(
            'status' => $status == 200 ? true : false,
            'message' => $message,
        );
        return response()->json($data, $status);
    }

    public static function getDataResponse($status, $data, $message)
    {
        $data = array(
            'status' => true,
            'message' => $message,
            'data' => $data,
        );
        return response()->json($data, $status);
    }

    public static function getUserObject($user)
    {
        $userArr = array();
        $userArr['id'] = $user->id;
        $userArr['name'] = $user->name ? $user->name : '';
        $userArr['email'] = $user->email ? $user->email : '';
        $userArr['phone'] = $user->phone ? $user->phone : '';
        $userArr['profile_image'] = $user->profile_image ? $user->profile_image : '';
        $userArr['description'] = $user->description ? $user->description : '';
        $userArr['role'] = $user->role;
        $userArr['status'] = $user->status == 1 ? 'Active' : 'Banned';
        $userArr['created_at'] = Carbon::parse($user->created_at)->format('d-m-Y h:m a');
        return $userArr;
    }

    public static function getUserDataResponse($status, $data)
    {
        return response()->json($data, $status);
    }

    public static function getAddressLatLong($address)
    {
        $latitude = '';
        $longitude = '';

        if ($address != '') {
            $url = "https://maps.google.com/maps/api/geocode/json?address=" . urlencode($address) . '&key=' . base64_decode(SiteHelper::settings()['GoogleAPIKey']);
            $responseJson = file_get_contents($url);
            $response = json_decode($responseJson);
            if ($response->status == 'OK') {
                $latitude = $response->results[0]->geometry->location->lat;
                $longitude = $response->results[0]->geometry->location->lng;
            }
        }

        return ['latitude' => $latitude, 'longitude' => $longitude];
    }

    public static function makeBoxes($stock, $pieces)
    {
        $details = (int) $stock / (int) $pieces;
        $roundedDetails = floor($details);
        $partialPiece = $details - $roundedDetails;
        $remaining = $partialPiece;
        if (is_float($partialPiece)) {
            $remaining = round($partialPiece * $pieces);
        }

        return "{$roundedDetails} ({$remaining})";
    }

    // public static function pushNotification($device_token, $title, $body)
    // {
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => '{
    //             "to": ' . $device_token . ',
    //             "notification": {
    //               "title": ' . $title . ',
    //               "body": ' . $body . ' 
    //             },
    //             "data": {
    //                 "title": ' . $title . ',
    //                 "body": ' . $body . ' 
    //             }
    //         }',
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json',
    //             'Authorization: key=c-v59LHAQb-l7RTplR8ioE:APA91bGRFy8086hxetQ4I8Wg-mLOH5omUgzbF9RXQWchIB0R0d4YdY_TIrBPJNozApCcq6toPSiMUdl3XT8pWN3gCw-5_497KMvNf2imlWopP6br7yQJfyM5EitvYUeb5yX_5UQSvGXb'
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);

    //     return true;
    // }
}
