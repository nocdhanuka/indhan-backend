package com.dhanuka.indhan.Retrofit;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;
import com.dhanuka.indhan.ATPMenu.activity.ClosedATPBean;
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse;
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse;
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.HighlightModel;
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorActivateBean;
import com.dhanuka.indhan.ActivateTicket.Model.ActiveTicketListModel;
import com.dhanuka.indhan.ActivateTicket.Model.PendingActiveTicketlistModel;
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean;
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse;
import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean;
import com.dhanuka.indhan.Activity.LoginMVP.Model.LoginBean;
import com.dhanuka.indhan.Activity.LogoutMVP.Model.LogoutBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.NewTicketBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilModel;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.IfcFarmersListResponse;
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean;
import com.dhanuka.indhan.Activity.ReportsMVP.Model.ReportsBean;
import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean;
import com.dhanuka.indhan.Activity.TicketDetailMVP.Model.TicketDetailBean;
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryAtpResponse;
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel;
import com.dhanuka.indhan.Activity.misAndReports.model.MisReportsListResponse;
import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenuResponse;
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse;
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model;
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel;
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.BDOPlanListDetailDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse;
import com.dhanuka.indhan.DataSyncServices.Model.MySyllabusModel;
import com.dhanuka.indhan.DataSyncServices.Model.PlanActivityDataModel;
import com.dhanuka.indhan.DataSyncServices.Model.PlanAtpSubmitResponseModel;
import com.dhanuka.indhan.DataSyncServices.Model.PlanRequirementDataModel;
import com.dhanuka.indhan.DataSyncServices.Model.PlanSyllabusModel;
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew;
import com.dhanuka.indhan.DemoRequestActivity.model.StateResponse;
import com.dhanuka.indhan.DemoRequestActivity.models.AllVillageListResponse;
import com.dhanuka.indhan.DemoRequestActivity.models.CreateRequestDemoResponse;
import com.dhanuka.indhan.DemoRequestActivity.models.CropProductList;
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestListResponse;
import com.dhanuka.indhan.DemoRequestActivity.models.DistrictResponseNew;
import com.dhanuka.indhan.DemoRequestActivity.models.ExistingFarmerListResponse;
import com.dhanuka.indhan.DemoRequestActivity.models.TahsilResponseNew;
import com.dhanuka.indhan.DemoRequestEvaluated.models.ASMListData;
import com.dhanuka.indhan.DemoRequestEvaluated.models.ChatListResponse;
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoMenuResponse;
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoRequestEvaluatedResponse;
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownResponse;
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponse;
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationOverview;
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationSummary;
import com.dhanuka.indhan.MarkVisits.MarkVisitBean;
import com.dhanuka.indhan.MenuScreenActivity.Model.AllLocation;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllTehsilBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllVillageBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.DemoRequestResponse;
import com.dhanuka.indhan.Model.AddCropResponse;
import com.dhanuka.indhan.Model.AddDistributorResponse;
import com.dhanuka.indhan.Model.AllDistDistrictReponse;
import com.dhanuka.indhan.Model.ArrivalDepartureResponse;
import com.dhanuka.indhan.Model.ChemicalAddCompanyResponse;
import com.dhanuka.indhan.Model.ChemicalAddDistrictWiseResponse;
import com.dhanuka.indhan.Model.ChemicalCompanyWiseDataList;
import com.dhanuka.indhan.Model.ChemicalCropWiseListResponse;
import com.dhanuka.indhan.Model.ChemicalDistrictWiseDataList;
import com.dhanuka.indhan.Model.CommonModel;
import com.dhanuka.indhan.Model.CompanyList;
import com.dhanuka.indhan.Model.CompanyListResponse;
import com.dhanuka.indhan.Model.ContactSyncModel;
import com.dhanuka.indhan.Model.DistrictResponse;
import com.dhanuka.indhan.Model.FoodCategoryResponse;
import com.dhanuka.indhan.Model.FoodSubCategoryResponse;
import com.dhanuka.indhan.Model.FormulationResponse;
import com.dhanuka.indhan.Model.ListClassResponse;
import com.dhanuka.indhan.Model.MarketResponse;
import com.dhanuka.indhan.Model.PieChartData;
import com.dhanuka.indhan.Model.PlanATPApiModel;
import com.dhanuka.indhan.Model.ProductSyllabusModel;
import com.dhanuka.indhan.Model.TerritoryResponse;
import com.dhanuka.indhan.Model.UpdateChemicalDistrictWiseData;
import com.dhanuka.indhan.Model.UpdateCompanyResponse;
import com.dhanuka.indhan.Model.UpdateDistributor;
import com.dhanuka.indhan.MyLeadMenu.activities.leaddetails.LeadDetailsBean;
import com.dhanuka.indhan.MyLeadMenu.models.CloseLeadResponse;
import com.dhanuka.indhan.MyLeadMenu.models.DistributorResponse;
import com.dhanuka.indhan.MyLeadMenu.models.OpenLeadsResponse;
import com.dhanuka.indhan.MyLeadMenu.models.RetailerResponse;
import com.dhanuka.indhan.MyVillage.MyVillageBean;
import com.dhanuka.indhan.PlanATP.Model.DataSourceListBean;
import com.dhanuka.indhan.PlanATP.Model.PresenterDataResponse;
import com.dhanuka.indhan.PlanATP.Model.VillageListTehsil;
import com.dhanuka.indhan.Profile.DDProfileBean;
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.IfcFarmerTicket;
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse;
import com.dhanuka.indhan.Reports.Model.ProductListResponse;
import com.dhanuka.indhan.Reports.Model.ReportsResponse;
import com.dhanuka.indhan.Reports.supervisor_report.SupervisorReportsBean;
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse;
import com.dhanuka.indhan.SelfParticipationActivity.Model.PreviousPlanResponse;
import com.dhanuka.indhan.SelfParticipationActivity.Model.SPDetailModel;
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse;
import com.dhanuka.indhan.SyllabusHistory.Model.SharedSyllabusHistoryModel;
import com.dhanuka.indhan.Testimonial.Model.TestimonialResponse;
import com.dhanuka.indhan.TestimonialList.TestimonialBean;
import com.dhanuka.indhan.TestimonialList.filter.DDBean;
import com.dhanuka.indhan.TestimonialList.supervisor.SupervisorTestimonialBean;
import com.dhanuka.indhan.VillageDetails.VillageDetailsModel;
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean;
import com.dhanuka.indhan.acpfarmers.ACPFarmerBean;
import com.dhanuka.indhan.activities.ActivitiesBean;
import com.dhanuka.indhan.activities.FLBean;
import com.dhanuka.indhan.addfarmers.AddFarmerSuccessModel;
import com.dhanuka.indhan.bdoddlist.BdoDdListBean;
import com.dhanuka.indhan.farmerlist.FarmerBean;
import com.dhanuka.indhan.institutional.model.CircularListResponse;
import com.dhanuka.indhan.institutional.model.ContactListResponse;
import com.dhanuka.indhan.institutional.model.DistributorListResponse;
import com.dhanuka.indhan.institutional.model.DistributorVisitSummary;
import com.dhanuka.indhan.institutional.model.InfluencersContactListResponse;
import com.dhanuka.indhan.institutional.model.SugarMillContactListResponse;
import com.dhanuka.indhan.institutional.model.TeaEstateSalesResponse;
import com.dhanuka.indhan.institutional.model.TestimonialListResponse;
import com.dhanuka.indhan.institutional.model.TypeDesignationFLModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersAtpListResponse;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersContactListData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersHeadOfficeListResponse;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersListResponse;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersListingResponse;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersPlanAtpApiModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesResponse;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersTestimonials;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersTypeDesignationFlModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersVisitSummaryResponse;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillContactListData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillHeadOfficeListResponse;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListResponse;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListingResponse;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillPlanAtpApiModel;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillSalesResponse;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillTypeDesignationFlModel;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillVisitSummaryResponse;
import com.dhanuka.indhan.institutional.model.sugarmill.SugarmillAtpListResponse;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateAtpListResponse;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateContactListData;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateListResponse;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpApiModel;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgHeadOfficeListResponse;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgListingResponse;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateVisitSummaryResponse;
import com.dhanuka.indhan.myteam.DDProfileDetails;
import com.dhanuka.indhan.myteam.SessionBean;
import com.dhanuka.indhan.myteam.editdd.EditBean;
import com.dhanuka.indhan.teamsummary.models.TeamSummaryResponseNew;
import com.dhanuka.indhan.teamsummary.models.UpcomingResponse;
import com.dhanuka.indhan.validatefarmers.SuccessBean;
import com.dhanuka.indhan.validatefarmers.ValidatedFarmerBean;
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import io.reactivex.Observable;
import okhttp3.MultipartBody;
import okhttp3.RequestBody;
import ren.yale.android.retrofitcachelibrx2.anno.Cache;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.Multipart;
import retrofit2.http.POST;
import retrofit2.http.Part;
import retrofit2.http.PartMap;
import retrofit2.http.Path;

