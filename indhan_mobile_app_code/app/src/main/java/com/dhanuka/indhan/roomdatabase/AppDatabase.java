package com.dhanuka.indhan.roomdatabase;

import android.annotation.SuppressLint;

import androidx.room.Database;
import androidx.room.RoomDatabase;
import androidx.room.TypeConverters;

import com.dhanuka.indhan.ATPMenu.activity.ATPBean;
import com.dhanuka.indhan.ATPMenu.activity.models.ValidatedfarmerListResponse;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean;
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel;
import com.dhanuka.indhan.Activity.misAndReports.model.MisSubMenu;
import com.dhanuka.indhan.Activity.misAndReports.model.ReportsListData;
import com.dhanuka.indhan.Activity.offlineClaimTicket.ManualTicketClaimImage;
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline;
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestOfflineModel;
import com.dhanuka.indhan.DemoRequestEvaluated.models.ChatListData;
import com.dhanuka.indhan.DemoRequestEvaluated.models.DemoSubMenu;
import com.dhanuka.indhan.DemoRequestEvaluated.models.EvaluateRequestModel;
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownData;
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestMaterialModel;
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData;
import com.dhanuka.indhan.MarkVisits.MarkVisitRequestModel;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllDistrictBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllStateBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllTehsilBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.AllVillageBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.CropBean;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.DemoRequestList;
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.ProductBean;
import com.dhanuka.indhan.Model.TerritoryResponse;
import com.dhanuka.indhan.MyVillage.MyVillageBean;
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.ActivityDurationModel;
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer;
import com.dhanuka.indhan.RegisterFarmerScreen.normalFarmerRegistration.FarmerRegistrationBean;
import com.dhanuka.indhan.addfarmers.model.AddFarmerModel;
import com.dhanuka.indhan.bdoddlist.BdoDdListBean;
import com.dhanuka.indhan.farmerlist.FarmerBean;
import com.dhanuka.indhan.institutional.model.CategoryData;
import com.dhanuka.indhan.institutional.model.CircularListData;
import com.dhanuka.indhan.institutional.model.CompanyTypeDataList;
import com.dhanuka.indhan.institutional.model.ContactInfluencers;
import com.dhanuka.indhan.institutional.model.ContactSugarMill;
import com.dhanuka.indhan.institutional.model.ContactTeaEstate;
import com.dhanuka.indhan.institutional.model.DesignationDataList;
import com.dhanuka.indhan.institutional.model.DistributorListData;
import com.dhanuka.indhan.institutional.model.DistributorVisit;
import com.dhanuka.indhan.institutional.model.FlDataList;
import com.dhanuka.indhan.institutional.model.MarkArrivalDepartureDistributor;
import com.dhanuka.indhan.institutional.model.TeaEstateSalesData;
import com.dhanuka.indhan.institutional.model.TestimonialListData;
import com.dhanuka.indhan.institutional.model.influencers.AddInfluencersBranchModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencerTestimonialData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersAtpListData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersCategoryData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersCompanyData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersDesignationData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersFlData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersHeadOfficeData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersListData;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersMarkArrivalDepartureAtpModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersPlanAtpModel;
import com.dhanuka.indhan.institutional.model.influencers.InfluencersSalesData;
import com.dhanuka.indhan.institutional.model.sugarMill.AddSugarMillBranchModel;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillCategoryData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillCompanyData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillDesignationData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillFlData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillHeadOfficeData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListData;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillMarkArrivalDepartureAtpModel;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillPlanAtpModel;
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillSalesData;
import com.dhanuka.indhan.institutional.model.sugarmill.SugarMillAtpListData;
import com.dhanuka.indhan.institutional.model.teaEstate.AddTeaEstateStgBranchModel;
import com.dhanuka.indhan.institutional.model.teaEstate.AtpListData;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateMarkArrivalDepartureAtpModel;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstatePlanAtpModel;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgData;
import com.dhanuka.indhan.institutional.model.teaEstate.TeaEstateStgHeadOfficeData;
import com.dhanuka.indhan.institutional.model.teaEstate.TeasEstateListData;
import com.dhanuka.indhan.roomdatabase.convertors.CircularContactConvertor;
import com.dhanuka.indhan.roomdatabase.convertors.CircularDataConvertor;
import com.dhanuka.indhan.roomdatabase.convertors.ContactConvertor;
import com.dhanuka.indhan.roomdatabase.convertors.CropConverter;
import com.dhanuka.indhan.roomdatabase.convertors.InfluencersContactConvertor;
import com.dhanuka.indhan.roomdatabase.convertors.ProductConverter;
import com.dhanuka.indhan.roomdatabase.convertors.SugarMillContactConvertor;
import com.dhanuka.indhan.roomdatabase.convertors.VisitHistoryFarmerConvertor;
import com.dhanuka.indhan.roomdatabase.dao.ATPCropDao;
import com.dhanuka.indhan.roomdatabase.dao.ATPFarmerDao;
import com.dhanuka.indhan.roomdatabase.dao.ATPListDao;
import com.dhanuka.indhan.roomdatabase.dao.ATPProductDao;
import com.dhanuka.indhan.roomdatabase.dao.ActivityDurationDao;
import com.dhanuka.indhan.roomdatabase.dao.AddFarmerDao;
import com.dhanuka.indhan.roomdatabase.dao.AddInfluencersBranchDao;
import com.dhanuka.indhan.roomdatabase.dao.AddSugarMillBranchDao;
import com.dhanuka.indhan.roomdatabase.dao.AddTeaEstateStgBranchDao;
import com.dhanuka.indhan.roomdatabase.dao.AllFarmersDao;
import com.dhanuka.indhan.roomdatabase.dao.ApiTimeDao;
import com.dhanuka.indhan.roomdatabase.dao.ArrivalDepartureDistributorDao;
import com.dhanuka.indhan.roomdatabase.dao.ArrivalDepartureParticipationDao;
import com.dhanuka.indhan.roomdatabase.dao.AtpListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.CategoryDataDao;
import com.dhanuka.indhan.roomdatabase.dao.ChatListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.CircularsListDao;
import com.dhanuka.indhan.roomdatabase.dao.CompanyTypeDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.ContactInfluencersDao;
import com.dhanuka.indhan.roomdatabase.dao.ContactSugarMillDao;
import com.dhanuka.indhan.roomdatabase.dao.ContactTeaEstateDao;
import com.dhanuka.indhan.roomdatabase.dao.CreateOfflineTicketDao;
import com.dhanuka.indhan.roomdatabase.dao.CropDao;
import com.dhanuka.indhan.roomdatabase.dao.DDListDao;
import com.dhanuka.indhan.roomdatabase.dao.DataSourceDao;
import com.dhanuka.indhan.roomdatabase.dao.DemoRequestDao;
import com.dhanuka.indhan.roomdatabase.dao.DemoRequestListDao;
import com.dhanuka.indhan.roomdatabase.dao.DemoSubMenuDao;
import com.dhanuka.indhan.roomdatabase.dao.DesignationDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.DistributorListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.DistributorVisitDao;
import com.dhanuka.indhan.roomdatabase.dao.DistrictDao;
import com.dhanuka.indhan.roomdatabase.dao.EvaluateDemoRequestDao;
import com.dhanuka.indhan.roomdatabase.dao.FarmerRegistrationDao;
import com.dhanuka.indhan.roomdatabase.dao.FlDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.GoDownDataDao;
import com.dhanuka.indhan.roomdatabase.dao.IfcFarmerDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersAtpListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersCategoryDataDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersCompanyTypeDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersDesignationDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersFlDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersHeadOfficeListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersListDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersMarkArrivalDepartureAtpDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersPlanAtpModelDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersSalesDataDao;
import com.dhanuka.indhan.roomdatabase.dao.InfluencersTestimonialsDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.ManualTicketClaimDao;
import com.dhanuka.indhan.roomdatabase.dao.MarkArrivalDepartureAtpDao;
import com.dhanuka.indhan.roomdatabase.dao.MarkVisitDao;
import com.dhanuka.indhan.roomdatabase.dao.MisReportsListDao;
import com.dhanuka.indhan.roomdatabase.dao.MisReportsSubMenuDao;
import com.dhanuka.indhan.roomdatabase.dao.MyVillageDao;
import com.dhanuka.indhan.roomdatabase.dao.ProductDao;
import com.dhanuka.indhan.roomdatabase.dao.RequestMaterialModelDao;
import com.dhanuka.indhan.roomdatabase.dao.RequestedMaterialListDao;
import com.dhanuka.indhan.roomdatabase.dao.StateDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillAtpListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillCategoryDataDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillCompanyTypeDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillDesignationDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillFlDataListDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillHeadOfficeListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillListDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillMarkArrivalDepartureAtpDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillPlanAtpModelDao;
import com.dhanuka.indhan.roomdatabase.dao.SugarMillSalesDataDao;
import com.dhanuka.indhan.roomdatabase.dao.TeaEstateListDao;
import com.dhanuka.indhan.roomdatabase.dao.TeaEstatePlanAtpModelDao;
import com.dhanuka.indhan.roomdatabase.dao.TeaEstateSalesDataDao;
import com.dhanuka.indhan.roomdatabase.dao.TeaEstateStgDropDownListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.TeaEstateStgListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.TehsilDao;
import com.dhanuka.indhan.roomdatabase.dao.TerritoryDao;
import com.dhanuka.indhan.roomdatabase.dao.TestimonialListDataDao;
import com.dhanuka.indhan.roomdatabase.dao.TicketHistoryDao;
import com.dhanuka.indhan.roomdatabase.dao.VillageDao;
import com.dhanuka.indhan.roomdatabase.dao.VisitHistoryDao;
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel;
import com.dhanuka.indhan.roomdatabase.model.OfflineTicketModel;
import com.dhanuka.indhan.visithistory.VisitHistoryFarmerBean;

