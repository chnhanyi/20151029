<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//Manager权限映射  
$config['acl']['manager'] = array(  
    'Admin' ,
    'Group',  
    'Agent',
    'Company',
    'Route',
    'Order',
    'Table',
    'Staff'
);  
//operator权限映射    
$config['acl']['operator'] = array(
	'Admin',//登录和登出页面
	'Order',
    'Table',
	'Group'
);  

//controller权限映射  
$config['acl']['controller'] = array(  
    'Admin',
    'Control'
);  

//accountant权限映射  
$config['acl']['accountant'] = array(  
    'Admin',
    'Account'
); 

//boss权限映射  
$config['acl']['boss'] = array(  
    'Admin',
    'Boss'
);

//visitor权限映射  
$config['acl']['visitor'] = array(  
    'Admin'
);  


   
//-------------配置权限不够的提示信息及跳转url------------------//  
$config['acl_info']['manager'] = array(  
    'info' => 'You cannot access this page, this error has been recorded.',  
    'return_url' => 'Admin/index'  
);  
   
$config['acl_info']['operator'] = array(  
    'info' => 'You cannot access this page, this error has been recorded.',  
    'return_url' => 'Admin/index'  
); 
$config['acl_info']['controller'] = array(  
    'info' => 'You cannot access this page, this error has been recorded.',  
    'return_url' => 'Admin/index'  
);  
   
$config['acl_info']['accountant'] = array(  
    'info' => 'You cannot access this page, this error has been recorded.',  
    'return_url' => 'Admin/index'  
); 

$config['acl_info']['visitor'] = array(  
    'info' => 'You cannot access this page.You need to login to continue, this error has been recorded.',  
    'return_url' => 'Admin/index'  
); 
   
/* End of file acl.php */  
/* Location: ./application/config/acl.php */  