/**
 * * Interface through which all the api calls will be performed
 */
public interface ApiServiceRequest {

    @POST("v2/login")
    rx.Observable<LoginBean> userLogin(@Body HashMap<String, Object> map);

    @POST("v2/ddprofilesummary")
    rx.Observable<DDProfileBean> ddprofilesummary(@Body HashMap<String, Object> map);

    @POST("v2/villageList")
    rx.Observable<MyVillageBean> villageList(@Body HashMap<String, Object> map);

    @POST("v3/villageList")
    rx.Observable<MyVillageBean> villageListOffline(@Body HashMap<String, Object> map);

    @POST("v2/allVillageList")
    rx.Observable<MyVillageBean> allVillageList(@Body HashMap<String, Object> map);

    @POST("v2/tehshilVillageList")
    rx.Observable<VillageListTehsil> tehshilVillageList(@Body HashMap<String, Object> map);

    @POST("v3/atpList")
    rx.Observable<ATPBean> atpList(@Body HashMap<String, Object> map);

    @POST("v2/addMyAtp")
    rx.Observable<SuccessBean> addMyAtp(@Body HashMap<String, Object> map);

    @POST("v2/closeAtp")
    rx.Observable<ClosedATPBean> closeAtp(@Body HashMap<String, Object> map);

    @POST("v2/getFarmerList")
    rx.Observable<FarmerBean> getFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/newGetFarmerList")
    rx.Observable<FarmerBean> getNewFarmerList(@Body HashMap<String, Object> map);

    @POST("v3/getFarmerList")
    rx.Observable<ACPFarmerBean> getBDOFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/acpMap")
    rx.Observable<ACPFarmerBean> getMApFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/highlightList")
    rx.Observable<HighlightModel> highlightList(@Body HashMap<String, Object> map);

    @POST("v3/SupervisorTicketHistory")
    rx.Observable<TicketHistoryAtpResponse> SupervisorTicketHistory(@Body HashMap<String, Object> map);