@SuppressLint("RestrictedApi")
@Database(entities = {TicketHistoryModel.Datum.class, ATPBean.Data.class,
        AllStateBean.StateData.class, AllDistrictBean.DistrictData.class,
        AllVillageBean.VillageData.class, AllTehsilBean.TehsilData.class,
        BdoDdListBean.Data.class, DemoRequestList.class, DataSourceBean.class,
        CropBean.class, ProductBean.class, OfflineTicketModel.class, FarmerRegistrationBean.class,
        ATPBean.Data.Crop.class, ATPBean.Data.Product.class, ValidatedfarmerListResponse.Data.class,
        AddFarmerModel.class, DemoRequestOfflineModel.class, FarmerBean.VillageFarmerData.class,
        MyVillageBean.MyVillageData.class, MarkVisitRequestModel.class, EvaluateRequestModel.class,
        ApiTimeTableModel.class, AddIfcFarmer.class, ActivityDurationModel.class,
        ManualTicketClaimImage.class, ArrivalDepartureModelOffline.class,
        MisSubMenu.class, ReportsListData.class, DemoSubMenu.class, GoDownData.class,
        RequestMaterialModel.class, RequestedMaterialResponseData.class, ChatListData.class,
        CompanyTypeDataList.class, DesignationDataList.class, FlDataList.class, TestimonialListData.class,
        TeaEstateStgData.class, TeaEstateSalesData.class, TeaEstatePlanAtpModel.class,
        TeasEstateListData.class, AtpListData.class, CategoryData.class,
        TeaEstateMarkArrivalDepartureAtpModel.class, AddTeaEstateStgBranchModel.class, DistributorListData.class,
        MarkArrivalDepartureDistributor.class, DistributorVisit.class, ContactTeaEstate.class, CircularListData.class,
        SugarMillListData.class, AddSugarMillBranchModel.class, SugarMillAtpListData.class,
        SugarMillMarkArrivalDepartureAtpModel.class, SugarMillPlanAtpModel.class, SugarMillData.class,
        SugarMillSalesData.class, SugarMillFlData.class, SugarMillDesignationData.class,
        SugarMillCompanyData.class, SugarMillCategoryData.class, InfluencersListData.class,
        AddInfluencersBranchModel.class, InfluencersAtpListData.class,
        InfluencersMarkArrivalDepartureAtpModel.class, InfluencersPlanAtpModel.class, InfluencersData.class,
        InfluencersSalesData.class, InfluencersFlData.class, InfluencersDesignationData.class,
        InfluencersCompanyData.class, InfluencersCategoryData.class, ContactInfluencers.class, ContactSugarMill.class,
        VisitHistoryFarmerBean.Data.class, InfluencerTestimonialData.class, TeaEstateStgHeadOfficeData.class,
        TerritoryResponse.TerritoryData.class,
        SugarMillHeadOfficeData.class, InfluencersHeadOfficeData.class}, version = 1)
