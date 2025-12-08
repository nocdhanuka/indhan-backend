<?php

Route::get('/', function () {
//    $login_user_id = \Session::get('id');
//    if (isset($login_user_id) || $login_user_id != "") {
//        Redirect::to('user/profile')->send();
//    } 
//    else {
    return View::make('backend.adminlogin');
//    }
});

Route::get('/circular/{path}/{preview?}', 'CircularController@getcircular');

Route::get('admin', function () {
    return View::make('backend.adminlogin');
});

Route::post('admin/admin-login', 'adminController@postAdminLogin');
Route::get('admin/campaign', 'CampController@campaignList');
Route::any('admin/campaign/createCampaign', 'CampController@createCampaign');
Route::any('admin/campaign/editCampaign/{id?}', 'CampController@createCampaign');
Route::any('admin/campaign/removeOption', 'CampController@removeOption');
Route::any('admin/campaign/removeQuestion', 'CampController@removeQuestion');
Route::post('admin/campaign/campaignOption', 'CampController@campaignOption');
Route::get('admin/campaign/editCampaignStatus/id={id}/status={status_id}', 'CampController@editCampaignStatus');
Route::post('admin/campaign/updateCampaignStatus', 'CampController@updateCampaignStatus');
Route::post('admin/campaign/sendCampNotification', 'CampController@sendCampNotification');

Route::post('admin/update-gen-status', 'CampController@updateStatus');

Route::post('admin/campaign/updateTextProceedTo', 'CampController@updateTextProceedTo');
Route::get('campaign/rawData/{id}','CampController@rawData');
Route::post('admin/all-campaignlist', 'CampController@getAllCampList');

Route::any('admin/add-gen', 'GeneralController@addgeneral');
Route::any('admin/gen-list', 'GeneralController@generalUserList');
Route::any('admin/create-gen', 'GeneralController@createGenUser');
Route::post('admin/all-genlist', 'GeneralController@getAllUserList');
Route::get('admin/editgen/{id}', 'GeneralController@editGenUser');
Route::post('admin/update-gen', 'GeneralController@updateUser');
Route::post('admin/update-gen-status', 'GeneralController@updateStatus');

Route::controller('call', 'CallController');
Route::controller('territory','territoryController');
Route::controller('market','marketController');
Route::controller('distributer','distributerController');
Route::controller('distributor', 'DistributorController');
Route::controller('user', 'userController');
Route::controller('admin', 'adminController');
Route::controller('language', 'languageController');
Route::controller('district', 'districtController');
Route::controller('tehsil', 'tehsilController');
Route::controller('village','villageController');
Route::controller('area', 'areaManagerController');
Route::controller('product', 'productController');
Route::controller('crop', 'cropController');
Route::controller('category', 'categoryController');
Route::controller('recommendation', 'productCropRelationController');
Route::controller('zonal', 'zonalManagerController');
Route::controller('regional', 'regionalManagerController');
Route::controller('salesregional', 'salesRegionalManagerController');
Route::controller('area', 'areaManagerController');
Route::controller('salesarea', 'salesAreaManagerController');
Route::controller('permission', 'permissionController');
Route::controller('customer', 'customerController');
Route::controller('selfparticipation', 'selfparticipationController');
Route::controller('testimonial', 'testimonialController');
Route::controller('syllabus', 'syllabusController');
Route::controller('atp', 'atpController');
Route::controller('efficiency', 'efficiencyController');
Route::controller('cron', 'cronController');
Route::controller('print', 'excelController');
Route::controller('ddearning', 'ddearningController');
Route::controller('demo', 'DemoController');
Route::controller('supervisor', 'MSupervisorController');
Route::controller('organogram','OrganogramController');
Route::match(['get', 'post'], 'postviewfarmers', 'adminController@postViewFarmers')->name('postviewfarmers');
Route::get('add-bdo', 'adminController@getAddBdo');
Route::get('add-dd', 'adminController@getAddDd');
Route::post('ddearninglist', 'ddearningController@postDdearninglist');
Route::post('rsmvalueslist', 'ddearningController@postRsmvalueslist');
Route::post('earningvaluelist', 'ddearningController@postEarningvaluelist');
Route::post('update-rsmvalue', 'ddearningController@postUpdateRsmvalue');
Route::controller('reports','ReportsController');
Route::controller('godown','GodownController');
Route::controller('demorequest','DemorequestController');
Route::controller('teaestate','TeaestateController');
Route::controller('distributoradvisor','DistributorAdvisorController');
Route::controller('circulars','CircularsController');
Route::controller('influencer','InfluencerController');
Route::controller('sugarmill','SugarmillController');
Route::controller('institutional','InstitutionalController');

// chemial section
Route::controller('chemical', 'chemicalController');
/*Route::controller('class', 'classController');
Route::controller('formulation', 'formulationController');
Route::controller('foodcategory', 'foodcategoryController');
Route::controller('foodsubcategory', 'foodsubcategoryController');
Route::controller('compcompany', 'compcompanyController');*/
Route::match(['get', 'post'], 'postexportfarmers', 'adminController@postexportfarmers')->name('postexportfarmers');
Route::post('postaddnewfarmers', 'adminController@postaddnewfarmers')->name('postaddnewfarmers');

Route::match(['get', 'post'], 'postexportallacpvisitlist', 'adminController@postexportallacpvisitlist')->name('postexportallacpvisitlist');

