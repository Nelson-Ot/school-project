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

$route['(:any)/authentication'] = 'authentication/index/$1';
$route['(:any)/forgot'] = 'authentication/forgot/$1';
$route['(:any)/teachers'] = 'home/teachers';
$route['(:any)/events'] = 'home/events';
$route['(:any)/news'] = 'home/news/';
$route['(:any)/about'] = 'home/about';
$route['(:any)/faq'] = 'home/faq';
$route['(:any)/admission'] = 'home/admission';
$route['(:any)/gallery'] = 'home/gallery';
$route['(:any)/contact'] = 'home/contact';
$route['(:any)/admit_card'] = 'home/admit_card';
$route['(:any)/exam_results'] = 'home/exam_results';
$route['(:any)/certificates'] = 'home/certificates';
$route['(:any)/page/(:any)'] = 'home/page/$2';
$route['(:any)/gallery_view/(:any)'] = 'home/gallery_view/$2';
$route['(:any)/event_view/(:num)'] = 'home/event_view/$2';
$route['(:any)/news_view/(:any)'] = 'home/news_view/$2';

$route['dashboard'] = 'dashboard/index';
$route['branch'] = 'branch/index';
$route['attachments'] = 'attachments/index';
$route['homework'] = 'homework/index';
$route['onlineexam'] = 'onlineexam/index';
$route['hostels'] = 'hostels/index';
$route['event'] = 'event/index';
$route['accounting'] = 'accounting/index';
$route['school_settings'] = 'school_settings/index';
$route['role'] = 'role/index';
$route['sessions'] = 'sessions/index';
$route['translations'] = 'translations/index';
$route['cron_api'] = 'cron_api/index';
$route['modules'] = 'modules/index';
$route['system_student_field'] = 'system_student_field/index';
$route['custom_field'] = 'custom_field/index';
$route['backup'] = 'backup/index';
$route['advance_salary'] = 'advance_salary/index';
$route['system_update'] = 'system_update/index';
$route['certificate'] = 'certificate/index';
$route['payroll'] = 'payroll/index';
$route['leave'] = 'leave/index';
$route['award'] = 'award/index';
$route['classes'] = 'classes/index';
$route['student_promotion'] = 'student_promotion/index';
$route['live_class'] = 'live_class/index';
$route['exam'] = 'exam/index';
$route['profile'] = 'profile/index';
$route['sections'] = 'sections/index';

$route['authentication'] = 'authentication/index';
$route['home'] = 'home/index';
$route['404_override'] = 'errors';
$route['default_controller'] = 'home';

$route['(:any)'] = 'home/index/$1';
$route['translate_uri_dashes'] = FALSE;