@TypeConverters({CropConverter.class, ProductConverter.class, ContactConvertor.class,
        CircularContactConvertor.class, CircularDataConvertor.class, SugarMillContactConvertor.class,
        InfluencersContactConvertor.class, VisitHistoryFarmerConvertor.class})
public abstract class AppDatabase extends RoomDatabase {

    public abstract TicketHistoryDao ticketHistoryDao();

    public abstract ATPListDao atpListDao();

    public abstract StateDao stateDao();

    public abstract MyVillageDao myVillageDao();

    public abstract DistrictDao districtDao();

    public abstract VillageDao villageDao();

    public abstract TerritoryDao territoryDao();

    public abstract TehsilDao tehsilDao();

    public abstract DDListDao ddListDao();

    public abstract IfcFarmerDao ifcFarmerDao();

    public abstract ActivityDurationDao activityDurationDao();

    public abstract AllFarmersDao allFarmerListDao();

    public abstract DemoRequestListDao demoRequestListDao();

    public abstract DataSourceDao dataSourceDao();

    public abstract ProductDao productDao();

    public abstract CropDao cropDao();

    public abstract ApiTimeDao apiTimeDao();

    public abstract CreateOfflineTicketDao createOfflineTicketDao();

    public abstract FarmerRegistrationDao farmerRegistrationDao();

