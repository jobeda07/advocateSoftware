<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\OurPartner;
use App\Models\HolidayPackage;
use App\Models\TranslateData;
use App\Models\Slider;
use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\OurPartnerResource;
use App\Http\Resources\HolidayPackageResource;
use App\Http\Resources\TranslateDataResource;
use App\Http\Resources\SliderResource;
use Log;

class FrontendAction extends Controller
{
    // function setting(Request $request){
    //     $names = $request->input('names', ['site_name','email','phone','open_close_time','whats_up','address','facebook_link','youtube_link','linkedin','pintarest','instagram','header_logo','footer_logo','fav_icon']);
        
    //     $setting = Setting::whereIn('name', $names)->get();
    
    //     if($setting->isNotEmpty()) {
    //         return response()->json(['response_data' => $setting]);
    //     } else {
    //         return response()->json(['message' => 'Settings not found'], 404);
    //     }
    // }
    // function setting(Request $request) {
    //     $names = $request->input('names', [
    //         'site_name', 'email', 'phone', 'open_close_time', 'whats_up', 
    //         'address', 'facebook_link', 'youtube_link', 'linkedin', 
    //         'pintarest', 'instagram', 'header_logo', 'footer_logo', 'fav_icon'
    //     ]);
    
    //     $settings = Setting::whereIn('name', $names)->get();
    //     $appUrl = env('APP_URL');
    //     foreach ($settings as $setting) {
    //         if (in_array($setting->name, ['header_logo', 'footer_logo', 'fav_icon']) && $setting->value) {
    //             $setting->value = $appUrl . "/" . $setting->value;
    //         }
    //     }
    
    //     if ($settings->isNotEmpty()) {
    //         return response()->json(['response_data' => $settings]);
    //     } else {
    //         return response()->json(['message' => 'Settings not found'], 404);
    //     }
    // }

    function setting(Request $request) {
        $names = $request->input('names', [
            'site_name', 'email', 'phone', 'open_close_time', 'whats_up', 
            'address', 'facebook_link', 'youtube_link', 'linkedin', 
            'pintarest', 'instagram', 'header_logo', 'footer_logo', 'fav_icon'
        ]);
    
        $settings = Setting::whereIn('name', $names)->get();
        $appUrl = env('APP_URL');
    
        $responseData = [];
        
        foreach ($settings as $setting) {
            if (in_array($setting->name, ['header_logo', 'footer_logo', 'fav_icon']) && $setting->value) {
                $responseData[$setting->name] = $appUrl . "/" . $setting->value;
            } else {
                $responseData[$setting->name] = $setting->value ?? '';
            }
        }
        if (!empty($responseData)) {
            return response()->json(['response_data' => $responseData]);
        } else {
            return response()->json(['message' => 'Settings not found'], 404);
        }
    }
    
    
    function our_partner(){
        $ourPartner = OurPartner::where('status',1)->latest()->get();
        if($ourPartner->isNotEmpty()) {
            return response()->json(['response_data' => OurPartnerResource::collection($ourPartner)]);
        } else {
            return response()->json(['message' => 'our partners data not found'], 404);
        }
    }
    function holiday_package(){
        $holidayPackage = HolidayPackage::where('status',1)->latest()->take(8)->get();
        if($holidayPackage->isNotEmpty()) {
            return response()->json(['response_data' => HolidayPackageResource::collection($holidayPackage)]);
        } else {
            return response()->json(['message' => 'Holiday Package data not found'], 404);
        }
    }
    function holiday_package_details($slug) {
        $holidayPackage = HolidayPackage::where('slug', $slug)->first();
        if ($holidayPackage) {
            return response()->json(['response_data' => new HolidayPackageResource($holidayPackage)]);
        } else {
            return response()->json(['message' => 'Holiday Package data not found'], 404);
        }
    }
    public function translate_data()
    {
        if (Schema::hasTable('cache') && Schema::hasTable('translate_data')) {
            $translate_data = Cache::remember('translations', now()->addDay(), function () {
                return TranslateData::where('lang_code', 'en')->get();
            });
            if ($translate_data->isNotEmpty()) {
                return response()->json([
                    'response_data' => TranslateDataResource::collection($translate_data)
                ]);
            } else {
                return response()->json(['message' => 'Data not found'], 404);
            }
        } else {
            return response()->json(['message' => 'Necessary tables not found'], 500);
        }
    }

