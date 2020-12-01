<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['(:any)'] = 'home/Home/index';


#================================================================================#
/**
 * Admin - 管理员
 */
#================================================================================#


/*
 * Admin
 */
$route['admin/login']               = 'account/Account_admin/login';
$route['admin/logout']              = 'account/Account_admin/logout';


#================================================================================#
/**
 * User
 */
#================================================================================#

//
$route['version/data']                        = 'common/Common/versionData';
$route['customerservice/info']                        = 'common/Common/customerService';

/**
 * User
 */

$route['guest/mobile/quicklogin']              = 'account/Account_guest/mobileQuickLogin';
$route['guest/otp/send']                       = 'account/Account_guest/otpSend';
$route['guest/otp/verify']                     = 'account/Account_guest/otpVerify';
//账号登录
$route['user/login']                           = 'account/Account_user/login';
//微信登录
$route['user/wxlogin']                         = 'account/Account_user/wxlogin';
$route['user/logout']                          = 'account/Account_user/logout';
$route['user/password/forgot']                 = 'account/Account_user/forgotPasswordOtpSend';
$route['user/password/forgotverify']           = 'account/Account_user/forgotPasswordOtpVerify';
$route['user/password/change']                 = 'account/Account_user/changePassword';
$route['user/info']                            = 'account/Account_user/userInfo';
$route['user/master']                          = 'account/Account_user/masterInfo';