    public abstract ATPCropDao atpCropDao();

    public abstract ATPProductDao atpProductDao();

    public abstract ATPFarmerDao atpFarmerDao();

    public abstract AddFarmerDao addFarmerDao();

    public abstract DemoRequestDao demoRequestDao();

    public abstract MarkVisitDao markVisitRequestDao();

    public abstract EvaluateDemoRequestDao evaluateDemoRequest();

    public abstract ArrivalDepartureParticipationDao arrivalDepartureParticipationDao();

    public abstract ManualTicketClaimDao manualTicketClaimDao();

    public abstract MisReportsSubMenuDao misReportsSubMenuDao();

    public abstract MisReportsListDao misReportsListDao();

    public abstract GoDownDataDao goDownDataDao();

    public abstract DemoSubMenuDao demoSubMenuDao();

    public abstract RequestMaterialModelDao requestMaterialModelDao();

    public abstract RequestedMaterialListDao requestedMaterialListDao();

    public abstract ChatListDataDao chatListDataDao();

    /**
     * TEA ESTATE START
     **/
    //TeaEstates Dao
    public abstract TeaEstateListDao teaEstateListDao();

    public abstract AddTeaEstateStgBranchDao addTeaEstateStgBranchDao();

    //AtpListData
    public abstract AtpListDataDao atpListDataDao();

    //MarkArrivalDepartureAtpModel
    public abstract MarkArrivalDepartureAtpDao markArrivalDepartureAtpDao();

    //TeaEstatePlanAtpModel
    public abstract TeaEstatePlanAtpModelDao teaEstatePlanAtpModelDao();

    //TeaEstateSalesDataList
    public abstract TeaEstateSalesDataDao teaEstateSalesDataDao();

    //TeaEstateStgData
    public abstract TeaEstateStgListDataDao teaEstateStgListDataDao();

    //TeaEstateStgData
    public abstract TeaEstateStgDropDownListDataDao teaEstateStgDropDownListDataDao();