Route::match(['get', 'post'], 'postexportallfarmers', 'adminController@postexportallfarmers')->name('postexportallfarmers');
Route::match(['get', 'post'], 'postexportallifcfarmers', 'adminController@postexportallifcfarmers')->name('postexportallifcfarmers');
Route::match(['get', 'post'], 'postexportdemo', 'DemoController@postexportdemo')->name('postexportdemo');
Route::match(['get', 'post'], 'postexportallinvitedfarmers', 'adminController@postexportallinvitedfarmers')->name('postexportallinvitedfarmers');
Route::match(['get', 'post'], 'postexportallinvitedfarmerlist', 'adminController@postexportallinvitedfarmerlist')->name('postexportallinvitedfarmerlist');
Route::match(['get', 'post'], 'postexportalltickets', 'adminController@postexportalltickets')->name('postexportalltickets');
Route::match(['get', 'post'], 'postexportallqueries', 'customerController@postexportallqueries')->name('postexportallqueries');
Route::match(['get', 'post'], 'postexportallparticipants', 'selfparticipationController@postexportallparticipants')->name('postexportallparticipants');
Route::match(['get', 'post'], 'postexportallparticipantsplan', 'selfparticipationController@postexportallparticipantsplan')->name('postexportallparticipantsplan');
Route::match(['get', 'post'], 'postexportalltestimonial', 'testimonialController@postexportalltestimonial')->name('postexportalltestimonial');
Route::match(['get', 'post'], 'postexportefficiency', 'efficiencyController@postexportefficiency')->name('postexportefficiency'); //Route::post('create-bdo',         'adminController@getAddBdo');
Route::match(['get', 'post'], 'postbdolist', 'customerController@postBdoList')->name('postbdolist');
Route::match(['get', 'post'], 'postupdatecomplaint', 'customerController@postUpdateComplaint')->name('postupdatecomplaint');
Route::match(['get', 'post'], 'postdeletesyllabus', 'syllabusController@postdeletesyllabus')->name('postdeletesyllabus');
Route::match(['get', 'post'], 'postdeletesyllabusbyid', 'syllabusController@postDeleteSyllabusByID')->name('postdeletesyllabusbyid');

Route::match(['get', 'post'], 'postbdoresponse', 'customerController@postBdoResponse')->name('postbdoresponse');
Route::match(['get', 'post'], 'postexportallsyllabus', 'syllabusController@postexportallsyllabus')->name('postexportallsyllabus');
Route::match(['get', 'post'], 'postexportallatp', 'atpController@postexportallatp')->name('postexportallatp');
Route::match(['get', 'post'], 'postbdosaleresponse', 'customerController@postBdoSaleResponse')->name('postbdosaleresponse');
Route::match(['get', 'post'], 'postqueryclosed', 'customerController@postQueryClosed')->name('postqueryclosed');

