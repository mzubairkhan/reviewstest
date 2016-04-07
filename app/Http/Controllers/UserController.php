<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Session\Middleware\StartSession;

class UserController extends Controller
{

public function login()
        {


            if(Session::has('logged_in')) {
                return redirect('admin/index');
                }else{
                return view('user.login');
            }
        }




    public function authenticate()
       {
           if(isset($_GET['token'])) {
               try
               {

                   $token_str = $_GET['token'];
                   $token_arr = explode('-',$token_str);
                   $token = $token_arr[0];
                   $user_id = $token_arr[1];
                   $is_admin = $token_arr[2];

                   $mode = 'production';
                   if (strpos($_SERVER['HTTP_HOST'], 'staging') !== false) {
                       $mode = 'staging';
                   }

                   $sess_array = array(
                       'userid' => $user_id,
                       'token' => $token,
                       'is_admin' => $is_admin,
                       'mode' => $mode,
                       'permissions' => array(),
                       'rules'=>array()
                   );



                   Session::set('logged_in', $sess_array);
                   Session::save();

                return redirect('admin/index');


               } catch(Exception $e) {

                   return view('user.login');
               }

           } else {

               return view('user.login');
           }

       }

       /*public function changeMode($mode)
       {
           $set_mode = 'staging';
           if($mode == 'staging') {
               $set_mode = 'production';
           }
           $session_data = $this->session->all_userdata();
           $logged_in = $session_data['logged_in'];
           $logged_in['mode'] = $set_mode;
           $this->session->set_userdata('logged_in', $logged_in);
           redirect('/');
       }*/

       public function logout()
       {
           $session = Session::get('logged_in');
            Session::flush();

           $roles = $session['rules'];

           $is_reseller = false;

           foreach($roles as $role)
           {
               if($role['title'] == 'Reseller') {
                   $is_reseller = true;
                   break;
               }
           }

           if($is_reseller) {
               return Redirect::to('https://reseller.purevpn.com/partner/logout.php');
           } else {
               return redirect('/');
           }
       }

}