    //CompanyTypeDataList
    public abstract CompanyTypeDataListDao companyTypeDataListDao();

    //DesignationDataList
    public abstract DesignationDataListDao designationDataListDao();

    //DesignationDataList
    public abstract CategoryDataDao categoryDataDao();

    //FlDataList
    public abstract FlDataListDao flDataListDao();

    /**
     * SUGAR MILL START
     **/
    public abstract SugarMillListDao sugarMillListDao();

    public abstract AddSugarMillBranchDao addSugarMillBranchDao();

    //AtpListData
    public abstract SugarMillAtpListDataDao atpListDataSugarMillDao();

    //MarkArrivalDepartureAtpModel
    public abstract SugarMillMarkArrivalDepartureAtpDao markArrivalDepartureAtpSugarMillDao();

    //SugarMillPlanAtpModel
    public abstract SugarMillPlanAtpModelDao sugarMillPlanAtpModelDao();

    //SugarMillSalesDataList
    public abstract SugarMillSalesDataDao sugarMillSalesDataDao();

    //SugarMillStgData
    public abstract SugarMillListDataDao sugarMillListDataDao();

    public abstract SugarMillHeadOfficeListDataDao sugarMillHeadOfficeListDataDao();

    //CompanyTypeDataList
    public abstract SugarMillCompanyTypeDataListDao companyTypeDataListSugarMillDao();

    //DesignationDataList
    public abstract SugarMillDesignationDataListDao designationDataListSugarMillDao();

    //DesignationDataList
    public abstract SugarMillCategoryDataDao categoryDataSugarMillDao();

    //FlDataList
    public abstract SugarMillFlDataListDao flDataListSugarMillDao();

    /**
     * SUGAR MILL END
     **/

    /**
     * INFLUENCERS START
     **/
    public abstract InfluencersListDao influencersListDao();

    public abstract AddInfluencersBranchDao addInfluencersBranchDao();

    //AtpListData
    public abstract InfluencersAtpListDataDao atpListDataInfluencersDao();

    //MarkArrivalDepartureAtpModel
    public abstract InfluencersMarkArrivalDepartureAtpDao markArrivalDepartureAtpInfluencersDao();

    //InfluencersPlanAtpModel
    public abstract InfluencersPlanAtpModelDao influencersPlanAtpModelDao();

    //InfluencersSalesDataList
    public abstract InfluencersSalesDataDao influencersSalesDataDao();

    //InfluencersStgData
    public abstract InfluencersListDataDao influencersListDataDao();

    public abstract InfluencersHeadOfficeListDataDao influencersHeadOfficeListDataDao();

    //CompanyTypeDataList
    public abstract InfluencersCompanyTypeDataListDao companyTypeDataListInfluencersDao();

    //DesignationDataList
    public abstract InfluencersDesignationDataListDao designationDataListInfluencersDao();

    //DesignationDataList
    public abstract InfluencersCategoryDataDao categoryDataInfluencersDao();

    //FlDataList
    public abstract InfluencersFlDataListDao flDataListInfluencersDao();

    //FlDataList
    public abstract InfluencersTestimonialsDataListDao influencersTestimonialsDataListDao();

    /**
     * INFLUENCERS END
     **/
    //TestimonialListData
    public abstract TestimonialListDataDao testimonialListDataDao();

    //DistributorDaoList
    public abstract DistributorListDataDao distributorListDataDao();

    public abstract ArrivalDepartureDistributorDao arrivalDepartureDistributorDao();

    //DistributorVisitDaoList
    public abstract DistributorVisitDao distributorVisitDao();

    //ContactTeaEstateDaoList
    public abstract ContactTeaEstateDao contactTeaEstateDao();

    //ContactTeaEstateDaoList
    public abstract ContactSugarMillDao contactSugarMillDao();

    //ContactTeaEstateDaoList
    public abstract ContactInfluencersDao contactInfluecersDao();

    //ContactTeaEstateDaoList
    public abstract CircularsListDao circularsListDao();

    public abstract VisitHistoryDao visitHistoryDao();

}