//version 1
Route::group(['middleware' => 'cors', 'prefix' => '/v1'], function () {
    Route::any('/getCampaign/ctype_id={id}', 'CampController@getCampaign');
    Route::any('/getSubMenu', 'CampController@getSubMenu');
    Route::any('/register', 'WebServiceController@register');
    Route::post('/login', 'WebServiceController@login');
    Route::get('/logout/{api_token}', 'WebServiceController@logout');
    Route::post('/forgot', 'WebServiceController@forgot');
    Route::get('/menu/{api_token}', 'WebServiceController@appmenu');
    Route::get('/umenu/{api_token}/{devide_id?}', 'WebServiceController@updatedappmenu');    
    Route::get('/planSyllabus/{api_token}', 'WebServiceController@s1PlanActivity');
    Route::post('/syllabusHistory', 'WebServiceController@s1createPlanActivity');
    Route::get('/bdoPlans/{api_token}', 'WebServiceController@s1bdoPlanList');
    Route::get('/bdoPlanDetail/{api_token}/{bdo_id}', 'WebServiceController@s1bdoPlanListDetail');
    Route::get('/newTicketOpen/{api_token}', 'WebServiceController@s1OpenTicket');
    Route::post('/state', 'WebServiceController@getstate');
    Route::post('/district', 'WebServiceController@getdistrict');
    Route::post('/tehsil', 'WebServiceController@gettehsil');
    Route::post('/newTicketSubmit', 'WebServiceController@s1createTicket');
    Route::get('/ticketHistory/{api_token}/{offset?}', 'WebServiceController@s1tickethistory');
    Route::post('/ticketEdit', 'WebServiceController@s1editticket');
    Route::post('/ticketUpdate', 'WebServiceController@s1updateticket');
    Route::post('/ticketUpdateImage', 'WebServiceController@s1updateticketimage');
    Route::post('/ticketUpdateImageTest', 'WebServiceController@s1updateticketimagetest');
    Route::post('/newS1updateticketimage', 'WebApiControllers@newS1updateticketimage');
    Route::post('/ticketView', 'WebServiceController@s1ticketdetail');
    Route::post('/ticketClose', 'WebServiceController@s1closeticket');
    Route::get('/farmerRegistrationOpen/{api_token}', 'WebServiceController@s1openregisterfarmer');
    Route::post('/farmerRegistrationSubmit', 'WebServiceController@s1setregisterfarmer');
    Route::post('/farmerRegistrationSubmitTest', 'WebServiceController@s1setregisterfarmertest');
    Route::post('/testimonialSubmit', 'WebServiceController@s1testimonialSubmit');
    Route::get('/selfParticipationOpen/{api_token}/{manager_id?}/{manager_level?}', 'WebServiceController@s1selfParticipationOpen');
    Route::get('/selfParticipationOpenNew/{api_token}/{manager_id?}/{manager_level?}', 'WebServiceController@s1selfParticipationOpenNew');
    Route::get('/selfParticipationDetail/{api_token}/{ticket_id}', 'WebServiceController@s1selfParticipationDetail');
    Route::post('/selfParticipationPlan', 'WebServiceController@s1selfParticipationPlan');
    Route::post('/selfParticipationReason', 'WebServiceController@s1selfParticipationReason');
    Route::get('/selfParticipationPrevious/{api_token}', 'WebServiceController@s1selfParticipationPrevious');
    Route::get('/selfParticipationPreviousNew/{api_token}', 'WebServiceController@s1selfParticipationPreviousNew');

    Route::post('/selfParticipationSubmit', 'WebServiceController@s1selfParticipationSubmit');
    Route::get('/selfParticipationHistory/{api_token}', 'WebServiceController@s1selfParticipationHistory');
    Route::get('/productList/{api_token}', 'WebServiceController@s1reportProductList');
    Route::post('/report', 'WebServiceController@s1reports');
    Route::post('/efficiency', 'WebServiceController@s1efficiency');
    Route::post('/allefficiency', 'WebServiceController@s1allefficiency');  
    Route::get('/customerCareList/{api_token}', 'WebServiceController@s1customerCare');
    Route::post('/customerCareAssign', 'WebServiceController@s1customerCareAssign');
    Route::get('/mySyllabus/{api_token}/{id?}', 'WebServiceController@bdoMySyllabus');
    Route::get('/s1mySyllabus/{api_token}/{id?}', 'WebServiceController@S1MySyllabus');
    Route::get('/planAtpOpen/{api_token}', 'WebServiceController@planATP');
    Route::post('/planAtpSubmit', 'WebServiceController@createATP');
    Route::get('/activateTickets/{api_token}', 'WebServiceController@activateTicket');
    Route::get('/activatePrevTickets/{api_token}', 'WebServiceController@activateTicketPrevious');
    Route::post('/activateTicketReason', 'WebServiceController@activateTicketReason');
    Route::post('/activateTicketDetail', 'WebServiceController@activateTicketDetail');
    Route::post('/activateTicketSubmit', 'WebServiceController@activateTicketSubmit');
    Route::get('/customerQueries/{api_token}', 'WebServiceController@customerCareList');
    Route::post('/customerQuerySubmit', 'WebServiceController@customerCareQuerySubmit');
    Route::post('/querySolutions', 'WebServiceController@customerCareSolution');
    Route::get('/filterList/{api_token}', 'WebServiceController@s2FilterList');
    Route::get('/productCrop/{api_token}', 'WebServiceController@productCropRelation');
    Route::get('/genpass/{api_token?}', 'WebServiceController@passwordmaker');
    Route::get('/testjson/{api_token?}', 'WebServiceController@testjson');
    Route::get('/notification/{api_token?}', 'WebServiceController@notification');
    Route::get('/croncloseticket', 'WebServiceController@croncloseticket');
    Route::get('/crondemorequests', 'WebServiceController@crondemorequest');
    Route::post('/cronddearning', 'WebServiceController@cronddearning');
    Route::post('/ddprofilesummary', 'WebServiceController@ddprofilesummary');

    //My Villages
    Route::post('/villageList', 'WebServiceController@villageList');
    Route::post('/addFarmer', 'WebServiceController@addFarmer');
    Route::post('/getFarmerList', 'WebServiceController@getFarmerList');
    Route::post('/newGetFarmerList', 'WebServiceController@newGetFarmerList');
    Route::post('/allVillageList', 'WebApiControllers@allVillageList');
    Route::post('/ddVillageList', 'WebApiControllers@ddVillageList');
    Route::post('/bdoVillageList', 'WebServiceController@bdoVillageList');

    //My leads
    Route::post('/updateCallStatus', 'WebServiceController@UpdatecallStatus');
    Route::post('/UpdateOpenleadStatus', 'WebServiceController@UpdateOpenleadStatus');
    Route::post('/UpdateCloseleadStatus', 'WebServiceController@UpdateCloseleadStatus');
    Route::post('/MyLeads', 'WebServiceController@MyLeads');
    Route::post('/FarmerProducts', 'WebApiControllers@FarmerProducts');
    Route::post('/Updateplanreason', 'WebApiControllers@Updateplanreason');

    //Demo Request
    Route::post('/createDemoRequest', 'WebServiceController@createDemoRequest');
    Route::post('/DemoRequestList', 'WebServiceController@DemoRequestList');
    Route::post('/BdoDemoRequestList', 'WebApiControllers@BdoDemoRequestList');
    Route::post('/demoRequestListV1', 'WebApiControllers@demoRequestListV1');
    Route::post('/DemoFeedback', 'WebApiControllers@DemoFeedback');
    Route::post('/BdoLeadDetail', 'WebApiControllers@BdoLeadDetail');

    //Testimonials
    Route::post('/TestimonialList', 'WebServiceController@TestimonialList');
    Route::post('/supervisorTestimonialList', 'WebServiceController@supervisorTestimonialList');

    //Mark Visit
    Route::post('/markVisit', 'WebServiceController@markVisit');
    Route::post('/getMarkVisit', 'WebServiceController@getMarkVisit');

    //cron data
    //
    Route::post('/testimonialRating', 'WebServiceController@testimonialRating');

    //ATP
    Route::post('/atpList','WebServiceController@DdAtpList1');
    //Route::post('/atpList','WebApiController@DdAtpList');
    Route::post('/chooseDemoNo','WebApiControllers@ChooseDemoNo');
    Route::post('/inviteFarmer','WebApiControllers@InviteFarmer');
    Route::post('/registerFarmer','WebApiControllers@RegisterFarmer');
    Route::post('/uploadAtpImage','WebApiControllers@UploadAtpImage');
    Route::post('/closeAtp','WebApiControllers@CloseAtp');
    Route::post('/activateAtp','WebApiControllers@ActivateAtp');

    Route::post('/getValidateFarmerList','WebApiControllers@getValidateFarmerList');
    Route::post('/invitedFarmerList','WebApiControllers@invitedFarmerList');
    Route::post('/ddStateList','WebApiControllers@ddStateList');

    Route::post('/AtpFeedback','WebApiControllers@AtpFeedback');
    Route::post('/NewAtpFeedback','WebApiControllers@NewAtpFeedback');

    //distributer & retailer
    Route::post('/distributorList', 'WebApiControllers@distributorList');
    Route::post('/retailerList', 'WebApiControllers@retailerList');

    Route::post('/cropProductList', 'WebApiControllers@cropProductList');

    Route::post('BdoLeads', 'WebApiControllers@BdoLeads');
    Route::post('FarmerActivityList', 'WebApiControllers@FarmerActivityList');

    //BDO Route
    Route::get('/ddList/{api_token}', 'BDOWebServiceController@ddList');
    Route::get('/BDOddList/{api_token}', 'BDOWebServiceController@BDOddList');
    Route::post('/editDD', 'BDOWebServiceController@editDD');
    Route::post('/teamSummary', 'BDOWebServiceController@teamSummary');
    Route::post('/ddBDOprofilesummary', 'BDOWebServiceController@ddBDOprofilesummary');
    Route::post('/allStateList', 'BDOWebServiceController@allStateList');
    Route::post('/stateDistrictList', 'BDOWebServiceController@stateDistrictList');
    Route::post('/districtTehshilList', 'BDOWebServiceController@districtTehshilList');
    Route::post('/tehshilVillageList', 'BDOWebServiceController@tehshilVillageList');
    Route::post('/newAllStateList', 'BDOWebServiceController@newAllStateList');
    Route::post('/newAllDistrictList', 'BDOWebServiceController@newAllDistrictList');
    Route::post('/newAllTehshilList', 'BDOWebServiceController@newAllTehshilList');
    Route::post('/newAllVillageList', 'BDOWebServiceController@newAllVillageList');
    Route::post('/allSeasonList', 'BDOWebServiceController@allSeasonList');
    Route::post('/upcomingFilter', 'BDOWebServiceController@upcomingFilter');
    Route::post('/BDOTestimonialList', 'BDOWebServiceController@BDOTestimonialList');
    Route::post('/getBDOFarmerList', 'BDOWebServiceController@getBDOFarmerList');
    Route::post('/updateDeviceInfo', 'BDOWebServiceController@updateDeviceInfo');
    Route::post('/flList', 'BDOWebServiceController@flList');

    Route::post('/addDdSeason', 'BDOWebServiceController@addDdSeason');
    Route::post('/assignDdvillage', 'BDOWebServiceController@assignDdvillage');

    // Route::get('/bdoreport/{api_token}', 'BDOWebServiceController@bdoreport');

    //Activity
    Route::post('/BdoActivityList', 'WebApiControllers@BdoActivityList');

    //my atp 
    Route::post('/addMyAtp', 'WebApiControllers@addMyAtp');
    Route::post('/MyAtp', 'WebApiControllers@MyAtp');
    Route::post('/SupervisorActivatedTickets', 'WebApiControllers@SupervisorActivatedTickets');
    Route::post('/SupervisorTicketHistory','WebServiceController@SupervisorTicketHistory');

    //department list
    Route::post('/departmentlist', 'WebApiControllers@departmentlist');

    Route::post('/supervisorDD', 'WebApiControllers@supervisorDD');

    //Highlight
    Route::post('/addHighlight', 'WebApiControllers@addHighlight');
    Route::post('/highlightList', 'WebApiControllers@highlightList');

    Route::get('/dataSourceList/{api_token}', 'WebApiControllers@dataSourceList');

    Route::post('/supervisorReport', 'WebApiControllers@supervisorReport');

    Route::post('/villageDetail', 'WebApiControllers@villageDetail');

    Route::post('/allLocations', 'WebApiControllers@allLocations');
    Route::post('/acpMap', 'WebApiControllers@acpMap');

    // Distributer Section APIs
    Route::post('/distdistrict', 'WebServiceController@getdistdistrict');
    Route::post('/distterritory', 'WebServiceController@getdistterritories');
    Route::post('/distmarket', 'WebServiceController@getdistmarket');
    Route::post('/listdistributer', 'WebServiceController@getdistributerlist');
    Route::post('/adddistmarket', 'WebServiceController@adddistmarket');
    Route::post('/adddistributer', 'WebServiceController@adddistributer');
    Route::post('/updatedistributer', 'WebServiceController@updatedistributer');
    Route::post('/distributorgraphapi', 'WebServiceController@graphapiDistributer');

    // Chemical Section APIs
    Route::get('/listclass', 'WebServiceController@getlistclass');
    Route::post('/listformulation', 'WebServiceController@getlistformulation');
    Route::get('/listfoodcat', 'WebServiceController@getlistfoodcat');
    Route::post('/listfoodsubcat', 'WebServiceController@getlistfoodsubcat');
    Route::get('/listcompany', 'WebServiceController@getlistcompany');
    Route::post('/listdcdiscrict', 'WebServiceController@getlistdcdiscrict');
    Route::post('/listdccrop', 'WebServiceController@getlistdccrop');
    Route::post('/listdccompany', 'WebServiceController@getlistdccompany');

    Route::post('/adddiscrictdata', 'WebServiceController@adddiscrictdata');
    Route::post('/updatediscrictdata', 'WebServiceController@updatediscrictdata');
    Route::post('/discrictdatadetail', 'WebServiceController@discrictdatadetail');

    Route::post('/addcropdata', 'WebServiceController@addcropdata');
    Route::post('/updatecropdata', 'WebServiceController@updatecropdata');
    Route::post('/cropdatadetail', 'WebServiceController@cropdatadetail');

    Route::post('/addcompanydata', 'WebServiceController@addcompanydata');
    Route::post('/updatecompanydata', 'WebServiceController@updatecompanydata');
    Route::post('/companydatadetail', 'WebServiceController@companydatadetail');

    Route::post('/chemicalgraphapi', 'WebServiceController@graphapiChemical');
});