    @POST("v2/ddBDOprofilesummary")
    rx.Observable<DDProfileDetails> ddBDOprofilesummary(@Body HashMap<String, Object> map);

    @POST("v2/addDdSeason")
    rx.Observable<SuccessBean> addDdSeason(@Body HashMap<String, Object> map);

    @POST("v2/assignDdvillage")
    rx.Observable<SuccessBean> addVillage(@Body HashMap<String, Object> map);

    @POST("v2/BdoActivityList")
    rx.Observable<ActivitiesBean> BdoActivityList(@Body HashMap<String, Object> map);

    @POST("v2/getValidateFarmerList")
    rx.Observable<ValidatedFarmerBean> getValidateFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/updateCallStatus")
    rx.Observable<SuccessBean> updateCallStatus(@Body HashMap<String, Object> map);

    @POST("v2/UpdateOpenleadStatus")
    rx.Observable<SuccessBean> updateLeadStatus(@Body HashMap<String, Object> map);

    @POST("v2/TestimonialList")
    rx.Observable<TestimonialBean> TestimonialList(@Body HashMap<String, Object> map);

    @POST("v2/supervisorTestimonialList")
    rx.Observable<SupervisorTestimonialBean> supervisorTestimonialList(@Body HashMap<String, Object> map);

    @POST("v2/testimonialRating")
    rx.Observable<SuccessBean> testimonialRating(@Body HashMap<String, Object> map);

    @POST("v3/getMarkVisit")
    rx.Observable<VisitHistoryFarmerBean> getMarkVisit(@Body HashMap<String, Object> map);


    @POST("v2/presneter-monitor")
    rx.Observable<PresenterDataResponse> getPresenterData(@Body HashMap<String, Object> map);

