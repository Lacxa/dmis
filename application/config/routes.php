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
    |	https://codeigniter.com/userguide3/general/routing.html
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
    $route['translate_uri_dashes'] = FALSE;
    
    // Collection of database migration routes
    $route['MigrationIndex'] = 'Migrate/index';
    $route['createMigration'] = 'Migrate/CreateMigration';
    $route['undoMigration'] = 'Migrate/undoMigration';
    $route['resetMigration'] = 'Migrate/resetMigration';
    
    // Disease routes
    $route['disease'] = 'disease';
    $route['disease/search'] = 'disease/search';
    $route['disease/get-diseases']['post'] = 'disease/get_diseases';
    $route['disease/add-disease']['post'] = 'disease/add_disease';
    $route['disease/disease-categories'] = 'disease/disease_categories';
    $route['disease/add-disease-category']['post'] = 'disease/add_disease_category';
    $route['disease/admin'] = 'disease/admin';
    $route['disease/admin/get-diseases']['post'] = 'disease/admin_get_diseases';
    $route['disease/admin/delete-disease/(:num)']['post'] = 'disease/delete_disease/$1';
    $route['disease/admin/import-disease']['post'] = 'disease/import_disease';
    $route['disease/communicable-state/(:num)/(:num)']['post'] = 'disease/communicable_state/$1/$2';

    // Complaits routes
    $route['complaints'] = 'complaints';
    $route['complaints/search'] = 'complaints/search';
    $route['complaints/get-complaints']['post'] = 'complaints/get_complaints';
    $route['complaints/add-complaint']['post'] = 'complaints/add_complaint';
    
    // Medicine routes
    $route['medicine/search'] = 'medicine/search';
    
    // Download routes
    $route['download/investigation-file/(:any)']['get'] = 'download/investigation_file/$1';
    
    // Authentication routes
    $route['password'] = 'home/generate_password';
    $route['login'] = 'home/login';
    $route['sign-out'] = 'home/logout';
    $route['password/expired/(:num)']['get'] = 'home/password_expired/$1';
    $route['password/expired/(:num)']['post'] = 'home/password_expired_post/$1';
    
    // Reception url routes
    $route['reception'] = 'reception';
    $route['reception/my-patients'] = 'reception/my_patients';
    $route['reception/patient-registration'] = 'reception/register_patient';
    $route['reception/search-patient'] = 'reception/search_patient';
    $route['reception/get-patient-by-id'] = 'reception/get_patient_by_id';
    $route['reception/patient-preliminaries'] = 'reception/patient_preliminaries';
    $route['reception/delete-patient/(:num)']['post'] = 'reception/delete_patient/$1';
    $route['reception/delete-previous-patient/(:num)']['post'] = 'reception/delete_patient_2/$1';
    $route['reception/reports'] = 'reception/reports';
    $route['reception/modifications'] = 'reception/modifications';
    
    // admin-user url routes
    $route['admin'] = 'admin';
    $route['admin/users'] = 'admin/users';
    $route['admin/add-user']['post'] = 'admin/register_user';
    $route['admin/user-state/(:num)/(:num)']['post'] = 'admin/user_state/$1/$2';
    $route['admin/user/delete/(:num)']['post'] = 'admin/delete_user/$1';
    $route['admin/user-incharge/(:num)/(:num)']['post'] = 'admin/user_incharge/$1/$2';
    $route['admin/user-change-category/(:num)/(:num)']['post'] = 'admin/user_change_category/$1/$2';
    $route['admin/user-change-role/(:num)/(:num)']['post'] = 'admin/user_change_role/$1/$2';
    
    // Doctor url routes
    $route['doctor'] = 'doctor';
    $route['doctor/patients'] = 'doctor/patients';
    $route['doctor/patients-from-reception']['post'] = 'doctor/patients_from_reception';
    $route['doctor/count-patients-from-reception']['post'] = 'doctor/count_patients_from_reception';
    $route['doctor/get-inpatients']['post'] = 'doctor/get_inpatients';
    $route['doctor/count-inpatients']['post'] = 'doctor/count_inpatients';
    $route['doctor/patients-from-lab']['post'] = 'doctor/patients_from_lab';
    $route['doctor/count-patients-from-lab']['post'] = 'doctor/count_patients_from_lab';
    $route['doctor/patients-lab-returns']['post'] = 'doctor/patients_lab_returns';
    $route['doctor/count-patients-lab-returns']['post'] = 'doctor/count_patients_lab_returns';
    $route['doctor/patients-pharmacy-returns']['post'] = 'doctor/patients_pharmacy_returns';
    $route['doctor/count-patients-pharmacy-returns']['post'] = 'doctor/count_patients_ph_returns';
    $route['doctor/serve-initial']['post'] = 'doctor/serve_initial';
    $route['doctor/serve-from-lab']['post'] = 'doctor/serve_from_lab';
    $route['doctor/serve-lab-return']['post'] = 'doctor/serve_lab_return';
    $route['doctor/serve-pharmacy-return']['post'] = 'doctor/serve_pharmacy_return';
    $route['doctor/session-patients'] = 'doctor/session_patients';
    $route['doctor/session-patients/(:num)'] = 'doctor/session_patient/$1';
    // $route['doctor/my-session'] = 'doctor/my_session';
    $route['doctor/ajax-count-session-patients'] = 'doctor/ajax_count_session_patients';
    $route['doctor/get-full-session-info/(:num)']['get'] = 'doctor/get_full_session_info/$1';
    $route['doctor/update-sypmtoms/(:num)/(:num)'] = 'doctor/update_sypmtoms/$1/$2';
    $route['doctor/save-patient-complaint/(:num)/(:num)/(:num)'] = 'doctor/save_patient_complaint/$1/$2/$3';
    $route['doctor/save-complaint-history/(:num)/(:num)/(:num)']['post'] = 'doctor/save_complaint_history/$1/$2/$3';
    $route['doctor/delete-patient-complaint/(:num)/(:num)/(:num)'] = 'doctor/delete_patient_complaint/$1/$2/$3';
    $route['doctor/update-investigations/(:num)/(:num)/(:num)'] = 'doctor/update_investigations/$1/$2/$3';
    $route['doctor/post-update-investigations/(:num)'] = 'doctor/update_investigations_2/$1';
    $route['doctor/save-patient-disease/(:num)/(:num)/(:num)'] = 'doctor/save_patient_disease/$1/$2/$3';
    $route['doctor/delete-myclient-disease/(:num)/(:num)/(:num)'] = 'doctor/delete_myclient_disease/$1/$2/$3';
    $route['doctor/save-patient-medicine/(:num)/(:num)/(:num)'] = 'doctor/save_patient_medicine/$1/$2/$3';
    $route['doctor/save-patient-medicine-description/(:num)/(:num)/(:num)'] = 'doctor/save_patient_medicine_description/$1/$2/$3';
    $route['doctor/delete-myclient-medicine/(:num)/(:num)/(:num)'] = 'doctor/delete_myclient_medicine/$1/$2/$3';
    $route['doctor/release-patient/(:num)'] = 'doctor/release_patient/$1';
    $route['doctor/reports'] = 'doctor/reports';
    $route['doctor/search-patient'] = 'doctor/search_patient';
    $route['doctor/patient-history'] = 'doctor/patient_history';
    $route['doctor/search-medicines']['post'] = 'doctor/search_medicines';
    $route['doctor/lab-diagnostics'] = 'doctor/lab_diagnostics';
    $route['doctor/edit-investigations'] = 'doctor/edit_investigations';
    $route['doctor/get-record-investigations/(:num)'] = 'doctor/get_record_investigations/$1';
    
    // Lab url routes
    $route['lab'] = 'lab';
    $route['lab/my-patients'] = 'lab/my_patients';
    $route['lab/serve-patient/(:num)'] = 'lab/serve_patient/$1';
    $route['lab/patient-results-get/(:num)']['get'] = 'lab/patient_results_get/$1';
    $route['lab/patient-results-post/(:num)']['post'] = 'lab/patient_results_post/$1';
    $route['lab/save-results/(:num)']['post'] = 'lab/save_results/$1';
    $route['lab/release-patient/(:num)']['get'] = 'lab/release_patient/$1';
    $route['lab/return-patient/(:num)']['get'] = 'lab/return_patient/$1';
    $route['lab/reports'] = 'lab/reports';
    $route['lab/search-patient'] = 'lab/search_patient';
    $route['lab/patient-history'] = 'lab/patient_history';
    $route['lab/lab-diagnostics'] = 'lab/lab_diagnostics';
    
    // Reports routes
    $route['reports/client/(:any)']['get'] = "e_reports/client_report_get/$1";
    $route['reports/client']['post'] = "e_reports/client_report_post";
    $route['reports/performance/doctor']['post'] = "e_reports/doctor_performance";
    $route['reports/performance/reception']['post'] = "e_reports/reception_performance";
    $route['reports/performance/lab']['post'] = "e_reports/lab_performance";
    $route['reports/performance/pharmacy']['post'] = "e_reports/pharmacy_performance";
    $route['reports/monitor/(:any)'] = "e_reports/dmis_monitoring/$1";
    $route['reports/served-patients/(:any)'] = "e_reports/served_patients/$1";
    $route['reports/dashboard/count-patients/(:num)']['post'] = "e_reports/count_patients/$1";
    
    // Pharmacy routes
    $route['pharmacy'] = 'pharmacy';
    $route['pharmacy/patients'] = 'pharmacy/patients';
    $route['pharmacy/serve-patient/(:num)'] = 'pharmacy/serve_patient/$1';
    $route['pharmacy/patient-prescriptions-get/(:num)']['get'] = 'pharmacy/patient_prescriptions_get/$1';
    $route['pharmacy/return-patient/(:num)']['get'] = 'pharmacy/return_patient/$1';
    $route['pharmacy/save-prescriptions/(:num)']['post'] = 'pharmacy/save_prescriptions/$1';
    $route['pharmacy/release-patient/(:num)']['get'] = 'pharmacy/release_patient/$1';
    $route['pharmacy/stock-register'] = 'pharmacy/stock_register';
    $route['pharmacy/stock-register/create-new-stock']['post'] = 'pharmacy/create_new_stock';
    $route['pharmacy/stock-register/get-draft'] = 'pharmacy/get_draft';
    $route['pharmacy/stock-register/get-stock-paths/(:any)'] = 'pharmacy/get_stock_paths/$1';
    $route['pharmacy/stock-register/add-medicine-to-stock']['post'] = 'pharmacy/save_medicine_to_stock';
    $route['pharmacy/stock-register/post-stock']['post'] = 'pharmacy/post_stock';
    $route['pharmacy/stock-register/remove-stock']['post'] = 'pharmacy/remove_stock';
    $route['pharmacy/medicine-categories'] = 'pharmacy/medicine_categories';
    $route['pharmacy/save-medicine-categories']['post'] = 'pharmacy/save_medicine_categories';
    $route['pharmacy/medicine-formats'] = 'pharmacy/medicine_formats';
    $route['pharmacy/save-medicine-formats']['post'] = 'pharmacy/save_medicine_formats';
    $route['pharmacy/medicine-names'] = 'pharmacy/medicine_names';
    $route['pharmacy/save-medicine-names']['post'] = 'pharmacy/save_medicine_names';
    $route['pharmacy/medicine-names/get-medicines-by-cat-format/(:num)/(:num)'] = 'pharmacy/show_medicines_by_cat_format/$1/$2';
    $route['pharmacy/medicine-state/(:num)/(:any)']['post'] = 'pharmacy/medicine_state/$1/$2';
    $route['pharmacy/search-master'] = 'pharmacy/search_master';
    $route['pharmacy/reports'] = 'pharmacy/reports';
    
    // Route to override default error page
    $route['404_override'] = 'errors/error404';