//version 2
Route::group(['middleware' => 'cors', 'prefix' => '/v2'], function () {
    Route::any('/getSubMenu/menu_id={id}', 'CampController@getSubMenu');
    Route::any('/getCampaign', 'CampController@getCampaign');
    Route::any('/getCampaignChatList/c_id={id}', 'CampController@getCampaignChatList');
    Route::post('/postResponse', 'CampController@postResponse');
    Route::any('/getMandCampaign', 'CampController@getMandCampaign');
    Route::any('/getCampAns','CampController@getCampAns');

    Route::any('/register', 'WebServicesController@register');
    Route::post('/login', 'WebServicesController@login');
    Route::get('/logout/{api_token}', 'WebServicesController@logout');
    Route::post('/forgot', 'WebServicesController@forgot');
    Route::get('/menu/{api_token}', 'WebServicesController@appmenu');
    Route::get('/umenu/{api_token}/{devide_id?}', 'WebServicesController@updatedappmenu');    
    Route::get('/planSyllabus/{api_token}', 'WebServicesController@s1PlanActivity');  // Deprecated
    Route::post('/syllabusHistory', 'WebServicesController@s1createPlanActivity');
    Route::get('/bdoPlans/{api_token}', 'WebServicesController@s1bdoPlanList');
    Route::get('/bdoPlanDetail/{api_token}/{bdo_id}', 'WebServicesController@s1bdoPlanListDetail');
    Route::get('/newTicketOpen/{api_token}', 'WebServicesController@s1OpenTicket');
    Route::post('/state', 'WebServicesController@getstate');
    Route::post('/district', 'WebServicesController@getdistrict');
    Route::post('/tehsil', 'WebServicesController@gettehsil');
    Route::post('/newTicketSubmit', 'WebServicesController@s1createTicket');
    Route::get('/ticketHistory/{api_token}/{offset?}', 'WebServicesController@s1tickethistory');
    Route::post('/ticketEdit', 'WebServicesController@s1editticket');
    Route::post('/ticketUpdate', 'WebServicesController@s1updateticket');
    Route::post('/ticketUpdateImage', 'WebServicesController@s1updateticketimage');
    Route::post('/ticketUpdateImageTest', 'WebServicesController@s1updateticketimagetest');
    Route::post('/newS1updateticketimage', 'WebApisControllers@newS1updateticketimage');
    Route::post('/ticketView', 'WebServicesController@s1ticketdetail');
    Route::post('/ticketClose', 'WebServicesController@s1closeticket');
    Route::get('/farmerRegistrationOpen/{api_token}', 'WebServicesController@s1openregisterfarmer');
    Route::post('/farmerRegistrationSubmit', 'WebServicesController@s1setregisterfarmer');
    Route::post('/farmerRegistrationSubmit_Test', 'WebServicesController@s1setregisterfarmer_test');
    Route::post('/farmerRegistrationSubmitTest', 'WebServicesController@s1setregisterfarmertest');
    Route::post('/testimonialSubmit', 'WebServicesController@s1testimonialSubmit');
    Route::get('/selfParticipationOpen/{api_token}/{manager_id?}/{manager_level?}', 'WebServicesController@s1selfParticipationOpen');
    Route::get('/selfParticipationOpenNew/{api_token}/{manager_id?}/{manager_level?}', 'WebServicesController@s1selfParticipationOpenNew');
    Route::get('/selfParticipationDetail/{api_token}/{ticket_id}', 'WebServicesController@s1selfParticipationDetail');
    Route::post('/selfParticipationPlan', 'WebServicesController@s1selfParticipationPlan');
    Route::post('/selfParticipationReason', 'WebServicesController@s1selfParticipationReason');
    Route::get('/selfParticipationPrevious/{api_token}', 'WebServicesController@s1selfParticipationPrevious');
    Route::get('/selfParticipationPreviousNew/{api_token}', 'WebServicesController@s1selfParticipationPreviousNew');

    Route::post('/selfParticipationSubmit', 'WebServicesController@s1selfParticipationSubmit');
    Route::get('/selfParticipationHistory/{api_token}', 'WebServicesController@s1selfParticipationHistory');
    Route::get('/productList/{api_token}', 'WebServicesController@s1reportProductList');
    Route::post('/report', 'WebServicesController@s1reports');
    Route::post('/efficiency', 'WebServicesController@s1efficiency');
    Route::post('/allefficiency', 'WebServicesController@s1allefficiency');  
    Route::get('/customerCareList/{api_token}', 'WebServicesController@s1customerCare');
    Route::post('/customerCareAssign', 'WebServicesController@s1customerCareAssign');
    Route::get('/mySyllabus/{api_token}/{id?}', 'WebServicesController@bdoMySyllabus');
    Route::get('/s1mySyllabus/{api_token}/{id?}', 'WebServicesController@S1MySyllabus');
    Route::get('/planAtpOpen/{api_token}', 'WebServicesController@planATP');
    Route::post('/planAtpSubmit', 'WebServicesController@createATP');
    Route::get('/activateTickets/{api_token}', 'WebServicesController@activateTicket');
    Route::get('/activatePrevTickets/{api_token}', 'WebServicesController@activateTicketPrevious');
    Route::post('/activateTicketReason', 'WebServicesController@activateTicketReason');
    Route::post('/activateTicketDetail', 'WebServicesController@activateTicketDetail');
    Route::post('/activateTicketSubmit', 'WebServicesController@activateTicketSubmit');
    Route::get('/customerQueries/{api_token}', 'WebServicesController@customerCareList');
    Route::post('/customerQuerySubmit', 'WebServicesController@customerCareQuerySubmit');
    Route::post('/querySolutions', 'WebServicesController@customerCareSolution');
    Route::get('/filterList/{api_token}', 'WebServicesController@s2FilterList');
    Route::get('/productCrop/{api_token}', 'WebServicesController@productCropRelation');
    Route::get('/genpass/{api_token?}', 'WebServicesController@passwordmaker');
    Route::get('/testjson/{api_token?}', 'WebServicesController@testjson');
    Route::get('/notification/{api_token?}', 'WebServicesController@notification');
    Route::get('/croncloseticket', 'WebServicesController@croncloseticket');
    Route::get('/crondemorequests', 'WebServicesController@crondemorequest');
    Route::post('/cronddearning', 'WebServicesController@cronddearning');
    Route::post('/ddprofilesummary', 'WebServicesController@ddprofilesummary');

    //My Villages
    Route::post('/villageList', 'WebServicesController@villageList');   // deprecated
    Route::post('/addFarmer', 'WebServicesController@addFarmer');
    Route::post('/getFarmerList', 'WebServicesController@getFarmerList');
    Route::post('/newGetFarmerList', 'WebServicesController@newGetFarmerList');
    Route::post('/allVillageList', 'WebApisControllers@allVillageList');
    Route::post('/ddVillageList', 'WebApisControllers@ddVillageList');
    Route::post('/bdoVillageList', 'WebServicesController@bdoVillageList');

    //My leads
    Route::post('/updateCallStatus', 'WebServicesController@UpdatecallStatus');
    Route::post('/UpdateOpenleadStatus', 'WebServicesController@UpdateOpenleadStatus');
    Route::post('/UpdateCloseleadStatus', 'WebServicesController@UpdateCloseleadStatus');
    Route::post('/MyLeads', 'WebServicesController@MyLeads');
    Route::post('/FarmerProducts', 'WebApisControllers@FarmerProducts');
    Route::post('/Updateplanreason', 'WebApisControllers@Updateplanreason');

    //Demo Request
    Route::post('/createDemoRequest', 'WebServicesController@createDemoRequest');
    Route::post('/DemoRequestList', 'WebServicesController@DemoRequestList');
    Route::post('/BdoDemoRequestList', 'WebApisControllers@BdoDemoRequestList');
    Route::post('/demoRequestListV1', 'WebApisControllers@demoRequestListV1');
    Route::post('/DemoFeedback', 'WebApisControllers@DemoFeedback');
    Route::post('/BdoLeadDetail', 'WebApisControllers@BdoLeadDetail');

    Route::post('/SelfParticipationImages', 'WebApisControllers@SelfParticipationImages');

    //Testimonials
    Route::post('/TestimonialList', 'WebServicesController@TestimonialList');
    Route::post('/supervisorTestimonialList', 'WebServicesController@supervisorTestimonialList');

    //Mark Visit
    Route::post('/markVisit', 'WebServicesController@markVisit');
    Route::post('/getMarkVisit', 'WebServicesController@getMarkVisit');

    //cron data
    //
    Route::post('/testimonialRating', 'WebServicesController@testimonialRating');

    //ATP
    Route::post('/atpList','WebServicesController@DdAtpList1');
    //Route::post('/atpList','WebApiController@DdAtpList');
    Route::post('/chooseDemoNo','WebApisControllers@ChooseDemoNo');
    Route::post('/inviteFarmer','WebApisControllers@InviteFarmer');
    Route::post('/registerFarmer','WebApisControllers@RegisterFarmer');
    Route::post('/uploadAtpImage','WebApisControllers@UploadAtpImage');
    Route::post('/closeAtp','WebApisControllers@CloseAtp');
    Route::post('/activateAtp','WebApisControllers@ActivateAtp');

    Route::post('/getValidateFarmerList','WebApisControllers@getValidateFarmerList');
    Route::post('/invitedFarmerList','WebApisControllers@invitedFarmerList');
    Route::post('/ddStateList','WebApisControllers@ddStateList');

    Route::post('/AtpFeedback','WebApisControllers@AtpFeedback');
    Route::post('/NewAtpFeedback','WebApisControllers@NewAtpFeedback');

    //distributer & retailer
    Route::post('/distributorList', 'WebApisControllers@distributorList');
    Route::post('/retailerList', 'WebApisControllers@retailerList');

    Route::post('/cropProductList', 'WebApisControllers@cropProductList');

    Route::post('BdoLeads', 'WebApisControllers@BdoLeads');
    Route::post('FarmerActivityList', 'WebApisControllers@FarmerActivityList');

    //BDO Route
    Route::get('/ddList/{api_token}', 'BDOWebServicesController@ddList');
    Route::get('/BDOddList/{api_token}', 'BDOWebServicesController@BDOddList');
    Route::post('/editDD', 'BDOWebServicesController@editDD');
    Route::post('/teamSummary', 'BDOWebServicesController@teamSummary');
    Route::post('/ddBDOprofilesummary', 'BDOWebServicesController@ddBDOprofilesummary');
    Route::post('/allStateList', 'BDOWebServicesController@allStateList');
    Route::post('/stateDistrictList', 'BDOWebServicesController@stateDistrictList');
    Route::post('/districtTehshilList', 'BDOWebServicesController@districtTehshilList');
    Route::post('/tehshilVillageList', 'BDOWebServicesController@tehshilVillageList');
    Route::post('/newAllStateList', 'BDOWebServicesController@newAllStateList');        // Deprecated
    Route::post('/newAllDistrictList', 'BDOWebServicesController@newAllDistrictList');  // Deprecated
    Route::post('/newAllTehshilList', 'BDOWebServicesController@newAllTehshilList');    // Deprecated
    Route::post('/newAllVillageList', 'BDOWebServicesController@newAllVillageList');    // Deprecated
    Route::post('/allSeasonList', 'BDOWebServicesController@allSeasonList');
    Route::post('/upcomingFilter', 'BDOWebServicesController@upcomingFilter');
    Route::post('/BDOTestimonialList', 'BDOWebServicesController@BDOTestimonialList');
    Route::post('/getBDOFarmerList', 'BDOWebServicesController@getBDOFarmerList');
    Route::post('/updateDeviceInfo', 'BDOWebServicesController@updateDeviceInfo');
    Route::post('/flList', 'BDOWebServicesController@flList');

    Route::post('/addDdSeason', 'BDOWebServicesController@addDdSeason');
    Route::post('/assignDdvillage', 'BDOWebServicesController@assignDdvillage');

    // Route::get('/bdoreport/{api_token}', 'BDOWebServiceController@bdoreport');

    //Activity
    Route::post('/BdoActivityList', 'WebApisControllers@BdoActivityList');

    //my atp 
    Route::post('/addMyAtp', 'WebApisControllers@addMyAtp');
    Route::post('/MyAtp', 'WebApisControllers@MyAtp');
    Route::post('/SupervisorActivatedTickets', 'WebApisControllers@SupervisorActivatedTickets');
    Route::post('/SupervisorTicketHistory','WebServicesController@SupervisorTicketHistory');

    //department list
    Route::post('/departmentlist', 'WebApisControllers@departmentlist');
    Route::post('/supervisorDD', 'WebApisControllers@supervisorDD');

    //Highlight
    Route::post('/addHighlight', 'WebApisControllers@addHighlight');
    Route::post('/highlightList', 'WebApisControllers@highlightList');
    Route::get('/dataSourceList/{api_token}', 'WebApisControllers@dataSourceList');
    Route::post('/supervisorReport', 'WebApisControllers@supervisorReport');
    Route::post('/villageDetail', 'WebApisControllers@villageDetail');
    Route::post('/allLocations', 'WebApisControllers@allLocations');
    Route::post('/acpMap', 'WebApisControllers@acpMap');

    // Phase 4 APIs
    Route::post('/presneter-monitor', 'WebServicesController@getPresneterMonitor');
    
    Route::post('/uploadaws-image', 'WebApisControllers@uploadawsImage');

});

