<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;
use Auth;
use Hash;
use DB;
use App\Models\User;
use App\Models\Code;
use App\Models\Menu;
use Jenssegers\Agent\Agent;
use Response;

class MobileController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $agent = new Agent();
        $Menu = DB::table('menus')->limit(12)->get();
        return view('mobile.home', compact('Menu'));
    }

    public function getMenu(Request $request)
    {
        $results = Menu::orderBy('id')->paginate(3);
        $Menu = '';
        if ($request->ajax()) {
            foreach ($results as $result) {
                $Menu.=
                '
                <span class="col-6 pr-2" href="detail1#html">
                    <div class="bg-white box_rounded overflow-hidden mb-3 shadow-sm">
                    <img width="100%" src="{{url('/')}}/uploads/menu/$result->thumbnail" class="img-fluid">
                    <div class="p-2">
                        <p class="text-dark mb-1 fw-bold">$result->title</p>
                        <p class="small mb-2"><i class="mdi mdi-star text-warning"></i> <span class="font-weight-bold text-dark ml-1 fw-bold">4.8</span> <span class="text-muted"> <span class="mdi mdi-circle-medium"></span> African <span class="mdi mdi-circle-medium"></span> kes $result->price </span> <span class="bg-light d-inline-block font-weight-bold text-muted rounded-3 py-1 px-2">25-30 min</span></span></p>
                        <p class="small mb-0 text-muted ml-auto"><a class="text-danger" href="{{url('/')}}/mobile/shopping-cart/add-to-cart/$result->id">Add to Basket <i class="mdi mdi-cart text-danger"></i></a></p>
                    </div>
                    </div>
                </span>
                ';
            }
            return $Menu;
        }
        return view('mobile.home');
    }


    public function add_to_cart($id)
    {
        $Product = Menu::find($id);
        \Cart::add([
            'id' => $Product->id,
            'name' => $Product->title,
            'price' => $Product->price,
            'quantity' => 1,
            'attributes' => array(
            'image' => $Product->thumbnail,
            )
        ]);
        $data = $Product->title." has been added to basket";
        return Response::json($data);
    }


    public function orders(){
        return view('mobile.orders');
    }

    public function orders_details(){
        return view('mobile.orders-details');
    }

    public function transactions(){
        return view('mobile.transactions');
    }

    public function edit_profile(){
        return view('mobile.edit-profile');
    }

    public function profile(){
        return view('mobile.profile');
    }

    public function offers(){
        return view('mobile.offers');
    }

    public function veryfy_number(){
        return view('mobile.veryfy-number');
    }

    public function verification_code(){
        return view('mobile.verification-code');
    }

    public function search(){
        return view('mobile.search');
    }

    public function shopping_cart(){
        $cartItems = \Cart::getContent();

        return view('mobile.shopping-cart', compact('cartItems'));
    }

    public function checkout(){
        $cartItems = \Cart::getContent();
        return view('mobile.checkout', compact('cartItems'));
    }

    public function menu($menu){
        $Menu = DB::table('menus')->where('slung', $menu)->get();
        return view('mobile.details', compact('Menu'));
    }

    public function menus($menu){
        $Menu = DB::table('menus')->get();
        return view('mobile.menu', compact('Menu'));
    }

    public function category($menu){
        $Menu = DB::table('category')->where('slung', $menu)->get();
        foreach ($Menu as $key => $value) {
            $Products = DB::table('menus')->where('cat_id', $value->id)->get();
        }
        return view('mobile.menu', compact('Menu','Products'));
    }

    public function location(Request $request)
    {
        // $ip = $request->ip();
        $ip = '197.156.140.165';
        $currentUserInfo = Location::get($ip);

        return view('mobile.location', compact('currentUserInfo'));
    }

    public function removeCart($id)
    {
        \Cart::remove($id);
        session()->flash('success', 'Item Cart Remove Successfully !');

        return redirect()->route('cart.list.mobile');
    }


    public function generateCode(){
        $num_str = sprintf(mt_rand(1000, 9999));
        $Codes = DB::table('codes')->where('code',$num_str)->get();
        if($Codes->isEmpty()){
            $Add = new Code;
            $Add->code = $num_str;
            $Add->user = Auth::User()->id;
            $Add->save();
            $Code = $num_str;
        }else{
            $Code = $this->generateCode();
        }
        return $Code;
    }

    public function send_verification(Request $request){
        // Generate Random Code
        $Code = $this->generateCode();

        $Message = "$Code is Your Shaq's House Verification code";
        $PhoneNumber = $request->mobile;

        $this->send($Message,$PhoneNumber);
        return response()->json([
            "message" => "Success"
        ]);


    }

    public function verify(Request $request){
        $code = $request->code;
        $Check =  DB::table('codes')->where('code',$code)->get();
        if($Check->isEmpty()){
            return response()->json([
                "message" => "Wrong Code"
            ]);
        }else{
            $updateDetails = array('status'=>1);
            DB::table('codes')->where('user', Auth::User()->id)->where('code',$code)->update($updateDetails);
            return response()->json([
                "message" => "Success"
            ]);
        }
    }

    public function send($Message,$mobile){
        $phoneNumbers = str_replace(' ', '', $mobile);
        $phoneNumber = str_replace('+', '', $phoneNumbers);
        //
        $message = $Message;
        $phone =$phoneNumber;
        $senderid = "DESIGNEKTA";
        //
        $url = 'https://bulk.cloudrebue.co.ke/api/v1/send-sms';
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvYnVsay5jbG91ZHJlYnVlLmNvLmtlXC8iLCJhdWQiOiJodHRwczpcL1wvYnVsay5jbG91ZHJlYnVlLmNvLmtlXC8iLCJpYXQiOjE2NTM5Nzc0NTEsImV4cCI6NDgwOTczNzQ1MSwiZGF0YSI6eyJlbWFpbCI6ImluZm9AZGVzaWduZWt0YS5jb20iLCJ1c2VyX2lkIjoiMTQiLCJ1c2VySWQiOiIxNCJ9fQ.N3y4QhqTApKi46YSiHmkaoEctO9z6Poc4k1g44ToyjA";

            $post_data=array(
            'sender'=>$senderid,
            'phone'=>$phone,
            'correlator'=>'Whatever',
            'link_id'=>null,
            'message'=>$message
            );

        $data_string = json_encode($post_data);
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization:Bearer '.$token,
                'Content-Length: ' . strlen($data_string)
                )
            );

        $response = curl_exec($ch);
        curl_close($ch);
    }

    public function update_profile(Request $request){
        $name = $request->name;
        $email = $request->email;
        $mobile = $request->mobile;
        $address = $request->address;

        $updateDetails = array(
            'name' =>$name,
            'email' =>$email,
            'mobile' =>$mobile,
            'location' =>$address,
        );
        DB::table('users')->where('id', Auth::User()->id)->update($updateDetails);
        return response()->json([
            "message" => "Success"
        ]);
    }

    public function edit_profile_pic(){
        $User = User::find(Auth::User()->id);
        return view('mobile.edit-profile-pic', compact('User'));
    }

    public function edit_profile_pic_post(Request $request){
        if($request->file('avatar')){
            $file= $request->file('avatar');
            $filename= date('YmdHi').$file->getClientOriginalName();
            $file-> move(public_path('uploads/users'), $filename);
            // $data['image']= $filename;
        }
        else{
            $filename = $request->fake_avatar;
        }
        $updateDetails = array(
           'image'=>$filename,
        );
        DB::table('users')->where('id', Auth::User()->id)->update($updateDetails);
        $User = User::find(Auth::User()->id);
        return view('mobile.edit-profile-pic', compact('User'));
    }

}