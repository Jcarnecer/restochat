<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Site_Controller/index';
$route['messages/(:any)'] = 'Site_Controller/messages/$1';

$api_url = "api/dev";

$route["$api_url/users/(:any)"]["GET"] = "User_Controller/show/$1";
$route["$api_url/users/(:any)/conversations"]["GET"] = 'User_Controller/conversations/$1';

$route["$api_url/companies/(:any)/users"]["GET"] = 'Company_Controller/users/$1';

$route["$api_url/conversations"]["GET"] = 'Conversation_Controller/index';
$route["$api_url/conversations"]["POST"] = 'Company_Controller/create_conversation';
$route["$api_url/conversations/private-conversation"]["GET"] = "Conversation_Controller/get_private_conversation";
$route["$api_url/conversations/(:any)"]["GET"] = 'Conversation_Controller/show/$1';
$route["$api_url/conversations/(:any)/messages"]["GET"] = 'Conversation_Controller/messages/$1';
$route["$api_url/conversations/(:any)/messages"]["POST"] = 'Conversation_Controller/create_message/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