Route::group(['middleware' => 'cors', 'prefix' => '/v3'], function () {
    Route::post('/newAllVillageList', 'WebServicesControllers@newAllVillageList');
    Route::post('/newAllStateList', 'WebServicesControllers@newAllStateList');
    Route::post('/newAllDistrictList', 'WebServicesControllers@newAllDistrictList');
    Route::post('/newAllTehshilList', 'WebServicesControllers@newAllTehshilList');
    Route::get('/planSyllabus/{api_token}/{updated_date?}', 'WebServicesControllers@s1PlanActivity');
    Route::get('/ticketHistory/{api_token}/{updated_date}/{offset?}', 'WebServicesControllers@s1tickethistory');
    Route::post('/villageList', 'WebServicesControllers@villageList');
    Route::post('/addIfcFarmer', 'WebServicesControllers@addIfcFarmer');
    Route::post('/newTicketSubmit', 'WebServicesControllers@s1createTicket');
    Route::get('/ifcfarmerRegistrationOpen/{api_token}', 'WebServicesControllers@s1openregisterifcfarmer');
    Route::get('/farmerRegistrationOpen/{api_token}', 'WebServicesControllers@s1openregisterfarmer');
    Route::post('/updateifcactivity', 'WebServicesControllers@updateIfcActivity');
    Route::post('/delifcfarmer', 'WebServicesControllers@delteifcfarmer');
    Route::post('/getValidateifcFarmerList','WebServicesControllers@getValidateifcFarmerList');
    Route::post('/planAtpSubmit', 'WebServicesControllers@createATP');
    Route::post('/getFarmerList', 'WebServicesControllers@getFarmerList');
    Route::post('/selfParticipationSubmit', 'WebServicesControllers@s1selfParticipationSubmit');
    Route::post('/selfParticipationDepartureSubmit', 'WebServicesControllers@s1selfParticipationDepartureSubmit');
    Route::get('/selfParticipationHistory/{api_token}', 'WebServicesControllers@s1selfParticipationHistory');
    Route::post('/ticketUpdateImageBytktno', 'WebServicesControllers@s1updateticketimagebytktno');
    Route::post('/NewAtpFeedbackTicket','WebServicesControllers@NewAtpFeedbackTicket');
    Route::get('/getreportType','WebServicesControllers@getreportType');
    Route::post('/getreportsList','WebServicesControllers@getreportsList');
    Route::post('/selfParticipationArivalDepartureSubmit', 'WebServicesControllers@s1selfParticipationArivalDepartureSubmit');
    Route::post('/SupervisorActivatedTickets', 'WebServicesControllers@SupervisorActivatedTickets');
    Route::post('/SupervisorTicketHistory','WebServicesControllers@SupervisorTicketHistory');
    Route::get('/ifcfarmerRegistrationOpenNew/{api_token}', 'WebServicesControllers@s1openregisterifcfarmerNew');
    Route::post('/atpList','WebServicesControllers@DdAtpList1');
    // Module 10 Demo Disbursing and Utilization
    Route::get('/demosubmenu/{api_token}','WebServicesControllers@demosubmenu');
    // Route::get('/listgodown/{district_id?}','WebServicesControllers@listgodown');
    Route::get('/listgodown/{state_id}/{updated_date?}','WebServicesControllers@listgodown');
    Route::post('/requestNewMaterial','WebServicesControllers@requestNewMaterial');
    Route::post('/listrequestedMaterial','WebServicesControllers@listrequestedMaterial');
    Route::post('/approveRequestedMaterial','WebServicesControllers@approveRequestedMaterial');
    Route::post('/receivedRequestedMaterial','WebServicesControllers@receivedRequestedMaterial');
    Route::post('/queryRequestedMaterial','WebServicesControllers@queryRequestedMaterial');
    Route::post('/listQueryRequestedMaterial','WebServicesControllers@listQueryRequestedMaterial');
    Route::post('/listUtilizationSummary','WebServicesControllers@listUtilizationSummary');
    Route::post('/listasms','WebServicesControllers@listasms');
    Route::post('/listDemoUtilizations','WebServicesControllers@listDemoUtilizations');
    Route::post('/uploadimage','WebServicesControllers@uploadimage');

    // Module 7 Tea Estate
    Route::post('/createTeaEstate','WebServicesControllers@createTeaEstate');
    Route::post('/getTypeDesignationFL','WebServicesControllers@getTypeDesignationFL');
    Route::post('/getListTeaEstate','WebServicesControllers@getListTeaEstate');
    Route::post('/teaEstateListing','WebServicesControllers@teaEstateListing');
    Route::post('/teaEstateListingHeadOfc','WebServicesControllers@teaEstateListingHeadOfc');
    Route::post('/addContactCompanyBranch','WebServicesControllers@addContactCompanyBranch');
    Route::get('/getAllContactsById/{tea_estate_id}/{updated_date?}','WebServicesControllers@getAllContactsById');
    Route::post('/teaEstatePlanAtp','WebServicesControllers@teaEstatePlanAtp');
    Route::post('/teaEstateAtpListing','WebServicesControllers@teaEstateAtpListing');
    Route::post('/teaEstateArrivalDeparture','WebServicesControllers@teaEstateArrivalDeparture');
    
    Route::post('/teaEstateVisitSummary','WebServicesControllers@teaEstateVisitSummary');
    Route::post('/teaEstateSalesReport','WebServicesControllers@teaEstateSalesReport');
    Route::post('/createInstitutionalTestimonial','WebServicesControllers@createInstitutionalTestimonial');
    Route::get('/testimonialListInstitutional/{updated_date?}','WebServicesControllers@testimonialListInstitutional');
    Route::get('/getRatingTestimonialList/{updated_date?}','WebServicesControllers@getRatingTestimonialList');
    
    // Distributor connect
    Route::post('/createDistributorAdvisor','WebServicesControllers@createDistributorAdvisor');
    Route::post('/getDistributorAdvisorList','WebServicesControllers@getDistributorAdvisorList');
    Route::post('/addDistributorArrivalDeparture','WebServicesControllers@addDistributorArrivalDeparture');
    Route::post('/distributorArrivalDepartureHistory','WebServicesControllers@distributorArrivalDepartureHistory');
    Route::get('/listdistricts/{state_id}','WebServicesControllers@listdistricts');
    Route::get('/listterritories/{district_id}','WebServicesControllers@listterritories');
    Route::get('/listmarket/{territory_id}','WebServicesControllers@listmarket');

    // Weekly circular
    Route::post('/createCircular','WebServicesControllers@createCircular');
    Route::post('/getCircular','WebServicesControllers@getCircular');
    Route::post('/getAllContacts','WebServicesControllers@getAllContacts');
    Route::post('/getAllSugarmillContacts','WebServicesControllers@getAllSugarmillContacts');
    Route::post('/getAllInfluencerContacts','WebServicesControllers@getAllInfluencerContacts');

    // Module 11 Influencer
    Route::post('/createInfluencer','WebServicesControllers@createInfluencer');
    Route::post('/getInfluencerTypeDesignationFL','WebServicesControllers@getInfluencerTypeDesignationFL');
    Route::post('/getListInfluencer','WebServicesControllers@getListInfluencer');
    Route::post('/influencerListing','WebServicesControllers@influencerListing');
    Route::post('/influencerListingHeadOfc','WebServicesControllers@influencerListingHeadOfc');
    Route::post('/addInfluencerContactCompanyBranch','WebServicesControllers@addInfluencerContactCompanyBranch');
    Route::get('/getAllInfluencerContactsById/{influencer_id}/{updated_date?}','WebServicesControllers@getAllInfluencerContactsById');
    Route::post('/influencerPlanAtp','WebServicesControllers@influencerPlanAtp');
    Route::post('/influencerAtpListing','WebServicesControllers@influencerAtpListing');
    Route::post('/influencerArrivalDeparture','WebServicesControllers@influencerArrivalDeparture');
    Route::post('/influencerVisitSummary','WebServicesControllers@influencerVisitSummary');
    Route::post('/influencerSalesReport','WebServicesControllers@influencerSalesReport');

    // Module 12 Sugarmill
    Route::post('/createSugarmill','WebServicesControllers@createSugarmill');
    Route::post('/getSugarmillTypeDesignationFL','WebServicesControllers@getSugarmillTypeDesignationFL');
    Route::post('/getListSugarmill','WebServicesControllers@getListSugarmill');
    Route::post('/sugarmillListing','WebServicesControllers@sugarmillListing');
    Route::post('/sugarmillListingHeadOfc','WebServicesControllers@sugarmillListingHeadOfc');
    Route::post('/addSugarmillContactCompanyBranch','WebServicesControllers@addSugarmillContactCompanyBranch');
    Route::get('/getAllSugarmillContactsById/{influencer_id}/{updated_date?}','WebServicesControllers@getAllSugarmillContactsById');
    Route::post('/sugarmillPlanAtp','WebServicesControllers@sugarmillPlanAtp');
    Route::post('/sugarmillAtpListing','WebServicesControllers@sugarmillAtpListing');
    Route::post('/sugarmillArrivalDeparture','WebServicesControllers@sugarmillArrivalDeparture');
    
    Route::post('/sugarmillVisitSummary','WebServicesControllers@sugarmillVisitSummary');
    Route::post('/sugarmillSalesReport','WebServicesControllers@sugarmillSalesReport');

    Route::post('/getMarkVisit', 'WebServicesControllers@getMarkVisit');
    Route::post('/getAllterritories', 'WebServicesControllers@getAllterritories');

    Route::get('/menu/{api_token}', 'WebServicesControllers@appmenu');
    Route::get('/umenu/{api_token}/{devide_id?}', 'WebServicesControllers@updatedappmenu');

    // Update the Ticket Table for IFC Data
    Route::get('/updateTicketIfcData','WebServicesControllers@updateTicketIfcData');
    Route::get('/updateTicketIfcUpdateImage','WebServicesControllers@updateTicketIfcUpdateImage');
    Route::get('/updatevillagelatlongByTicket','WebServicesControllers@updatevillagelatlongByTicket');
    Route::get('/updatevillagelatlongByAddress','WebServicesControllers@updatevillagelatlongByAddress');
    Route::get('/removeduplicateassignedvillage','WebServicesControllers@removeduplicateassignedvillage');
    Route::get('/updteFarmerdate','WebServicesControllers@updteFarmerdate');
    Route::get('/updateCircularPath','WebServicesControllers@updateCircularPath');
    Route::get('/updateFarmerType','WebServicesControllers@updateFarmerType');
    Route::get('/generateTicketNo','WebServicesControllers@generateTicketNo');
    Route::post('/getMarkVisit_old', 'WebServicesControllers@getMarkVisit_old');
});

Route::group(['middleware' => 'cors', 'prefix' => '/cc'], function () {
    Route::post('/getTicketNumbers/', 'CallCenterWebServiceController@ticketNumberList');
    Route::post('/getOtherTicketNumbers/', 'CallCenterWebServiceController@otherTicketNumberList');
    Route::post('/getFarmerNumbers/', 'CallCenterWebServiceController@farmerNumberList');
    Route::post('/setValidatedFarmerNumber/', 'CallCenterWebServiceController@verificationStatus');
    Route::post('/bdolist/', 'CallCenterWebServiceController@getBdoList');
    Route::post('/customerQuery/', 'CallCenterWebServiceController@getCustomerQuery');
});

Route::controller('test', 'TestController');

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});