    public function change_language($lang_code)
    {
        $userId = auth()->id() ?? request()->ip();
        $cacheKey = "lang_code_{$userId}";
        Cache::forget($cacheKey);
        Cache::put($cacheKey, $lang_code, 86400);
        $translate_data = TranslateData::where('lang_code', $lang_code)->get();
        if ($translate_data->isNotEmpty()) {
            return response()->json([
                'response_data' => TranslateDataResource::collection($translate_data),
            ]);
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }

    function slider(){
        
        $slider =Slider::where('status',1)->latest()->first();
        if ($slider) {
            return response()->json(['response_data' =>new  SliderResource($slider)]);
        } else {
            return response()->json(['message' => 'data not found'], 404);
        }
    } 
    
    function explore_destination()
    {
        $explore_destination = [];
        for ($i = 0; $i < 5; $i++) {
            $destination = [
                'id' =>$i +1,
                'name' => "Dubai",
                'slug' => "dubai-5430",
                'image' => "https://sharetrip.net/_next/image?url=https%3A%2F%2Ftbbd-flight.s3.ap-southeast-1.amazonaws.com%2Fpromotion%2FMaafushi.jpg&w=1920&q=75",
                'total_hotel' => 24,
            ];
            $explore_destination[] = $destination;
        }
        if (!empty($explore_destination)) {
            return response()->json(['response_data' => $explore_destination]);
        } else {
            return response()->json(['message' => 'Holiday Package data not found'], 404);
        }
    }

    function hotel_list($country_id){
        $country=Country::find($country_id);
        if( $country){
            $hotel_list = [];
            for ($i = 0; $i < 9; $i++) {
                $item = [
                    'id' => $i +1,
                    'name' => "Everest Hotel",
                    'slug' => "everest-hotel-i-5430",
                    'regular_price' => "14225",
                    'discount_price' => "12703",
                    'rating' => "4.6",
                    'total_review' => 320,
                    'location' => "P.o. Box 659 New Banesware Kathmandu Nepal, Kathmandu",
                    'image' => "https://images.pexels.com/photos/338504/pexels-photo-338504.jpeg?auto=compress&cs=tinysrgb&w=600",
                ];
                $hotel_list[] = $item;
            }
            if (!empty($hotel_list)) {
                return response()->json(['response_data' => $hotel_list]);
            } else {
                return response()->json(['message' => 'Hotel data not found'], 404);
            }
        }else{
            return response()->json(['message' => 'Hotel data not found'], 404);
        }
    }
   
    function hotel_show($slug){
        
        if( $slug){
            $item = [
                'id' => 1,
                'name' => "Everest Hotel",
                'slug' => "everest-hotel-i-5430",
                'regular_price' => "14225",
                'discount_price' => "12703",
                'rating' => "4.6",
                'total_review' => 320,
                'location' => "P.o. Box 659 New Banesware Kathmandu Nepal, Kathmandu",
                'description' => "About this hotel 
                            Property Location
                            With a stay at The Everest Hotel, you'll be centrally located in Kathmandu, convenient to Banglamukhi Temple and Pashupatinath Temple. This 4-star hotel is within close proximity of Golden Temple and Patan Durbar Square.
                            Rooms
                            Make yourself at home in one of the 160 individually furnished guestrooms, featuring refrigerators and minibars. Your Select Comfort bed comes with down comforters. 21-inch LCD televisions with cable programming provide entertainment, while complimentary wireless Internet access keeps you connected. Private bathrooms with bathtubs feature rainfall showerheads and complimentary toiletries.
                            Rec, Spa, Premium Amenities
                            Head straight for the casino, or wait for that lucky feeling while you enjoy one of the other recreational opportunities, such as a health club and an outdoor pool. Additional amenities include complimentary wireless Internet access, concierge services, and babysitting/childcare.
                            Dining
                            Enjoy a meal at one of the hotel's dining establishments, which include 2 restaurants and a coffee shop/café. From your room, you can also access 24-hour room service. Thirsty? Quench your thirst at a bar/lounge, a poolside bar, or a swim-up bar.
                            Business, Other Amenities
                            Featured amenities include a 24-hour business center, a computer station, and express check-in. Event facilities at this hotel consist of a conference center, conference space, and meeting rooms. A roundtrip airport shuttle is complimentary (available 24 hours). ",
                'image' => "https://api.sharetrip.net/api/v1/hotel/image?key=TzjoxbPYzP1P80vm8j5r4YNo2n6PvKhwUfNMynl4LJnMNHvc+gFOdjzJk9d47np6QO2Uf3YPHWzdf8liv5O4OA==",
                'multi_image' =>[
                    'image-1'=>"https://api.sharetrip.net/api/v1/hotel/image?key=w7klgRIJAxc6CR8qEjeIP0FnYOhPU1kGPWbutqUhJSB7qhinlA+3AymkWPxw7yrQqTIOMJxsif76y7+FGKqPYQ==",
                    'image-2'=>"https://api.sharetrip.net/api/v1/hotel/image?key=ZCKIcKdnzB/JKZvX7X6lDp6Zz3bElV6/XANyYK2HXCIrooPv49MEsqZuEz1ATd7s7VREFtSdOgjWeoHgEZksWA==",
                    'image-3'=>"https://api.sharetrip.net/api/v1/hotel/image?key=GM6rdpetjFWOXudAtkt/5FAyVts15gvW9KocOaM/ScbgImzIGvNJzY0N66FkAohY6UyWTvwu955i1sh1NlCI6w==",
                    'image-4'=>"https://api.sharetrip.net/api/v1/hotel/image?key=GM6rdpetjFWOXudAtkt/5FAyVts15gvW9KocOaM/ScYMlz++EQod33VoFDjRPx7+fyQX6CkJ92WzzYTco8skHA==",
                ],

            ];
            return response()->json(['response_data' => $item]);
        }else{
            return response()->json(['message' => 'Hotel details not found'], 404);
        }
    }

    function featured_hotels(){
        $hotel_list = [];
        for ($i = 0; $i < 9; $i++) {
            $item = [
                'id' => $i +1,
                'name' => "Featured Hotel",
                'slug' => "featured-hotel-i-5430",
                'regular_price' => "19655",
                'discount_price' => "18703",
                'rating' => "4.9",
                'total_review' => 720,
                'location' => "Chalkpara, Mawna, Sreepur, Gazipur",
                'image' => "https://images.pexels.com/photos/1743231/pexels-photo-1743231.jpeg?auto=compress&cs=tinysrgb&w=600",
            ];
            $hotel_list[] = $item;
        }
        if (!empty($hotel_list)) {
            return response()->json(['response_data' => $hotel_list]);
        } else {
            return response()->json(['message' => 'Hotel data not found'], 404);
        }
        
    }


    public function test_post(Request $request)
    {
        $name = $request->name;
        $email = $request->email;

        return response()->json([
            'response_data' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
    }

    
}