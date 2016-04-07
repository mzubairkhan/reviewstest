<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Session\Middleware\StartSession;

class AdminController extends Controller
{

   public function __construct() {

            $user_session = Session::get('logged_in');

            if(!($user_session['is_admin'] == 1)) {
                return view('user.login');
            }
        }


   public function index()
       {
           $options = array('page'=>'dashboard','sub-page'=>'index','title'=>'Admin Dashboard');
           return view('admin.index')->with('option', $options);
       }


   public function users()
       {
           $params = $_GET;
           $options = array('page'=>'users','sub-page'=>'view','title'=>'Partners','params'=>$params);
           return view('admin.users')->with('option', $options);
    }

   public function editUser($userId)
       {
           $params = array('userId'=>$userId);
           $options = array('page'=>'users','sub-page'=>'new','title' => 'Edit Partner','params'=>$params);
           return view('admin.newUser')->with('option', $options);

       }


   public function newUser()
       {
           $options = array('page'=>'users','sub-page'=>'new','title' => 'Add a Partner');
           return view('admin.newUser')->with('option', $options);

       }

   public function pages()
        {
            $params = $_GET;
            $options= array('page'=>'pages','sub-page'=>'view','title'=>'Pages','params'=>$params);
            return view('admin.pages')->with('option', $options);
        }

   public function editPage($pageId)
        {
            $params = array('pageId'=>$pageId);
            $options = array('page'=>'pages','sub-page'=>'new','title' => 'Edit Page','params'=>$params);
            return view('admin.newPage')->with('option', $options);

        }



   public function newPage()
       {
           $params = $_GET;
           $options = array('page'=>'pages','sub-page'=>'view','title'=>'Add new page','params'=>$params);
           return view('admin.newPage')->with('option', $options);

        }


   public function providers()
       {
           $params = $_GET;
           $options = array('page'=>'providers','sub-page'=>'view','title'=>'Providers','params'=>$params);
           return view('admin.providers')->with('option', $options);

       }

   public function editProvider($providerId)
        {
            $params = array('providerId'=>$providerId);
            $options = array('page'=>'providers','sub-page'=>'new','title' => 'Edit Provider','params'=>$params);
            return view('admin.newProvider')->with('option', $options);

        }

   public function newProvider()
        {
            $params = $_GET;
            $options = array('page'=>'providers','sub-page'=>'view','title'=>'Add new provider','params'=>$params);
            return view('admin.newProvider')->with('option', $options);


        }

   public function customfields()
       {
           $params = $_GET;
           $options = array('page'=>'customfields','sub-page'=>'view','title'=>'Custom Field Groups','params'=>$params);
           return view('admin.customFields')->with('option', $options);

       }

   public function editCustomfield($cf_id)
        {
            $params = array('cf_id'=>$cf_id);
            $options = array('page'=>'customfields','sub-page'=>'edit','title' => 'Edit custom field','params'=>$params);
            return view('admin.newCustomfield')->with('option', $options);
            //print_r($params);

        }

   public function newCustomfield()
        {
            $params = $_GET;
            $options = array('page'=>'customfields','sub-page'=>'view','title'=>'Add new custom field group','params'=>$params);
            return view('admin.newCustomfield')->with('option', $options);

        }

   public function websites()
       {
           $params = $_GET;
           $options = array('page'=>'websites','sub-page'=>'view','title'=>'Websites','params'=>$params);
            return view('admin.websites')->with('option', $options);

       }

   public function newWebsite()
        {
            $params = $_GET;
            $options = array('page'=>'websites','sub-page'=>'view','title'=>'Add new','params'=>$params);
            //echo '<pre>';print_r($options);echo '</pre>';exit;
            return view('admin.newWebsite')->with('option', $options);


        }

   public function editWebsite($id)
      {

          $params = array('websiteId'=>$id);

          $options = array('page'=>'websites','sub-page'=>'new','title' => 'Edit Website','params'=>$params);
          return view('admin.newWebsite')->with('option', $options);

      }

   public function git()
        {
            $params = $_GET;
            $options = array('page'=>'git','sub-page'=>'view','title'=>'Manage Git Repos','params'=>$params);
            return view('admin.git')->with('option', $options);

        }


   public function newMedia()
        {
            $params = $_GET;
            $options = array('page'=>'newMedia','sub-page'=>'view','title'=>'Add new Media','params'=>$params);
            return view('admin.newMedia')->with('option', $options);

        }

   public function media()
        {
            $params = $_GET;
            $options = array('page'=>'media','sub-page'=>'view','title'=>'Media','params'=>$params);
            return view('admin.media')->with('option', $options);

        }


    public function roles()
       {
           $params = $_GET;
           $options = array('page'=>'roles','sub-page'=>'view' ,'title' => 'Roles' ,'params'=>$params);
           return view('admin.roles')->with('option', $options);

       }


    public function permissions()
        {
            $params = $_GET;
            $options = array('page'=>'permissions','sub-page'=>'view','title' => 'Permissions','params'=>$params);
            return view('admin.permissions')->with('option', $options);

        }

    public function modules()
        {
            $params = $_GET;
            $options = array('page'=>'modules','sub-page'=>'view','title' => 'Modules' ,'params'=>$params);
            return view('admin.modules')->with('option', $options);

        }

    public function newRole()
        {
            $params = $_GET;
            $options = array('page'=>'roles','sub-page'=>'new','title' => 'Add Role','params'=>$params);
            return view('admin.newRole')->with('option', $options);

        }

    public function newPermission()
       {
           $params = $_GET;
           $options = array('page'=>'permissions','sub-page'=>'new','title' => 'Add Permission','params'=>$params);
           return view('admin.newPermission')->with('option', $options);

       }

    public function newModule()
       {
           $params = $_GET;
           $options = array('page'=>'modules','sub-page'=>'new','title' => 'Add Module','params'=>$params);
           return view('admin.newModule')->with('option', $options);

       }


    public function editPermission($id)

        {
            $params = array('id'=>$id);
            $options = array('page'=>'permissions','sub-page'=>'edit','title' => 'Edit Permission','params'=>$params);
            return view('admin.newPermission')->with('option', $options);

        }

    public function userRoles($id)
        {
            $params = array('user_id'=>$id);
            $options = array('page'=>'users','sub-page'=>'view','title' => 'Add Roles for User','params'=>$params);
            return view('admin.userRoles')->with('option', $options);

        }

    public function rolePermissions($id)
        {
            $params = array('role_id'=>$id);
            $options = array('page'=>'roles','sub-page'=>'view','title' => 'Add Permissions for Role','params'=>$params);
            return view('admin.rolePermissions')->with('option', $options);

        }

    public function modulePermissions($id)
       {
           $params = array('module_id'=>$id);
           $options = array('page'=>'modules','sub-page'=>'view','title' => 'See Permissions for Module','params'=>$params);
           return view('admin.modulePermissions')->with('option', $options);
       }

    public function permissionRoles($id)
       {
           $params = array('permission_id'=>$id);
           $options = array('page'=>'permissions','sub-page'=>'view','title' => 'See Roles for Permission','params'=>$params);
           return view('admin.permissionRoles')->with('option', $options);
       }





}