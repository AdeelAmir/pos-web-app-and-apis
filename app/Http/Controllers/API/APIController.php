<?php

namespace App\Http\Controllers\API;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DamageReplaceItem;
use App\Models\Demand;
use App\Models\DemandDetail;
use App\Models\Expenditure;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PartialPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Models\Service;
use App\Models\Shop;
use App\Models\SellerTarget;
use App\Models\SellerTargetDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class APIController extends Controller
{
    // Auth - Start
    public function login(Request $request)
    {
        // Custom Validation for Email
        if ($request['email'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Email is required');
        } elseif (strlen($request['email']) > 255) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Email should be under 255 characters');
        } elseif (!(filter_var($request['email'], FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $request['email']))) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Email should be a valid email');
        }

        // Custom Validation for Password
        if ($request['password'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Password is required');
        }

        $credentials = [
            'email' => $request['email'],
            'password' => $request['password']
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], SiteHelper::$unauthorized_status);
        }

        $user = User::where('email', $request->email)->first();

        if (!in_array($user->role, ['seller', 'user'])) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], SiteHelper::$unauthorized_status);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        // update device token
        if ($request->device_token != '') {
            $user->device_token = $request['device_token'];
            $user->save();
        }

        // get User details
        $data = array(
            'status' => true,
            'message' => 'User logged in successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => SiteHelper::getUserObject($user)
        );

        return SiteHelper::getUserDataResponse(SiteHelper::$success_status, $data);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|max:255|confirmed',
            'password_confirmation' => 'required|min:8|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), SiteHelper::$bad_request_status);
        } else {
            $user = User::where('id', $request->user()->id)->first();
            $user->password = Hash::make($request->password);
            $user->updated_at = Carbon::now();
            $user->save();
            $data = array(
                'status' => true,
                'message' => 'Password updated successfully'
            );
            return response()->json($data, SiteHelper::$success_status);
        }
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email,deleted_at,NULL'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), SiteHelper::$error_status);
        } else {
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );

            return $response == Password::RESET_LINK_SENT
                ? response()->json('Password reset link sent to your email', SiteHelper::$success_status)
                : response()->json('Unable to send password reset link', SiteHelper::$error_status);
        }
    }

    public function logout()
    {
        Auth::user()->tokens()->where('id', Auth::user()->currentAccessToken()->id)->delete();
        $data = array(
            'status' => true,
            'message' => 'Logout successfully!'
        );
        return response()->json($data, SiteHelper::$success_status);
    }
    // Auth - End

    public function getUserDetails(Request $request)
    {
        $user = User::where('id', $request->user()->id)
            ->first();
        $QImages = array();
        if (!empty($user->qualifications)) {
            $qualifImages = json_decode($user->qualifications);
            foreach ($qualifImages as $value) {
                $val = asset('public/storage/user') . '/' . $value;
                $QImages[] = $val;
            }
        }
        if ($user->role == 'customer') {
            $data = array(
                'status' => true,
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone,
                'profile_image' => $user->profile_image,
                'address' => $user->address,
                'level' => !empty($user->level) ? $user->level : '',
                'role' => $user->role,
                'device_token' => $user->device_token != '' ? $user->device_token : '',
                'created_at' => $user->created_at
            );
        }
        if ($user->role == 'vendor') {
            $data = array(
                'status' => true,
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone,
                'gender' => $user->gender,
                'profile_image' => $user->profile_image,
                'category_id' => $user->category_id,
                'sub_category_id' => $user->sub_category_id,
                'address' => $user->address,
                'city' => $user->city,
                'district' => $user->district,
                'parish' => $user->parish,
                'service_description' => $user->service_description,
                'identification_type' => $user->identification_type,
                'national_id_front' => !empty($user->national_id_front) ? asset('public/storage/user') . '/' . $user->national_id_front : '',
                'national_id_back' => !empty($user->national_id_back) ? asset('public/storage/user') . '/' . $user->national_id_back : '',
                'drivers_license' => !empty($user->drivers_license) ? asset('public/storage/user') . '/' . $user->drivers_license : '',
                'passport' => !empty($user->passport) ? asset('public/storage/user') . '/' . $user->passport : '',
                'qualifications' => $QImages,
                'experience' => $user->experience != '' ? $user->experience : '',
                'months_of_experience' => $user->months_of_experience != '' ? $user->months_of_experience : '',
                'level' => $user->level != '' ? $user->level : '',
                'about' => $user->about != '' ? $user->about : '',
                'role' => $user->role,
                'device_token' => $user->device_token != '' ? $user->device_token : '',
                'created_at' => $user->created_at
            );
        }
        return response()->json($data, SiteHelper::$success_status);
    }

    public function editProfile(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        /* 1 - Profile Picture */
        $temp = explode('/', $user->profile_image);
        $oldProfileImage = end($temp);
        if ($request->profile_picture != '') {
            if ($oldProfileImage != '') {
                $path = public_path('storage/profile_image') . '/' . $oldProfileImage;
                // Unlink the old file if it exists
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            $ProfileImage = 'Profile-Picture-' . Carbon::now()->format('Ymd-His') . '.' . $request->file('profile_picture')->extension();
            $request->file('profile_picture')->storeAs('public/profile_image/', $ProfileImage);
        } else {
            $ProfileImage = $oldProfileImage;
        }

        // Add record in users table
        DB::beginTransaction();
        $Affected = null;
        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->profile_image = $ProfileImage;
        $user->address = $request->address ?? $user->address;
        if ($user->role == 'vendor') {
            $user->gender = $request->gender ?? $user->gender;
            $user->experience = $request->experience ?? $user->experience;
            $user->months_of_experience = $request->months_of_experience ?? $user->months_of_experience;
            $user->about = $request->about ?? $user->about;

            $services = Service::where('vendor_id', $user->id)->update(['address' => $user->address]);
        }
        $Affected = $user->save();

        $QImages = array();
        if (!empty($user->qualifications)) {
            $qualifImages = json_decode($user->qualifications);
            foreach ($qualifImages as $value) {
                $val = asset('public/storage/user') . '/' . $value;
                array_push($QImages, $val);
            }
        }

        if ($Affected) {
            DB::commit();
            if ($user->role == 'customer') {
                $data = array(
                    'status' => true,
                    'message' => 'Profile updated successfully',
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone_no' => $user->phone,
                    'profile_image' => $user->profile_image,
                    'address' => $user->address,
                    'level' => !empty($user->level) ? $user->level : '',
                    'role' => $user->role,
                    'device_token' => $user->device_token != '' ? $user->device_token : '',
                    'created_at' => $user->created_at
                );
            }
            if ($user->role == 'vendor') {
                $data = array(
                    'status' => true,
                    'message' => 'Profile updated successfully',
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone_no' => $user->phone,
                    'gender' => !empty($user->gender) ? $user->gender : '',
                    'profile_image' => $user->profile_image,
                    'category_id' => $user->category_id,
                    'sub_category_id' => $user->sub_category_id,
                    'address' => $user->address,
                    'city' => $user->city,
                    'district' => $user->district,
                    'parish' => $user->parish,
                    'service_description' => $user->service_description,
                    'identification_type' => $user->identification_type,
                    'national_id_front' => !empty($user->national_id_front) ? asset('public/storage/user') . '/' . $user->national_id_front : '',
                    'national_id_back' => !empty($user->national_id_back) ? asset('public/storage/user') . '/' . $user->national_id_back : '',
                    'drivers_license' => !empty($user->drivers_license) ? asset('public/storage/user') . '/' . $user->drivers_license : '',
                    'passport' => !empty($user->passport) ? asset('public/storage/user') . '/' . $user->passport : '',
                    'qualifications' => $QImages,
                    'experience' => $user->experience != '' ? $user->experience : '',
                    'months_of_experience' => $user->months_of_experience != '' ? $user->months_of_experience : '',
                    'level' => $user->level != '' ? $user->level : '',
                    'about' => $user->about != '' ? $user->about : '',
                    'role' => $user->role,
                    'device_token' => $user->device_token != '' ? $user->device_token : '',
                    'created_at' => $user->created_at
                );
            }
            return response()->json($data, SiteHelper::$success_status);
        }
    }

    public function convert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id,deleted_at,NULL',
            // 'sub_category_id' => 'required|exists:sub_categories,id,deleted_at,NULL',
            'gender' => 'required',
            'city' => 'required',
            'district' => 'required',
            'parish' => 'required',
            'service_description' => 'required',
            'identification_type' => 'required',
            'experience' => 'required',
            'months_of_experience' => 'required',
            'about' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), SiteHelper::$bad_request_status);
        }
        if ($request->identification_type == 'national_id' && empty($request->file('national_id_front')) && empty($request->file('national_id_back'))) {
            $data = array(
                'status' => false,
                'message' => 'National id is required'
            );
            return response()->json($data, SiteHelper::$bad_request_status);
        }
        if ($request->identification_type == 'national_id' && empty($request->file('national_id_front'))) {
            $data = array(
                'status' => false,
                'message' => 'National id front is required'
            );
            return response()->json($data, SiteHelper::$bad_request_status);
        }
        if ($request->identification_type == 'national_id' && empty($request->file('national_id_back'))) {
            $data = array(
                'status' => false,
                'message' => 'National id back is required'
            );
            return response()->json($data, SiteHelper::$bad_request_status);
        }
        if ($request->identification_type == 'drivers_license' && empty($request->file('drivers_license'))) {
            $data = array(
                'status' => false,
                'message' => 'Drivers license is required'
            );
            return response()->json($data, SiteHelper::$bad_request_status);
        }
        if ($request->identification_type == 'passport' && empty($request->file('passport'))) {
            $data = array(
                'status' => false,
                'message' => 'Passport is required'
            );
            return response()->json($data, SiteHelper::$bad_request_status);
        }
        // Initialization
        $Affected = null;
        $national_id_front = null;
        $national_id_back = null;
        $drivers_license = null;
        $passport = null;
        $qualifications = array();
        if (!empty($request->file('national_id_front'))) {
            $image = $request->file('national_id_front');
            $national_id_front = 'National-Id-Front' . '-' . rand(000000, 999999) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/user', $national_id_front);
        }
        if (!empty($request->file('national_id_back'))) {
            $image = $request->file('national_id_front');
            $national_id_back = 'National-Id-Back' . '-' . rand(000000, 999999) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/user', $national_id_back);
        }
        if (!empty($request->file('drivers_license'))) {
            $image = $request->file('drivers_license');
            $drivers_license = 'Drivers-License' . '-' . rand(000000, 999999) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/user', $drivers_license);
        }
        if (!empty($request->file('passport'))) {
            $image = $request->file('passport');
            $passport = 'Passport' . '-' . rand(000000, 999999) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/user', $passport);
        }
        if ($request->qualifications != "") {
            foreach ($request->qualifications as $index => $image) {
                $qualification = 'Qualification' . '-' . rand(000000, 999999) . '-' . time() . '-' . $index . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/user', $qualification);
                $qualifications[] = $qualification;
            }
        }
        $user = User::where('id', $request->user()->id)->first();
        DB::beginTransaction();
        $user->role = 'vendor';
        $user->gender = $request->gender;
        $user->category_id = $request->category_id;
        $user->sub_category_id = $request->sub_category_id;
        $user->city = $request->city;
        $user->district = $request->district;
        $user->parish = $request->parish;
        $user->service_description = $request->service_description;
        $user->identification_type = $request->identification_type;
        $user->national_id_front = $national_id_front;
        $user->national_id_back = $national_id_back;
        $user->drivers_license = $drivers_license;
        $user->passport = $passport;
        $user->qualifications = !empty($qualifications) ? json_encode($qualifications) : null;
        $user->experience = $request->experience != '' ? $request->experience : '';
        $user->months_of_experience = $request->months_of_experience != '' ? $request->months_of_experience : '';
        $user->about = $request->about != '' ? $request->about : '';
        $Affected = $user->save();

        $QImages = array();
        if (!empty($user->qualifications)) {
            $qualifImages = json_decode($user->qualifications);
            foreach ($qualifImages as $value) {
                $val = asset('public/storage/user') . '/' . $value;
                array_push($QImages, $val);
            }
        }
        if ($Affected) {
            DB::commit();
            $data = array(
                'status' => true,
                'message' => 'Customer converted to vendor successfully',
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone_no' => $user->phone,
                'gender' => $user->gender,
                'profile_image' => $user->profile_image,
                'category_id' => $user->category_id,
                'sub_category_id' => $user->sub_category_id,
                'city' => $user->city,
                'district' => $user->district,
                'parish' => $user->parish,
                'service_description' => $user->service_description,
                'identification_type' => $user->identification_type,
                'national_id_front' => !empty($user->national_id_front) ? asset('public/storage/user') . '/' . $user->national_id_front : '',
                'national_id_back' => !empty($user->national_id_back) ? asset('public/storage/user') . '/' . $user->national_id_back : '',
                'drivers_license' => !empty($user->drivers_license) ? asset('public/storage/user') . '/' . $user->drivers_license : '',
                'passport' => !empty($user->passport) ? asset('public/storage/user') . '/' . $user->passport : '',
                'qualifications' => $QImages,
                'experience' => $user->experience != '' ? $user->experience : '',
                'months_of_experience' => $user->months_of_experience != '' ? $user->months_of_experience : '',
                'level' => $user->level != '' ? $user->level : '',
                'about' => $user->about != '' ? $user->about : '',
                'role' => $user->role,
                'device_token' => $user->device_token != '' ? $user->device_token : '',
                'created_at' => $user->created_at
            );
            return response()->json($data, SiteHelper::$success_status);
        }
    }

    public function deleteAccount(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        if (!empty($user)) {
            if (!empty($user->profile_image)) {
                $temp = explode('/', $user->profile_image);
                $fileName = end($temp);
                $file = public_path('storage/profile_image') . '/' . $fileName;
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            if (!empty($user->qualifications)) {
                $Qimages = json_decode($user->qualifications);
                foreach ($Qimages as $Qimage) {
                    $file = public_path('storage/user') . '/' . $Qimage;
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
            // Delete All Services
            Service::where('vendor_id', $request->user()->id)->delete();
            // Delete User Account
            $user->delete();
            $data = array(
                'status' => true,
                'message' => 'Account deleted successfully'
            );
            return response()->json($data, SiteHelper::$success_status);
        } else {
            $data = array(
                'status' => false,
                'message' => 'Account not found'
            );
            return response()->json($data, SiteHelper::$error_status);
        }
    }

    public function getAllCities(Request $request)
    {
        $cities = City::all();
        $cities->makeHidden(['created_at', 'updated_at']);
        return SiteHelper::getDataResponse(SiteHelper::$success_status, $cities, "List of all Cities");
    }

    public function getAllProducts(Request $request)
    {
        // Custom Validation for Date
        if ($request['date'] != "") {
            try {
                Carbon::parse($request['date']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid date');
            }
        }

        if (!in_array($request['type'], ['Stock', 'Bonus'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid type');
        }

        $products = Product::with('category', 'city')
            ->join('sale_details', 'sale_details.product_id', 'products.id')
            ->join('sales', 'sales.id', 'sale_details.sale_id')
            ->where('sales.seller_id', $request->user()->id)
            ->where(function ($query) use ($request) {
                if ($request->date != '') {
                    $parsedDate = Carbon::parse($request->date)->format('Y-m-d');
                    $query->where('sales.date', $parsedDate);
                }
                if ($request['type'] == 'Stock') {
                    $query->where('sales.bonus', 0);
                } elseif ($request['type'] == 'Bonus') {
                    $query->where('sales.bonus', 1);
                }
            })
            ->select(
                'products.*',
                'sales.date',
                'sales.grand_total',
                'sale_details.quantity',
                'sale_details.sub_total',
                DB::raw(
                    'COALESCE(
                        (
                            SELECT SUM(sd.quantity)
                            FROM sales s
                            JOIN sale_details sd ON s.id = sd.sale_id
                            WHERE s.seller_id = ' . $request->user()->id . '
                            ' . ($request->date ? 'AND s.date = "' . Carbon::parse($request->date)->format('Y-m-d') . '"' : '') . '
                            AND s.bonus = ' . ($request['type'] == 'Bonus' ? 1 : 0) . '
                            AND sd.product_id = products.id
                        ), 0
                    ) - COALESCE(
                        (
                            SELECT SUM(od.quantity)
                            FROM orders o
                            JOIN order_details od ON o.id = od.order_id
                            WHERE o.seller_id = ' . $request->user()->id . '
                            ' . ($request->date ? 'AND o.date = "' . Carbon::parse($request->date)->format('Y-m-d') . '"' : '') . '
                            AND o.sale_type = "' . $request['type'] . '"
                            AND od.product_id = products.id
                        ), 0
                    ) AS remaining_product'
                )
            )
            ->get();

        $products->makeHidden(['created_at', 'updated_at', 'deleted_at']);
        foreach ($products as $product) {
            $product->retail_price = (int) $product->retail_price;
            $product->wholesale_price = (int) $product->wholesale_price;
            $product->extra_price = (int) $product->extra_price;
            $product->quantity = (int) $product->quantity;
            $product->sub_total = (int) $product->sub_total;
            $product->remaining_product = (int) $product->remaining_product;
            $product->date = Carbon::parse($product->date)->format('d-m-Y');
            $product->boxes = (!empty($product->remaining_product) && !empty($product->pieces_in_box)) ? SiteHelper::makeBoxes($product->remaining_product, $product->pieces_in_box) : 0;
            if ($product->category) {
                $product->category->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            }
            if ($product->city) {
                $product->city->makeHidden(['created_at', 'updated_at']);
            }
        }
        return SiteHelper::getDataResponse(SiteHelper::$success_status, $products, "List of all Products");
    }

    public function getAllGoods(Request $request)
    {
        $products = Product::with('category', 'city')->get();
        $products->makeHidden(['created_at', 'updated_at', 'deleted_at']);
        foreach ($products as $product) {
            if ($product->category) {
                $product->category->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            }
            if ($product->city) {
                $product->city->makeHidden(['created_at', 'updated_at']);
            }
        }
        return SiteHelper::getDataResponse(SiteHelper::$success_status, $products, "List of all Products");
    }

    public function getAllShops(Request $request)
    {
        $shops = Shop::with('city')
            ->where(function ($query) use ($request) {
                if ($request->seller_id != '') {
                    $query->where('user_id', $request->seller_id);
                }
            })
            ->get();
        $shops->makeHidden(['created_at', 'updated_at', 'deleted_at']);

        foreach ($shops as $shop) {
            $shop->micro_district = $shop->micro_district != '' ? $shop->micro_district : '';
            $shop->latitude = $shop->latitude != '' ? $shop->latitude : '';
            $shop->longitude = $shop->longitude != '' ? $shop->longitude : '';
            if (!empty($shop->city)) {
                $shop->city->makeHidden(['created_at', 'updated_at']);
            }
        }

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $shops, "List of all Shops");
    }

    public function searchShop(Request $request)
    {
        // Custom Validation for Address
        if ($request['address'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Address is required');
        }

        $coordinations = SiteHelper::getAddressLatLong($request->address);
        $latitude = $coordinations['latitude'];
        $longitude = $coordinations['longitude'];

        $radius = 100;
        $shops = Shop::with('city')
            ->select('shops.*')
            ->selectRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having("distance", "<=", $radius)
            ->orderBy("distance", "asc")
            ->get();
        $shops->makeHidden(['created_at', 'updated_at', 'deleted_at']);

        foreach ($shops as $shop) {
            $shop->micro_district = $shop->micro_district != '' ? $shop->micro_district : '';
            $shop->latitude = $shop->latitude != '' ? $shop->latitude : '';
            $shop->longitude = $shop->longitude != '' ? $shop->longitude : '';
            if (!empty($shop->city)) {
                $shop->city->makeHidden(['created_at', 'updated_at']);
            }
        }

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $shops, "List of all Shops");
    }

    public function addShop(Request $request)
    {
        // Custom Validation for Shop Name
        $shop = Shop::where('name', $request->name)->first();
        if ($request['name'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Shop name is required');
        } elseif (!empty($shop)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Shop name should be unique');
        }

        // Custom Validation for City ID
        $city = City::whereId($request->city_id)->first();
        if ($request['city_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'City id is required');
        } elseif (empty($city)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid city id');
        }

        // Custom Validation for Location
        if ($request['location'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Location is required');
        }

        // Custom Validation for Address
        if ($request['address'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Address is required');
        }

        // Custom Validation for Description
        if ($request['description'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Description is required');
        }

        $affected = null;
        DB::beginTransaction();

        $firstTwoLetters = substr($request->name, 0, 2);
        $generated_id = Str::upper($firstTwoLetters) . rand(00000, 99999);

        $shop = new Shop();
        $shop->shop_id = $generated_id;
        $shop->user_id = $request->user()->id;
        $shop->name = $request->name;
        $shop->city_id = $request->city_id;
        $shop->location = $request->location;
        $shop->address = $request->address;
        $shop->micro_district = $request->micro_district;
        $shop->latitude = $request->latitude;
        $shop->longitude = $request->longitude;
        $shop->description = $request->description;
        $shop->status = 'active';
        $affected = $shop->save();

        if ($affected) {
            DB::commit();
            return SiteHelper::getResponse(SiteHelper::$success_status, 'Shop added successfully');
        } else {
            DB::rollBack();
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'An unhandled error occurred');
        }
    }

    public function addOrder(Request $request)
    {
        // Custom Validation for Date
        if ($request['date'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Date is required');
        } else {
            try {
                Carbon::parse($request['date']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid date');
            }
        }

        // Custom Validation for Shop ID
        $shop = Shop::whereId($request['shop_id'])->first();
        if ($request['shop_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Shop id is required');
        } elseif (empty($shop)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid shop id');
        }

        // Custom Validation for Products
        if ($request['products'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Product are required');
        }

        DB::beginTransaction();
        $affected = null;

        $productIds = [];
        $productDetails = [];
        $quantityStatus = [];
        $priceStatus = [];
        $subTotalArray = [];

        $productsOrdered = json_decode($request['products'], true);

        foreach ($productsOrdered as $item) {
            $productIds[] = $item['id'];
            $productDetails[] = [
                "id" => $item['id'],
                "price" => $item['price'],
            ];

            $subTotalArray[] = $item['quantity'] * $item['price'];

            if ($item['quantity'] < 1) {
                $quantityStatus = ['status' => false, 'message' => 'One of the product quantity is less then 1'];
            }

            if ($item['price'] == '') {
                $priceStatus = ['status' => false, 'message' => 'Price is required'];
            }
        }

        $products = Product::whereIn('id', $productIds)->get();
        if (empty($products) || count($products) != count($productIds)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'One of the product id is invalid');
        }

        // Custom Validation for Price
        if (!empty($priceStatus) && $priceStatus['status'] == false) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, $priceStatus['message']);
        }

        // Custom Validation for Quantity
        if (!empty($quantityStatus) && $quantityStatus['status'] == false) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, $quantityStatus['message']);
        }

        // Custom Validation for Payment Type
        if ($request['payment_type'] == '') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Payment type is required');
        } else if (!in_array($request['payment_type'], ['Cash', 'Credit'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Payment type is invalid');
        }

        // Custom Validation for Price Type
        if ($request['price_type'] == '') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Price type is required');
        } else if (!in_array($request['price_type'], ['Retail', 'Wholesale', 'Extra'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Price type is invalid');
        }

        $priceType = '';
        if ($request['price_type'] == 'Retail') {
            $priceType = 'retail_price';
        } elseif ($request['price_type'] == 'Wholesale') {
            $priceType = 'wholesale_price';
        } elseif ($request['price_type'] == 'Extra') {
            $priceType = 'extra_price';
        }

        if (!empty($productDetails) && $priceType != 'extra_price') {
            foreach ($products as $item) {
                foreach ($productDetails as $data) {
                    if ($item->id == $data['id'] && $item[$priceType] != $data['price']) {
                        return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'One of the product prices is invalid');
                    }
                }
            }
        }

        // Custom Validation for Sale Type
        if ($request['sale_type'] == '') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Sale type is required');
        } else if (!in_array($request['sale_type'], ['Stock', 'Bonus'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Sale type is invalid');
        }

        $date = Carbon::parse($request['date'])->format('Y-m-d');
        $bonus = $request->sale_type == 'Bonus' ? 1 : 0;
        $checkSales = Sale::join('sale_details', 'sale_details.sale_id', 'sales.id')
            ->select(
                'sales.*',
                'sale_details.product_id',
                'sale_details.quantity',
                DB::raw(
                    'COALESCE(
                        (
                            SELECT SUM(sd.quantity)
                            FROM sales s
                            JOIN sale_details sd ON s.id = sd.sale_id
                            WHERE s.date = "' . $date . '"
                            AND s.seller_id = ' . $request->user()->id . '
                            AND sd.product_id = sale_details.product_id
                        ), 0
                    ) - COALESCE(
                        (
                            SELECT SUM(od.quantity)
                            FROM orders o
                            JOIN order_details od ON o.id = od.order_id
                            WHERE o.date = "' . $date . '"
                            AND o.seller_id = ' . $request->user()->id . '
                            AND od.product_id = sale_details.product_id
                        ), 0
                    ) AS remaining_product'
                )
            )
            ->whereIn('sale_details.product_id', $productIds)
            ->where('sales.seller_id', $request->user()->id)
            ->whereDate('sales.date', $date)
            ->whereDate('sales.bonus', $bonus)
            ->get();

        if (empty($checkSales) || $checkSales == '[]') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'No products found!');
        } else {
            foreach ($checkSales as $sale) {
                foreach ($productsOrdered as $productItem) {
                    if ($sale->product_id == $productItem['id'] && $sale->remaining_product < $productItem['quantity']) {
                        return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'One of the product quantity is invalid');
                    }
                }
            }
        }

        $order = new Order();
        $order->seller_id = $request->user()->id;
        $order->shop_id = $request->shop_id;
        $order->date = Carbon::parse($request->date)->format('Y-m-d');
        $order->grand_total = array_sum($subTotalArray);
        $order->price_type = $priceType;
        // $order->orignal_payment_type = $request->payment_type;
        $order->payment_type = $request->payment_type;
        $order->sale_type = $request->sale_type;
        if ($request->payment_type == 'Credit') {
            $order->loan = 1;
        } else {
            $order->status = 'Completed';
        }
        $affected = $order->save();

        if ($affected) {
            $orderDetail = [];
            foreach ($productsOrdered as $item) {
                $subArray = [];
                $subArray['order_id'] = $order->id;
                $subArray['product_id'] = $item['id'];
                $subArray['quantity'] = $item['quantity'];
                $subArray['price'] = $item['price'];
                $subArray['created_at'] = Carbon::now();
                $subArray['updated_at'] = Carbon::now();
                $orderDetail[] = $subArray;
            }
            OrderDetail::insert($orderDetail);

            DB::commit();
            return SiteHelper::getResponse(SiteHelper::$success_status, 'Order added successfully');
        }
    }

    public function orderDetails(Request $request)
    {
        $order = Order::with('shop', 'orderDetail', 'partialPaymentHistory', 'orderDetail.product')
            ->whereId($request->order_id)
            ->select(
                'orders.*',
                DB::raw(
                    '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                    )
                    AS paid_amount'
                ),
                DB::raw('(
                   orders.grand_total - COALESCE(
                        (
                        SELECT SUM(partial_payments.amount) 
                        FROM partial_payments 
                        WHERE partial_payments.order_id = orders.id
                        ), 0
                    )
                ) AS remaining_amount')
            )
            ->first();
        // Custom Validation for Order
        if ($request['order_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Order id is required');
        } elseif ($order == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid order id');
        }

        $order->makeHidden(['updated_at']);
        $order->date = Carbon::parse($order->date)->format('d-m-Y');

        if ($order->shop) {
            $order->shop->makeHidden(['updated_at']);
        }
        if ($order->orderDetail) {
            $order->orderDetail->makeHidden(['updated_at']);
        }
        if ($order->partialPaymentHistory) {
            $order->partialPaymentHistory->makeHidden(['office_sale_id', 'created_at', 'updated_at']);
            foreach ($order->partialPaymentHistory as $partialPayment) {
                $partialPayment->createdAt = Carbon::parse($partialPayment->created_at)->format('d-m-Y h:m A');
            }
        }

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $order, "Order Details");
    }

    public function getSaleReport(Request $request)
    {
        // Custom Validation for From Date
        if ($request['from'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Sale From date is required');
        } else {
            try {
                Carbon::parse($request['from']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid sale from date');
            }
        }
        // Custom Validation for To Date
        if ($request['to'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Sale To date is required');
        } else {
            try {
                Carbon::parse($request['to']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid sale to date');
            }
        }
        if (!in_array($request['payment_type'], ['Cash', 'Credit'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid Payment Type');
        }
        // Get Sale Report Data
        $saleReport = Order::with('shop')
            ->whereBetween('date', [Carbon::parse($request['from'])->format('Y-m-d'), Carbon::parse($request['to'])->format('Y-m-d')])
            ->where('seller_id', $request->user()->id)
            ->where('payment_type', $request['payment_type'])
            // ->select('orders.*')
            ->select(
                'orders.*',
                DB::raw(
                    '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                    )
                    AS paid_amount'
                ),
                DB::raw('(
                   orders.grand_total - COALESCE(
                        (
                        SELECT SUM(partial_payments.amount) 
                        FROM partial_payments 
                        WHERE partial_payments.order_id = orders.id
                        ), 0
                    )
                ) AS remaining_amount')
            )
            ->paginate(SiteHelper::settings()['Pagination']);

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $saleReport, "Seller Sale Report");
    }

    public function getSaleReportDetails(Request $request)
    {
        // Custom Validation for From Date
        if ($request['sale_report_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Sale report id is required');
        }
        // Get Sale Report Data
        $saleReport = Order::with('shop', 'orderDetail', 'partialPaymentHistory', 'orderDetail.product')
            ->where('seller_id', $request->user()->id)
            ->where('id', $request['sale_report_id'])
            ->select(
                'orders.*',
                DB::raw(
                    '(
                        SELECT 
                        COALESCE(SUM(partial_payments.amount),0)
                        FROM partial_payments
                        WHERE partial_payments.order_id = orders.id
                    )
                    AS paid_amount'
                ),
                DB::raw('(
                   orders.grand_total - COALESCE(
                        (
                        SELECT SUM(partial_payments.amount) 
                        FROM partial_payments 
                        WHERE partial_payments.order_id = orders.id
                        ), 0
                    )
                ) AS remaining_amount')
            )
            ->first();

        if (empty($saleReport)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid Sale Report Id');
        }

        if ($saleReport->partialPaymentHistory) {
            $saleReport->partialPaymentHistory->makeHidden(['office_sale_id', 'created_at', 'updated_at']);
            foreach ($saleReport->partialPaymentHistory as $partialPayment) {
                $partialPayment->createdAt = Carbon::parse($partialPayment->created_at)->format('d-m-Y h:m A');
            }
        }

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $saleReport, "Seller Sale Report Detail");
    }

    public function getOrderReport(Request $request)
    {
        // Custom Validation for From Date
        if ($request['from'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Order From date is required');
        } else {
            try {
                Carbon::parse($request['from']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid order from date');
            }
        }
        // Custom Validation for To Date
        if ($request['to'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Order To date is required');
        } else {
            try {
                Carbon::parse($request['to']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid order to date');
            }
        }

        $from = Carbon::parse($request->from)->format('Y-m-d');
        $to = Carbon::parse($request->to)->format('Y-m-d');

        // Total Today Orders & Amount
        $todayOrderReport = Order::whereBetween('orders.date', [$from, $to])
            ->where('seller_id', $request->user()->id)
            ->where('orders.status', 'Completed')
            ->select(
                DB::raw('COALESCE(COUNT(orders.id), 0) as total_orders'),
                DB::raw('COALESCE(SUM(orders.grand_total), 0) as total_orders_amount')
            )
            ->get();

        // Get Order Report Data
        $orderReport = Order::whereBetween('orders.date', [$from, $to])
            ->where('seller_id', $request->user()->id)
            ->where('orders.status', 'Completed')
            ->select(
                'orders.id as order_id',
                'orders.date as order_date',
                DB::raw('(SELECT COALESCE(SUM(order_details.quantity), 0) FROM order_details WHERE order_details.order_id = orders.id) as total_goods'),
                DB::raw('COALESCE(SUM(orders.grand_total), 0) as total_goods_price')
            )
            ->groupBy('orders.id', 'orders.date')
            ->get();

        $data = array(
            'today_order_report' => $todayOrderReport,
            'order_report' => $orderReport
        );

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $data, "Seller Order Report");
    }

    public function getLoanReport(Request $request)
    {
        // Custom Validation for From Date
        if ($request['from'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Order From date is required');
        } else {
            try {
                Carbon::parse($request['from']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid order from date');
            }
        }

        // Custom Validation for To Date
        if ($request['to'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Order To date is required');
        } else {
            try {
                Carbon::parse($request['to']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid order to date');
            }
        }

        $from = Carbon::parse($request->from)->format('Y-m-d');
        $to = Carbon::parse($request->to)->format('Y-m-d');

        // Total Today Loan & Amount
        $todayLoanReport = Order::whereBetween('orders.date', [$from, $to])
            ->where('seller_id', $request->user()->id)
            ->where('orders.status', 'Pending')
            ->select(
                DB::raw('COALESCE(COUNT(orders.id), 0) as total_loans'),
                DB::raw('COALESCE(SUM(orders.grand_total), 0) as total_loans_amount')
            )
            ->get();

        // Get Loan Report Data
        $loanReport = Order::whereBetween('orders.date', [$from, $to])
            ->where('seller_id', $request->user()->id)
            ->where('orders.status', 'Pending')
            ->select(
                'orders.id as order_id',
                'orders.date as order_date',
                DB::raw('(SELECT COALESCE(SUM(order_details.quantity), 0) FROM order_details WHERE order_details.order_id = orders.id) as total_goods'),
                DB::raw('COALESCE(SUM(orders.grand_total), 0) as total_goods_price')
            )
            ->groupBy('orders.id', 'orders.date')
            ->get();

        $data = array(
            'today_loan_report' => $todayLoanReport,
            'loan_report' => $loanReport
        );

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $data, "Seller Loan Report");
    }

    public function loanCollectPartialPayment(Request $request)
    {
        // Custom Validation for Loan ID
        $loan = Order::whereId($request['loan_id'])->where('seller_id', $request->user()->id)->first();
        if ($request['loan_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Loan id is required');
        } elseif (empty($loan)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid loan id');
        }

        // Custom Validation for Amount
        if ($request['amount'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Amount is required');
        }
        $partialPaymentAmount = PartialPayment::where('order_id', $request->loan_id)->sum('amount');
        if ($loan->grand_total <= $partialPaymentAmount) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Loan payment is already completed');
        }
        $remainingAmount = $loan->grand_total - $partialPaymentAmount;
        if ($loan->grand_total < $request->amount) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Amount exceeds the loan amount');
        }
        $affected = null;
        DB::beginTransaction();
        $parPayment = new PartialPayment();
        $parPayment->order_id = $request->loan_id;
        $parPayment->amount = $request->amount;
        $affected = $parPayment->save();
        if ($affected) {
            DB::commit();
            return SiteHelper::getResponse(SiteHelper::$success_status, 'Partial loan money collected successfully');
        } else {
            DB::rollBack();
            return SiteHelper::getResponse(SiteHelper::$error_status, 'An unhandled error exception');
        }
    }

    public function loanCollectMoney(Request $request)
    {
        // Custom Validation for Loan ID
        $loan = Order::whereId($request['loan_id'])->where('seller_id', $request->user()->id)->first();
        if ($request['loan_id'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Loan id is required');
        } elseif (empty($loan)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid loan id');
        }

        $affected = null;
        DB::beginTransaction();

        $loan->payment_type = 'Cash';
        $loan->status = 'Completed';
        $affected = $loan->save();

        if ($affected) {
            DB::commit();
            return SiteHelper::getResponse(SiteHelper::$success_status, 'Loan money collected successfully');
        }
    }

    public function getSellerTarget(Request $request)
    {
        $targets = SellerTarget::with('seller', 'targetDetails', 'targetDetails.product')
            ->where('seller_id', $request->user()->id)
            ->paginate(SiteHelper::settings()['Pagination']);


        $targets->makeHidden(['created_at', 'updated_at']);

        $targetDetails = SellerTargetDetails::get();

        foreach ($targets as $item) {
            $cal = 0;
            $total_quantity = 0;

            $startDate = Carbon::parse('01' . $item->month . $item->year)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::parse($startDate)->endOfMonth()->format('Y-m-d');

            foreach ($targetDetails as $targetData) {
                if ($targetData->target_id == $item->id) {
                    $orders = Order::join('order_details', 'order_details.order_id', 'orders.id')
                        ->whereBetween('orders.date', [$startDate, $endDate])
                        ->where('order_details.product_id', $targetData->product_id)
                        ->where('orders.seller_id', $item->seller_id)
                        ->where('orders.status', 'Completed')
                        ->sum('order_details.quantity');
                    $cal += (int) $orders;
                    $total_quantity += (int) $targetData->quantity;
                }
            }
            $percentage = ($cal * 100) / $total_quantity;
            $item->completion = round(($percentage > 100 ? 100 : $percentage), 2) . '%';
            // $item->completion = $cal . '%';

            if ($item->seller) {
                $item->seller->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            }

            if ($item->targetDetails) {
                $item->targetDetails->makeHidden(['created_at', 'updated_at']);
            }
        }

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $targets, 'List of Seller Targets');
    }

    public function getDashboard(Request $request)
    {
        // Custom Validation for Start Date
        if ($request['start_date'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Start date is required');
        } else {
            try {
                Carbon::parse($request['start_date']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid start date');
            }
        }

        // Custom Validation for End Date
        if ($request['end_date'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'End date is required');
        } else {
            try {
                Carbon::parse($request['end_date']);
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid end date');
            }
        }

        // Custom Validation for Income Chart Type
        if ($request['income_chart_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Income chart type is required');
        } elseif (!in_array($request['income_chart_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid income chart type');
        }

        // Custom Validation for Expense Chart Type
        if ($request['expense_chart_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Expense chart type is required');
        } elseif (!in_array($request['expense_chart_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid expense chart type');
        }

        // Custom Validation for Top Selling Chart Type
        if ($request['top_selling_chart_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Top Selling chart type is required');
        } elseif (!in_array($request['top_selling_chart_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid top selling chart type');
        }

        // Custom Validation for Products In Stock Chart Type
        if ($request['products_in_stock_chart_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Products in stock chart type is required');
        } elseif (!in_array($request['products_in_stock_chart_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid products in stock chart type');
        }

        // Custom Validation for Total Sellers Table Type
        if ($request['top_sellers_table_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Top sellers table type is required');
        } elseif (!in_array($request['top_sellers_table_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid top sellers table type');
        }

        // Custom Validation for Damage Replace Chart Type
        if ($request['damage_replace_chart_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Damage/Replace chart type is required');
        } elseif (!in_array($request['damage_replace_chart_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid damage/replace chart type');
        }

        // Custom Validation for Store Loan Table Type
        if ($request['store_loan_table_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Store loan table type is required');
        } elseif (!in_array($request['store_loan_table_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid store loan table type');
        }

        // Custom Validation for Store Loan Table Type
        if ($request['seller_loan_table_type'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Seller loan table type is required');
        } elseif (!in_array($request['seller_loan_table_type'], ['Daily', 'Weekly', 'Monthly'])) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid seller loan table type');
        }

        $daysArray = [];

        $year = date('Y');
        $month = date('m');
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($startDate != '' && $endDate != '') {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        }

        $totalRevenue = Sale::where('status', 'Completed')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                }
            })
            ->sum('grand_total');

        $totalProducts = Product::select(
            DB::raw(
                '(
                        COALESCE(
                            (
                                SELECT SUM(stocks.stock)
                                FROM stocks
                                WHERE stocks.product_id = products.id
                            ), 
                            0
                        ) 
                        - COALESCE(
                            (
                                SELECT SUM(sale_details.quantity)
                                FROM sale_details
                                INNER JOIN sales ON sales.id = sale_details.sale_id
                                WHERE sale_details.product_id = products.id
                                AND (sales.bonus = 0 OR (sales.bonus = 1 AND sales.status = "Completed"))
                            ), 
                            0
                        ) 
                        - COALESCE(
                            (
                                SELECT SUM(damage_replace_items.quantity)
                                FROM damage_replace_items
                                INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                WHERE damage_replace_items.product_id = products.id
                            ),
                            0
                        )
                    ) AS total_stock'
            )
        )
            ->get();

        $totalStocks = 0;
        foreach ($totalProducts as $index => $value) {
            $totalStocks += $value->total_stock;
        }

        $totalSellers = User::where('role', 'seller')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->count();

        $totalUsers = User::where('role', 'user')
            ->where(function ($query) use ($request, $startDate, $endDate) {
                if ($startDate != '' && $endDate != '') {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            })
            ->count();

        $totalShops = Shop::where(function ($query) use ($request, $startDate, $endDate) {
            if ($startDate != '' && $endDate != '') {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        })
            ->count();

        $cardsArray = [
            'total_revenue' => !empty($totalRevenue) ? $totalRevenue : 0,
            'total_stocks' => !empty($totalStocks) ? $totalStocks : 0,
            'total_sellers' => !empty($totalSellers) ? $totalSellers : 0,
            'total_users' => !empty($totalUsers) ? $totalUsers : 0,
            'total_shops' => !empty($totalShops) ? $totalShops : 0,
        ];

        $todayRevenue = Sale::where('status', 'Completed')
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->sum('grand_total');

        $weeklyRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('grand_total');

        $monthlyRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('grand_total');

        $annualRevenue = Sale::where('status', 'Completed')
            ->whereBetween('date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])
            ->sum('grand_total');

        $revenueArray = [
            'total_revenue' => !empty($todayRevenue) ? $todayRevenue : 0,
            'weekly_revenue' => !empty($weeklyRevenue) ? $weeklyRevenue : 0,
            'monthly_revenue' => !empty($monthlyRevenue) ? $monthlyRevenue : 0,
            'annual_revenue' => !empty($annualRevenue) ? $annualRevenue : 0,
        ];

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->income_chart_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $daysArray[] = $date->format('d-m-Y');
            }
        } else if ($request->income_chart_type == 'Daily') {
            $startDate->startOfDay();
            $endDate->endOfDay();
            $daysArray[] = Carbon::now()->format('d-m-Y');
        } else {
            $startDate->startOfMonth();
            $endDate->endOfMonth();

            // Get the number of days in the current month
            $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Create an array of days from 1 to the end of the month
            $daysArray = range(1, $numDays);
        }

        $incomeArray = array();

        $saleDetails = Sale::where('status', 'Completed')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('date', 'grand_total')
            ->get();

        foreach ($daysArray as $d_index => $d_value) {
            $total_sale = 0;
            if ($request->income_chart_type != 'Monthly') {
                $date_parse = Carbon::parse($d_value)->format('Y-m-d');
            } else {
                $month_date = $d_value . '-' . $month . '-' . $year;
                $date_parse = Carbon::parse($month_date)->format('Y-m-d');
            }
            foreach ($saleDetails as $index => $value) {
                $sale_date = Carbon::parse($value->date)->format('Y-m-d');
                if ($sale_date == $date_parse) {
                    $total_sale += $value->grand_total;
                }
            }
            array_push($incomeArray, $total_sale);
        }

        $incomeDetailsArray = [
            'days' => $daysArray,
            'day_wise_income' => $incomeArray
        ];

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->expense_chart_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->expense_chart_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $expenses = Expenditure::join('office_expenditure_details', 'office_expenditure_details.expenditure_id', 'expenditures.id')
            ->leftJoin('office_expenditures', 'office_expenditures.id', 'office_expenditure_details.office_expenditure_id')
            ->select(
                'expenditures.id',
                'expenditures.name',
                DB::raw('SUM(office_expenditure_details.amount) as total_amount')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('office_expenditures.expenditure_date', [$startDate, $endDate]);
            })
            ->groupBy('expenditures.id')
            ->get();

        $expenseArray = array();
        $totalExpense = 0;
        foreach ($expenses as $index => $value) {
            $subArray = [];
            $subArray['id'] = $value->id;
            $subArray['name'] = $value->name;
            $subArray['total_amount'] = (float) $value->total_amount;
            array_push($expenseArray, $subArray);

            $totalExpense += (float) $value->total_amount;
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->top_selling_chart_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->top_selling_chart_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $bestSellingProducts = Sale::join('sale_details', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', 'products.id')
            ->join('stocks', 'stocks.product_id', 'products.id')
            ->select(
                'products.id',
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as total_product_sold'),
                DB::raw('SUM(sale_details.sub_total) as total_sale'),
                DB::raw('SUM(stocks.stock) as total_stock')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sale', 'desc')
            ->limit(3)
            ->get();

        $topSellingProductsArray = array();
        foreach ($bestSellingProducts as $index => $value) {
            $subArray = [];
            $subArray['id'] = $value->id;
            $subArray['product_name'] = $value->product_name;
            $subArray['total_product_sold'] = $value->total_product_sold;
            $subArray['total_sale'] = $value->total_sale;
            $subArray['total_stock'] = $value->total_stock;
            array_push($topSellingProductsArray, $value);
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->products_in_stock_chart_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->products_in_stock_chart_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $productsInStock = Product::join('stocks', 'stocks.product_id', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.pieces_in_box',
                'stocks.box',
                DB::raw(
                    '
                    (
                        COALESCE(SUM(stocks.stock), 0)
                        - COALESCE(
                            (
                                SELECT SUM(sale_details.quantity)
                                FROM sale_details
                                INNER JOIN sales ON sales.id = sale_details.sale_id
                                WHERE sale_details.product_id = products.id
                                AND sales.bonus = 0
                                AND sales.date BETWEEN ? AND ?
                            ), 
                            0
                        ) 
                        - COALESCE(
                            (
                                SELECT SUM(damage_replace_items.quantity)
                                FROM damage_replace_items
                                INNER JOIN damage_replaces ON damage_replaces.id = damage_replace_items.damage_id
                                WHERE damage_replace_items.product_id = products.id
                                AND damage_replaces.date BETWEEN ? AND ?
                            ),
                            0
                        )
                    ) AS total_stock'
                )
            )
            ->groupBy('products.id', 'products.name')
            ->setBindings([
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->get();

        $details = 0;
        $remaining = 0;
        $totalProductsStock = 0;
        $productsInStockArray = [];
        foreach ($productsInStock as $product) {
            $details = (int) $product->total_stock / (int) $product->pieces_in_box;
            $roundedDetails = floor($details);
            $partialPiece = $details - $roundedDetails;
            $remaining = $partialPiece;
            if (is_float($partialPiece)) {
                $remaining = round($partialPiece * $product->pieces_in_box);
            }

            $totalProductsStock += $product->total_stock;
            $productsInStockArray[] = [
                'id' => $product->id,
                'product_name' => $product->name . ' (' . $roundedDetails . ') ' . $remaining,
                'total_stock' => $product->total_stock,
            ];
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->top_sellers_table_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->top_sellers_table_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $topSellers = Sale::join('users', 'sales.seller_id', 'users.id')
            ->join('sale_details', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', 'products.id')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.profile_image as user_profile_image',
                'sale_details.product_id',
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as total_product_sold'),
                DB::raw('SUM(sale_details.sub_total) as total_sale')
            )
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('sales.created_at', [$startDate, $endDate]);
            })
            ->groupBy('users.id', 'sale_details.product_id', 'products.name')
            ->orderBy('total_sale', 'desc')
            ->limit(4)
            ->get();

        $topSellersArray = array();
        $sellers = array();
        foreach ($topSellers as $index => $value) {
            if (!in_array($value->user_id, $sellers)) {
                array_push($topSellersArray, $value);
                array_push($sellers, $value->user_id);
            }
            if (count($topSellersArray) == 4) {
                break;
            }
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->damage_replace_chart_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->damage_replace_chart_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $productsReplace = DamageReplaceItem::join('damage_replaces', 'damage_replaces.id', 'damage_replace_items.damage_id')
            ->join('products', 'products.id', 'damage_replace_items.product_id')
            ->whereNull('products.deleted_at')
            ->whereNull('damage_replaces.deleted_at')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(damage_replace_items.quantity) as total_quantity')
            )
            ->whereBetween('damage_replaces.date', [$startDate, $endDate])
            ->groupBy('damage_replace_items.product_id', 'products.id', 'products.name')
            ->get();

        $totalProductsDamage = 0;
        $productsReplaceArray = [];
        foreach ($productsReplace as $product) {
            $totalProductsDamage += $product->total_quantity;
            $productsReplaceArray[] = [
                'id' => $product->id,
                'product_name' => $product->name,
                'total_quantity' => $product->total_quantity,
            ];
        }

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->store_loan_table_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->store_loan_table_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $storeGetMostCredit = Order::join('shops', 'shops.id', 'orders.shop_id')
            ->where('orders.payment_type', 'Credit')
            ->select(
                'shops.shop_id',
                'shops.name as shop_name',
                DB::raw('SUM(orders.grand_total) as total_credit')
            )
            ->groupBy('shops.id', 'shops.name')
            ->orderBy('total_credit', 'desc')
            ->limit(4)
            ->get();

        $startDate = Carbon::now();
        $endDate = Carbon::now();

        if ($request->seller_loan_table_type == 'Weekly') {
            $startDate->startOfWeek();
            $endDate->endOfWeek();
        } else if ($request->seller_loan_table_type == 'Monthly') {
            $startDate->startOfMonth();
            $endDate->endOfMonth();
        } else {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $sellerGetMostCredit = Order::join('users', 'users.id', 'orders.seller_id')
            ->where('orders.payment_type', 'Credit')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                DB::raw('SUM(orders.grand_total) as total_credit')
            )
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_credit', 'desc')
            ->limit(4)
            ->get();

        $bonus = Sale::join('sale_details', 'sales.id', 'sale_details.sale_id')
            ->join('products', 'sale_details.product_id', 'products.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(sale_details.quantity) as product_quantity'),
                DB::raw('SUM(sale_details.retail_price) as product_price')
            )
            ->where('sales.bonus', 1)
            ->groupBy('products.id')
            ->orderBy('product_quantity', 'DESC')
            ->get();

        $bonusArray = array();
        foreach ($bonus as $index => $value) {
            $subArray = [];
            $subArray['product_name'] = $value->product_name;
            $subArray['product_quantity'] = (int) $value->product_quantity;
            $subArray['product_price'] = $value->product_price;
            array_push($bonusArray, $subArray);
        }

        $data = [
            'details' => $cardsArray,
            'revenue' => $revenueArray,
            'income' => $incomeDetailsArray,
            'expense' => ['total_expense' => $totalExpense, 'details' => $expenseArray],
            'topSellingProducts' => $topSellingProductsArray,
            'productsInStockDetails' => ['products' => $productsInStockArray, 'total_products_stock' => $totalProductsStock],
            'topSellers' => $topSellersArray,
            'damageReplaceDetails' => ['products' => $productsReplaceArray, 'total_damage_products' => $totalProductsDamage],
            'storeWithMostCredit' => $storeGetMostCredit,
            'sellerWithMaximumCredit' => $sellerGetMostCredit,
            'bonus' => $bonusArray,
        ];

        return SiteHelper::getDataResponse(SiteHelper::$success_status, $data, 'Dashboard');
    }

    public function getAllDemands(Request $request)
    {
        $demands = Demand::with('demandDetails', 'demandDetails.product')->where('seller_id', $request->user()->id)->orderBy('demand_date','DESC')->get();
        $demands->makeHidden(['updated_at', 'deleted_at']);
        foreach ($demands as $demand) {
            if ($demand->demandDetails) {
                $demand->demandDetails->makeHidden(['created_at', 'updated_at']);
            }
        }
        return SiteHelper::getDataResponse(SiteHelper::$success_status, $demands, 'List of Demands');
    }

    public function addDemand(Request $request)
    {
        // Custom Validation for Date
        if ($request['date'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Date is required');
        } else {
            try {
                $parsedDate = Carbon::parse($request['date']);

                if ($parsedDate->isBefore(Carbon::today())) {
                    return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Date cannot be before today');
                }
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid date');
            }
        }

        $productIds = [];
        $quantityStatus = [];

        $productsDemand = json_decode($request['products'], true);

        foreach ($productsDemand as $item) {
            $productIds[] = $item['id'];
            if ($item['quantity'] == '') {
                $quantityStatus = ['status' => false, 'message' => 'Product quantity is required'];
            } elseif ($item['quantity'] < 1) {
                $quantityStatus = ['status' => false, 'message' => 'One of the product quantity is less then 1'];
            }
        }

        $products = Product::whereIn('id', $productIds)->get();
        if (empty($products) || count($products) != count($productIds)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'One of the product id is invalid');
        }

        // Custom Validation for Quantity
        if (!empty($quantityStatus) && $quantityStatus['status'] == false) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, $quantityStatus['message']);
        }

        DB::beginTransaction();

        $demand = new Demand();
        $demand->seller_id = $request->user()->id;
        $demand->demand_date = $parsedDate->format('Y-m-d');
        $demand->save();

        $demandDetails = [];
        foreach ($productsDemand as $demanded) {
            $subArray = [];
            $subArray['demand_id'] = $demand->id;
            $subArray['product_id'] = $demanded['id'];
            $subArray['quantity'] = $demanded['quantity'];
            $subArray['created_at'] = Carbon::now();
            $demandDetails[] = $subArray;
        }

        if (!empty($demandDetails)) {
            foreach (array_chunk($demandDetails, 50) as $chunk) {
                DemandDetail::insert($chunk);
            }
        }

        DB::commit();
        return SiteHelper::getResponse(SiteHelper::$success_status, 'Demand added successfully');
    }

    public function editDemand(Request $request)
    {
        // Custom Validation for Demand ID
        $demand = Demand::whereId($request->demand_id)->where('seller_id', $request->user()->id)->first();
        if ($request['demand_id'] != '') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Demand id is required');
        } elseif (empty($demand)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invaild demand id');
        }

        // Custom Validation for Date
        if ($request['date'] == "") {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Date is required');
        } else {
            try {
                $parsedDate = Carbon::parse($request['date']);

                if ($parsedDate->isBefore(Carbon::today())) {
                    return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Date cannot be before today');
                }
            } catch (\Exception $e) {
                return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invalid date');
            }
        }

        $productIds = [];
        $quantityStatus = [];

        $productsDemand = json_decode($request['products'], true);

        foreach ($productsDemand as $item) {
            $productIds[] = $item['id'];
            if ($item['quantity'] == '') {
                $quantityStatus = ['status' => false, 'message' => 'Product quantity is required'];
            } elseif ($item['quantity'] < 1) {
                $quantityStatus = ['status' => false, 'message' => 'One of the product quantity is less then 1'];
            }
        }

        $products = Product::whereIn('id', $productIds)->get();
        if (empty($products) || count($products) != count($productIds)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'One of the product id is invalid');
        }

        // Custom Validation for Quantity
        if (!empty($quantityStatus) && $quantityStatus['status'] == false) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, $quantityStatus['message']);
        }

        DB::beginTransaction();

        $demand->demand_date = $parsedDate->format('Y-m-d');
        $demand->save();

        $demandDetails = [];

        DemandDetail::where('demand_id', $demand->id)->delete();
        foreach ($productsDemand as $demanded) {
            $subArray = [];
            $subArray['demand_id'] = $demand->id;
            $subArray['product_id'] = $demanded['id'];
            $subArray['quantity'] = $demanded['quantity'];
            $subArray['created_at'] = Carbon::now();
            $demandDetails[] = $subArray;
        }

        if (!empty($demandDetails)) {
            foreach (array_chunk($demandDetails, 50) as $chunk) {
                DemandDetail::insert($chunk);
            }
        }

        DB::commit();
        return SiteHelper::getResponse(SiteHelper::$success_status, 'Demand edited successfully');
    }

    public function deleteDemand(Request $request)
    {
        // Custom Validation for Demand ID
        $demand = Demand::whereId($request->demand_id)->where('seller_id', $request->user()->id)->first();
        if ($request['demand_id'] == '') {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Demand id is required');
        } elseif (empty($demand)) {
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'Invaild demand id');
        }

        $affected = null;
        DB::beginTransaction();

        DemandDetail::where('demand_id', $demand->id)->delete();
        $affected = $demand->delete();

        if ($affected) {
            DB::commit();
            return SiteHelper::getResponse(SiteHelper::$success_status, 'Demand deleted successfully');
        } else {
            DB::rollBack();
            return SiteHelper::getResponse(SiteHelper::$bad_request_status, 'An unhandled error occurred');
        }
    }
}