    @Multipart
    @POST("v2/markVisit")
    rx.Observable<MarkVisitBean> markVisit(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @Multipart
    @POST("v2/addFarmer")
    rx.Observable<AddFarmerSuccessModel> addFarmer(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @Multipart
    @POST("v2/editDD")
    rx.Observable<EditBean> editDD(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @GET("v2/logout/{api_token}")
    rx.Observable<LogoutBean> userLogout(@Path("api_token") String api_token);

    @GET("v2/dataSourceList/{api_token}")
    rx.Observable<DataSourceListBean> dataSourceList(@Path("api_token") String api_token);

    @GET("v1/menu/{api_token}")
    rx.Observable<HomeBean> userMenu(@Path("api_token") String api_token);

    @GET("v2/umenu/{api_token}/{token}")
    rx.Observable<HomeBean> userUMenu(@Path("api_token") String api_token, @Path("token") String token);

    @GET("v2/productCrop/{api_token}")
    rx.Observable<ProductCropResponse> prodCrop(@Path("api_token") String api_token);

    @GET("v2/productList/{api_token}")
    rx.Observable<ProductListResponseNew> getProductListNew(@Path("api_token") String api_token);

    @GET("v2/ddList/{api_token}")
    rx.Observable<DDBean> getDDList(@Path("api_token") String api_token);

    @POST("v2/state")
    rx.Observable<StateResponse> userState(@Body HashMap<String, Object> map);

    @POST("v2/BdoLeadDetail")
    rx.Observable<LeadDetailsBean> BdoLeadDetail(@Body HashMap<String, Object> map);

    @POST("v2/flList")
    rx.Observable<FLBean> flList(@Body HashMap<String, Object> map);

    @GET("v2/report/{api_token}")
    rx.Observable<ReportsBean> userReport(@Path("api_token") String api_token);

    @GET("v2/newTicketOpen/{api_token}")
    rx.Observable<NewTicketBean> userNewTicket(@Path("api_token") String api_token);

    @POST("v2/district")
    rx.Observable<DistrictModel> userDistrict(@Body HashMap<String, Object> map);

    @POST("v2/district")
    rx.Observable<DistrictResponseNew> userDistrictNew(@Body HashMap<String, Object> map);

    @POST("v2/tehsil")
    rx.Observable<TehsilModel> userTehsil(@Body HashMap<String, Object> map);

    @POST("v2/tehsil")
    rx.Observable<TahsilResponseNew> userTehsilNew(@Body HashMap<String, Object> map);

    @POST("v2/newTicketSubmit")
    rx.Observable<SubmitTicketBean> submitTicket(@Body HashMap<String, Object> map);


    @POST("v3/delifcfarmer")
    rx.Observable<CommonAtpResponse> removeFarmerFromApi(@Body HashMap<String, Object> map);

    //  @GET("history/{api_token}")
    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v3/ticketHistory/{api_token}/{ticket_number}/{offset}")
    Observable<TicketHistoryModel> userHistory(@Path("api_token") String api_token,
                                               @Path("ticket_number") String ticketId,
                                               @Path("offset") String offset);

    //@POST("vTicket")
    @POST("v2/ticketView")
    rx.Observable<TicketDetailBean> detailTicket(@Body HashMap<String, Object> map);

    //@POST("eTicket")
    @POST("v2/ticketEdit")
    rx.Observable<EditTicketBean> editTicket(@Body HashMap<String, Object> map);

    @POST("v2/ticketClose")
    rx.Observable<EndTicketBean> endTicket(@Body HashMap<String, Object> map);

    @POST("v2/ticketUpdate")
    rx.Observable<SubmitTicketBean> updatedTicket(@Body HashMap<String, Object> map);

    @Cache(time = 2, timeUnit = TimeUnit.SECONDS)
    @GET("v3/farmerRegistrationOpen/{api_token}")
    rx.Observable<RegisterFarmersBean> farmerTicket(@Path("api_token") String api_token);

    @POST("v3/getValidateifcFarmerList")
    rx.Observable<IfcFarmersListResponse> getIfcFarmerList(@Body HashMap<String, Object> map);

    @Cache(time = 2, timeUnit = TimeUnit.SECONDS)
    @GET("v3/ifcfarmerRegistrationOpen/{api_token}")
    rx.Observable<IfcFarmerTicket> ifcFarmerGetTicket(@Path("api_token") String api_token);

    @POST("v2/farmerRegistrationSubmit")
    rx.Observable<CommonModel> submitNumberOptimized(@Body ContactSyncModel model);

    /**
     * john version 2 update new API
     */
    @GET("v2/mySyllabus/{api_token}/{id}")
    rx.Observable<MySyllabusModel> mySyllabus(@Path("api_token") String api_token, @Path("id") int bdo_id);

    // @GET("iPlanActivity/{api_token}")
    @GET("v3/planSyllabus/{api_token}/{updated_date}")
    rx.Observable<PlanActivityDataModel> planActivityData(@Path("api_token") String api_token, @Path("updated_date") String updated_date);

    @GET("v2/planAtpOpen/{api_token}")
    rx.Observable<PlanRequirementDataModel> getATPPlanRequirement(@Path("api_token") String api_token);

    @GET("v2/bdoPlans/{api_token}")
    rx.Observable<BDOPlanListDBModel> getBDOListOfS1(@Path("api_token") String api_token);

    @GET("v2/bdoPlanDetail/{api_token}/{bdo_id}")
    rx.Observable<BDOPlanListDetailDBModel> getBDODetailOfBDOofS1(@Path("api_token") String api_token, @Path("bdo_id") int bdo_id);

    /**
     * List of active tickets
     *
     * @param api_token
     * @return
     */
    @GET("v2/activateTickets/{api_token}")
    rx.Observable<ActiveTicketListModel> activeTickets(@Path("api_token") String api_token);

    @GET("v2/filterList/{api_token}")
    rx.Observable<BDOFilterResponse> getBDOFilterList(@Path("api_token") String api_token);

    @GET("v2/productList/{api_token}")
    rx.Observable<ProductListResponse> getProductList(@Path("api_token") String api_token);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v2/selfParticipationOpenNew/{api_token}/{manager_id}/{manager_level}")
    Observable<SelfParticipationResponse> getSelfparticipationData(@Path("api_token") String api_token, @Path("manager_id") String manager_id, @Path("manager_level") String manager_level);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v2/selfParticipationDetail/{api_token}/{ticket_id}")
    Observable<SPDetailModel> getSPDetailData(@Path("api_token") String api_token, @Path("ticket_id") String ticket_id);

    @GET("v2/customerQueries/{api_token}")
    rx.Observable<CCQueriesModel> getCustomerQueries(@Path("api_token") String api_token);

    @GET("v2/customerCareList/{api_token}")
    rx.Observable<CCQueriesForS1Model> getCustomerQueriesForS1(@Path("api_token") String api_token);

    @GET("v2/selfParticipationPrevious/{api_token}")
    rx.Observable<PreviousPlanResponse> getSelfParticipationPrevious(@Path("api_token") String api_token);


    @GET("v2/activatePrevTickets/{api_token}")
    rx.Observable<PendingActiveTicketlistModel> getActiveTicketPlan(@Path("api_token") String api_token);

    @GET("v2/selfParticipationHistory/{api_token}")
    rx.Observable<SelfParticipationResponse> getSelfparticipationHistoryData(@Path("api_token") String api_token);

    @POST("v2/selfParticipationPlan")
    rx.Observable<ParticipationPlannedResponse> selfParticipationPlan(@Body HashMap<String, Object> map);

    @POST("v2/selfParticipationReason")
    rx.Observable<ParticipationPlannedResponse> selfParticipationReason(@Body HashMap<String, Object> map);

    @POST("v2/Updateplanreason")
    rx.Observable<SuccessBean> Updateplanreason(@Body HashMap<String, Object> map);

    @POST("v2/activateTicketReason")
    rx.Observable<ParticipationPlannedResponse> activateTicketReason(@Body HashMap<String, Object> map);

    @POST("v2/MyAtp")
    rx.Observable<ATPBean> MyAtp(@Body HashMap<String, Object> map);

    @POST("v2/customerQuerySubmit")
    rx.Observable<ParticipationPlannedResponse> customerQuerySubmit(@Body HashMap<String, Object> map);

    @POST("v3/SupervisorActivatedTickets")
    rx.Observable<SupervisorActivateBean> SupervisorActivatedTickets(@Body HashMap<String, Object> map);

    @POST("v2/customerCareAssign")
    rx.Observable<ParticipationPlannedResponse> customerQuery(@Body HashMap<String, Object> map);

    @POST("v2/activateTicketSubmit")
    rx.Observable<SubmitTicketBean> activateTicket(@Body HashMap<String, Object> map);

    @POST("v2/report")
    rx.Observable<ReportsResponse> getReportsData(@Body HashMap<String, Object> map);

    @POST("v2/allefficiency")
    rx.Observable<EfficiencyResponse> getEfficiencyDate(@Body HashMap<String, Object> map);


    @POST("v2/syllabusHistory")
    rx.Observable<PlanSyllabusModel> cPlanActivity(@Body ProductSyllabusModel map);

    @POST("v3/planAtpSubmit")
    rx.Observable<PlanAtpSubmitResponseModel> planAtpSubmit(@Body PlanATPApiModel map);

    @Multipart
    @POST("v2/ticketUpdateImage")
    Call<ImageTicketResponse> uploadImageToTicket(@PartMap() HashMap<String, RequestBody> partMap);

    @Multipart
    @POST("v2/ticketUpdateImage")
    rx.Observable<ImageTicketResponse> uploadImageToTicketw(@PartMap() HashMap<String, RequestBody> partMap);

    @Multipart
    @POST("v3/ticketUpdateImageBytktno")
    rx.Observable<CommonAtpResponse> uploadImageToTicketByNumber(@PartMap() HashMap<String, RequestBody> partMap);

    @Multipart
    @POST("v2/selfParticipationSubmit")
    rx.Observable<ImageTicketResponse> submitSelfParticipationData(@PartMap() HashMap<String, RequestBody> partMap);

    @Multipart
    @POST("v2/testimonialSubmit")
    rx.Observable<TestimonialResponse> submitTestimonialData(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @GET("v2/s1mySyllabus/{api_token}")
    rx.Observable<SharedSyllabusHistoryModel> getSyllabusHistoryData(@Path("api_token") String api_token);

    @Multipart
    @POST("v2/createDemoRequest")
    rx.Observable<CreateRequestDemoResponse> createRequestDemo(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @Multipart
    @POST("v3/addIfcFarmer")
    rx.Observable<CreateRequestDemoResponse> addIfcFarmer(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @POST("v2/DemoRequestList")
    rx.Observable<DemoRequestListResponse> demoRequestList(@Body HashMap<String, Object> map);

    @POST("v2/demoRequestListV1")
    rx.Observable<DemoRequestListResponse> demoRequestListV1(@Body HashMap<String, Object> map);

    @POST("v2/getFarmerList")
    rx.Observable<ExistingFarmerListResponse> getExistingFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/MyLeads")
    rx.Observable<OpenLeadsResponse> getOpenLead(@Body HashMap<String, Object> map);

    @POST("v3/updateifcactivity")
    rx.Observable<CreateRequestDemoResponse> updateActivityDuration(@Body HashMap<String, Object> map);

    @POST("v2/UpdateCloseleadStatus")
    rx.Observable<CloseLeadResponse> getUpdateLeadStatus(@Body HashMap<String, Object> map);

    @POST("v2/registerFarmer")
    rx.Observable<CommonAtpResponse> getRegisterFarmer(@Body HashMap<String, Object> map);

    @POST("v2/inviteFarmer")
    rx.Observable<CommonAtpResponse> getInviteFarmer(@Body HashMap<String, Object> map);

    @POST("v2/closeAtp")
    rx.Observable<CommonAtpResponse> closeAtpApi(@Body HashMap<String, Object> map);

    @Multipart
    @POST("v2/closeAtp")
    rx.Observable<CommonAtpResponse> closeAtpApi(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @POST("v2/activateAtp")
    rx.Observable<CommonAtpResponse> activeDemo(@Body HashMap<String, Object> map);

    @POST("v2/invitedFarmerList")
    rx.Observable<ValidatedfarmerListResponse> validateFarlerListApi(@Body HashMap<String, Object> map);

    @POST("v2/ddStateList")
    rx.Observable<StateResponse> userStateNew(@Body HashMap<String, Object> map);

    @POST("v2/allVillageList")
    rx.Observable<AllVillageListResponse> getAllVillageList(@Body HashMap<String, Object> map);

    @POST("v2/ddVillageList")
    rx.Observable<AllVillageListResponse> getDdVillageList(@Body HashMap<String, Object> map);

    @POST("v2/bdoVillageList")
    rx.Observable<AllVillageBean> bdoVillageList(@Body HashMap<String, Object> map);

    @POST("v2/allSeasonList")
    rx.Observable<SessionBean> allSeasonList(@Body HashMap<String, Object> map);

    @POST("v2/departmentlist")
    rx.Observable<ACPFIlterBean> departmentlist(@Body HashMap<String, Object> map);

    @POST("v2/supervisorReport")
    rx.Observable<SupervisorReportsBean> supervisorReport(@Body HashMap<String, Object> map);

    @POST("v2/distributorList")
    rx.Observable<DistributorResponse> getDistributorList(@Body HashMap<String, Object> map);

    @POST("v2/retailerList")
    rx.Observable<RetailerResponse> getRetailerResponse(@Body HashMap<String, Object> map);

    @POST("v2/cropProductList")
    rx.Observable<CropProductList> getpRoductResponse(@Body HashMap<String, Object> map);

    @POST("v2/teamSummary")
    rx.Observable<TeamSummaryResponseNew> getpTeamSummaryResponse(@Body HashMap<String, Object> map);

    @POST("v2/upcomingFilter")
    rx.Observable<UpcomingResponse> upcomingFilterApi(@Body HashMap<String, Object> map);

    @POST("v2/DemoRequestList")
    rx.Observable<DemoRequestEvaluatedResponse> getDemoRequestEvaluated(@Body HashMap<String, Object> map);

    @Multipart
    @POST("v2/DemoFeedback")
    rx.Observable<CommonAtpResponse> demoFeedbackApi(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    // for multiple file upload
    @Multipart
    @POST("v3/selfParticipationArivalDepartureSubmit")
    Call<ArrivalDepartureResponse> atpFeedBackNewFile(
            @PartMap() Map<String, RequestBody> partMap,
            @Part List<MultipartBody.Part> files);

    @Multipart
    @POST("v3/NewAtpFeedbackTicket")
    Call<CommonAtpResponse> atpFeedbackManualFile(
            @PartMap() Map<String, String> partMap,
            @Part List<MultipartBody.Part> files);

    @Multipart
    @POST("v2/addHighlight")
    rx.Observable<CommonAtpResponse> addHighlight(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @GET("v2/BDOddList/{api_token}")
    rx.Observable<BdoDdListBean> getBdoList(@Path("api_token") String api_token);

    @POST("v2/FarmerActivityList")
    rx.Observable<TicketHistoryModel> bdoFarmerList(@Body HashMap<String, Object> map);

    @POST("v2/villageDetail")
    rx.Observable<VillageDetailsModel> villageDetail(@Body HashMap<String, Object> map);

    @POST("v2/allLocations")
    rx.Observable<AllLocation> allLocations(@Body HashMap<String, Object> map);

    // New API's for State, District, Village and Tehsil
    @POST("v2/newAllStateList")
    rx.Observable<AllStateBean> getNewStateList(@Body HashMap<String, Object> map);

    @POST("v3/newAllDistrictList")
    rx.Observable<AllDistrictBean> getNewDistrictList(@Body HashMap<String, Object> map);

    @POST("v3/newAllVillageList")
    rx.Observable<AllVillageBean> getNewVillageList(@Body HashMap<String, Object> map);

    @POST("v3/getAllterritories")
    rx.Observable<TerritoryResponse> getAllTerritoryList(@Body HashMap<String, Object> map);

    @POST("v3/newAllTehshilList")
    rx.Observable<AllTehsilBean> getNewTehsilList(@Body HashMap<String, Object> map);

    @GET("v3/listgodown/{state_id}")
    rx.Observable<GoDownResponse> getGoDownList(@Path("state_id") String district_id);

    @POST("v2/demoRequestListV1")
    rx.Observable<DemoRequestResponse> getDemoRequestList(@Body HashMap<String, Object> map);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v3/ticketHistory/{api_token}/{ticket_number}/{offset}")
    rx.Observable<TicketHistoryModel> getTicketHistory(@Path("api_token") String api_token,
                                                       @Path("ticket_number") String ticketId,
                                                       @Path("offset") String offset);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v2/logout/{api_token}")
    rx.Observable<TicketHistoryModel> logoutAPI(@Path("api_token") String api_token);

    @Multipart
    @POST("v2/addFarmer")
    Call<AddFarmerSuccessModel> addFarmerNew(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @Multipart
    @POST("v2/createDemoRequest")
    Call<CreateRequestDemoResponse> createDemoRequestNew(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    //my work
    @POST("v1/distdistrict")
    rx.Observable<DistrictResponse> getDistricts(@Body HashMap<String, Object> map);

    @POST("v1/distterritory")
    rx.Observable<TerritoryResponse> getTerritory(@Body HashMap<String, Object> map);

    @POST("v1/distmarket")
    rx.Observable<MarketResponse> getMarketList(@Body HashMap<String, Object> map);

    @POST("v1/adddistmarket")
    rx.Observable<MarketResponse> saveMarketData(@Body HashMap<String, Object> map);

    @POST("v1/adddistributer")
    rx.Observable<AddDistributorResponse> saveDistributorData(@Body HashMap<String, Object> map);

    @POST("v1/updatedistributer")
    rx.Observable<UpdateDistributor> updateDistributorData(@Body HashMap<String, Object> map);

    @POST("v1/listdistributer")
    rx.Observable<com.dhanuka.indhan.Model.DistributorResponse> getDistributor(@Body HashMap<String, Object> map);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v1/listcompany")
    rx.Observable<CompanyList> getBrands();

    @GET("v1/listclass")
    rx.Observable<ListClassResponse> getClassData();

    @POST("v1/listformulation")
    rx.Observable<FormulationResponse> getFormulation(@Body HashMap<String, Object> map);

    @POST("v1/adddiscrictdata")
    rx.Observable<ChemicalAddDistrictWiseResponse> addChemicalDistrictData(@Body HashMap<String, Object> map);

    @POST("v1/updatediscrictdata")
    rx.Observable<UpdateChemicalDistrictWiseData> updateChemicalDistrictData(@Body HashMap<String, Object> map);

    @POST("v1/listdcdiscrict")
    rx.Observable<ChemicalDistrictWiseDataList> chemicalDistrictWiseRecord(@Body HashMap<String, Object> map);

    @GET("v1/listfoodcat")
    rx.Observable<FoodCategoryResponse> getFoodCategory();

    @GET("v3/getreportType")
    rx.Observable<MisSubMenuResponse> getSubMenuReports();

    @GET("v3/demosubmenu/{api_token}")
    rx.Observable<DemoMenuResponse> getSubMenuDemo(@Path("api_token") String token);

    @POST("v3/getreportsList")
    rx.Observable<MisReportsListResponse> getMisReportsList(@Body HashMap<String, Object> map);

    @POST("v1/listfoodsubcat")
    rx.Observable<FoodSubCategoryResponse> getFoodSubCategory(@Body HashMap<String, Object> map);

    @GET("v1/listcompany")
    rx.Observable<CompanyListResponse> getCompanyList();

    @POST("v1/addcompanydata")
    rx.Observable<ChemicalAddCompanyResponse> saveCompanyData(@Body HashMap<String, Object> map);

    @POST("v1/listdccompany")
    rx.Observable<ChemicalCompanyWiseDataList> chemicalCompanyWiseRecord(@Body HashMap<String, Object> map);

    @POST("v1/listdccrop")
    rx.Observable<ChemicalCropWiseListResponse> chemicalCropWiseRecord(@Body HashMap<String, Object> map);

    @POST("v1/updatecompanydata")
    rx.Observable<UpdateCompanyResponse> updatedCompanyData(@Body HashMap<String, Object> map);

    @POST("v1/addcropdata")
    rx.Observable<AddCropResponse> addCropData(@Body HashMap<String, Object> map);

    @POST("v1/updatecropdata")
    rx.Observable<AddCropResponse> updatedCropData(@Body HashMap<String, Object> map);

    @POST("v1/distributorgraphapi")
    rx.Observable<AllDistDistrictReponse> getAllDistricts(@Body HashMap<String, Object> query);

    @POST("v1/chemicalgraphapi")
    rx.Observable<PieChartData> getChemicalChartData(@Body HashMap<String, Object> query);

    @POST("v3/requestNewMaterial")
    rx.Observable<CommonAtpResponse> updateMaterialRequestData(@Body HashMap<String, Object> query);

    @POST("v3/listrequestedMaterial")
    rx.Observable<RequestedMaterialResponse> demoMaterialRquestList(@Body HashMap<String, Object> query);

    @POST("v3/receivedRequestedMaterial")
    rx.Observable<CommonAtpResponse> updateReceivingMaterial(@Body HashMap<String, Object> query);

    @POST("v3/listQueryRequestedMaterial")
    rx.Observable<ChatListResponse> getChatListData(@Body HashMap<String, Object> query);

    @POST("v3/queryRequestedMaterial")
    rx.Observable<CommonAtpResponse> sendChatMessage(@Body HashMap<String, Object> query);

    @POST("v3/listasms")
    rx.Observable<ASMListData> getAsmList(@Body HashMap<String, Object> query);

    @POST("v3/listUtilizationSummary")
    rx.Observable<UtilizationOverview> getUtilizationOverView(@Body HashMap<String, Object> query);

    @POST("v3/listDemoUtilizations")
    rx.Observable<UtilizationSummary> getUtilizationSummary(@Body HashMap<String, Object> query);

    @POST("v3/approveRequestedMaterial")
    rx.Observable<CommonAtpResponse> markApproveOrReject(@Body HashMap<String, Object> query);

    @POST("v3/getTypeDesignationFL")
    rx.Observable<TypeDesignationFLModel> getCompanyDesignationFl(@Body HashMap<String, Object> query);

    @POST("v3/teaEstateListing")
    rx.Observable<TeaEstateStgListingResponse> getTeaEstateStgList(@Body HashMap<String, Object> query);

    @POST("v3/teaEstateListingHeadOfc")
    rx.Observable<TeaEstateStgHeadOfficeListResponse> getTeaEstateStgHeadOfficeList(@Body HashMap<String, Object> query);

    @POST("v3/teaEstateAtpListing")
    rx.Observable<TeaEstateAtpListResponse> getTeaAtpListing(@Body HashMap<String, Object> query);

    @POST("v3/getSugarmillTypeDesignationFL")
    rx.Observable<SugarMillTypeDesignationFlModel> getSuagrMillCompanyDesignationFl(@Body HashMap<String, Object> query);

    @POST("v3/sugarmillListing")
    rx.Observable<SugarMillListingResponse> getSugarMillList(@Body HashMap<String, Object> query);

    @POST("v3/sugarmillListingHeadOfc")
    rx.Observable<SugarMillHeadOfficeListResponse> getSugarMillHeadOfficeList(@Body HashMap<String, Object> query);

    @POST("v3/getListSugarmill")
    rx.Observable<SugarMillListResponse> getSugarMillListing(@Body HashMap<String, Object> query);

    @Multipart
    @POST("v3/createSugarmill")
    rx.Observable<CommonAtpResponse> addSugarMillData(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @POST("v3/sugarmillAtpListing")
    rx.Observable<SugarmillAtpListResponse> getSugarMillAtpListing(@Body HashMap<String, Object> query);

    @POST("v3/addSugarmillContactCompanyBranch")
    rx.Observable<CommonAtpResponse> addContactSugarMill(@Body HashMap<String, Object> query);

    @POST("v3/sugarmillPlanAtp")
    rx.Observable<CommonAtpResponse> planAtpSugarMillSubmit(@Body SugarMillPlanAtpApiModel map);

    @POST("v3/sugarmillSalesReport")
    rx.Observable<SugarMillSalesResponse> salesReportSugarMillList(@Body HashMap<String, Object> query);

    @POST("v3/sugarmillVisitSummary")
    rx.Observable<SugarMillVisitSummaryResponse> visitSummarySugarMill(@Body HashMap<String, Object> query);

    @GET("v3/getAllSugarmillContactsById/{sugarmill_id}/{time}")
    rx.Observable<SugarMillContactListData> getSugarMillContactData(@Path("sugarmill_id") String tea_id, @Path("time") String time);

    @Multipart
    @POST("v3/sugarmillArrivalDeparture")
    Call<ArrivalDepartureResponse> arrivalDepartureSugarMill(@PartMap() Map<String, RequestBody> map, @Part ArrayList<MultipartBody.Part> parts);

    @Multipart
    @POST("v3/createTeaEstate")
    rx.Observable<CommonAtpResponse> addTeaEstateData(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @POST("v3/getListTeaEstate")
    rx.Observable<TeaEstateListResponse> getTeaEstateListing(@Body HashMap<String, Object> query);

    @POST("v3/addContactCompanyBranch")
    rx.Observable<CommonAtpResponse> addContacts(@Body HashMap<String, Object> query);


    @POST("v3/teaEstatePlanAtp")
    rx.Observable<CommonAtpResponse> planAtpTeaEstateSubmit(@Body TeaEstatePlanAtpApiModel map);

    @POST("v3/teaEstateSalesReport")
    rx.Observable<TeaEstateSalesResponse> salesReportList(@Body HashMap<String, Object> query);

    @POST("v3/teaEstateVisitSummary")
    rx.Observable<TeaEstateVisitSummaryResponse> visitSummary(@Body HashMap<String, Object> query);

    @GET("v3/getAllContactsById/{tea_id}/{time}")
    rx.Observable<TeaEstateContactListData> getContactData(@Path("tea_id") String tea_id, @Path("time") String time);

    @GET("v3/testimonialListInstitutional/{time}")
    rx.Observable<TestimonialListResponse> getTestimonialList(@Path("time") String time);

    @GET("v3/getRatingTestimonialList/{time}")
    rx.Observable<InfluencersTestimonials> getTestimonialInfluencersList(@Path("time") String time);

    @Multipart
    @POST("v3/teaEstateArrivalDeparture")
    Call<ArrivalDepartureResponse> arrivalDeparture(@PartMap() Map<String, RequestBody> map, @Part ArrayList<MultipartBody.Part> parts);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v3/listterritories/{district_id}")
    rx.Observable<TerritoryResponse> getAllTerritory(@Path("district_id") String district_id);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v3/listmarket/{teritory_id}")
    rx.Observable<MarketResponse> getAllMarketList(@Path("teritory_id") String teritory_id);

    @Cache(time = 5, timeUnit = TimeUnit.SECONDS)
    @GET("v3/listdistricts/{state_id}")
    rx.Observable<DistrictResponse> getDistricts(@Path("state_id") String state_id);

    @POST("v3/createDistributorAdvisor")
    rx.Observable<ArrivalDepartureResponse> addDistributor(@Body HashMap<String, Object> query);

    @POST("v3/getDistributorAdvisorList")
    rx.Observable<DistributorListResponse> getDistributorData(@Body HashMap<String, Object> query);

    @POST("v3/getAllContacts")
    rx.Observable<ContactListResponse> getAllContactsTeaEstate(@Body HashMap<String, Object> query);


    @POST("v3/getAllSugarmillContacts")
    rx.Observable<SugarMillContactListResponse> getAllContactsSugarMill(@Body HashMap<String, Object> query);


    @POST("v3/getAllInfluencerContacts")
    rx.Observable<InfluencersContactListResponse> getAllContactsInfluencers(@Body HashMap<String, Object> query);

    @POST("v3/distributorArrivalDepartureHistory")
    rx.Observable<DistributorVisitSummary> getDistributorVisitSummaryData(@Body HashMap<String, Object> query);

    @Multipart
    @POST("v3/addDistributorArrivalDeparture")
    Call<ArrivalDepartureResponse> arrivalDepartureDistributor(@PartMap() Map<String, RequestBody> map, @Part ArrayList<MultipartBody.Part> parts);

    @POST("v3/createCircular")
    rx.Observable<ArrivalDepartureResponse> createCircular(@Body HashMap<String, Object> query);

    @POST("v3/getCircular")
    rx.Observable<CircularListResponse> getCirculars(@Body HashMap<String, Object> query);

    @POST("v3/getInfluencerTypeDesignationFL")
    rx.Observable<InfluencersTypeDesignationFlModel> getInfluencersCompanyDesignationFl(@Body HashMap<String, Object> query);

    @POST("v3/influencerListing")
    rx.Observable<InfluencersListingResponse> getInfluencersList(@Body HashMap<String, Object> query);

    @POST("v3/influencerListingHeadOfc")
    rx.Observable<InfluencersHeadOfficeListResponse> getInfluencersHeadOfficeList(@Body HashMap<String, Object> query);

    @POST("v3/getListInfluencer")
    rx.Observable<InfluencersListResponse> getInfluencersListing(@Body HashMap<String, Object> query);

    @Multipart
    @POST("v3/createInfluencer")
    rx.Observable<CommonAtpResponse> addInfluencersData(@PartMap() HashMap<String, RequestBody> partMap, @Part MultipartBody.Part part);

    @POST("v3/influencerAtpListing")
    rx.Observable<InfluencersAtpListResponse> getInfluencersAtpListing(@Body HashMap<String, Object> query);

    @POST("v3/addInfluencerContactCompanyBranch")
    rx.Observable<CommonAtpResponse> addContactInfluencers(@Body HashMap<String, Object> query);

    @POST("v3/influencerPlanAtp")
    rx.Observable<CommonAtpResponse> planAtpInfluencersSubmit(@Body InfluencersPlanAtpApiModel map);

    @POST("v3/influencerSalesReport")
    rx.Observable<InfluencersSalesResponse> salesReportInfluencersList(@Body HashMap<String, Object> query);

    @POST("v3/influencerVisitSummary")
    rx.Observable<InfluencersVisitSummaryResponse> visitSummaryInfluencers(@Body HashMap<String, Object> query);

    @GET("v3/getAllInfluencerContactsById/{sugarmill_id}/{time}")
    rx.Observable<InfluencersContactListData> getInfluencersContactData(@Path("sugarmill_id") String tea_id, @Path("time") String time);

    @Multipart
    @POST("v3/influencerArrivalDeparture")
    Call<ArrivalDepartureResponse> arrivalDepartureInfluencers(@PartMap() Map<String, RequestBody> map, @Part ArrayList<MultipartBody.Part> parts);

}
