package com.dhanuka.indhan.DataBase.DbStructure;

import android.database.Cursor;

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean;
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel;
import com.dhanuka.indhan.DataBase.TableDataModel.MySyllabusDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDetail;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpList;
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse;
import com.dhanuka.indhan.DataBase.TableDataModel.UserTeamModel;
import com.dhanuka.indhan.DataBase.TableStructureModels.BDOPlanS1DetailTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.BDOPlanS1Table;
import com.dhanuka.indhan.DataBase.TableStructureModels.CropTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.DataSourceTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.DistrictTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ImageOfflineTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.LanguageTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.MenuTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.MySyllabusForBDOTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.PlanAtpTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ProductCropTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ProductTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.StateTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.TehsilTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable;
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel;
import com.dhanuka.indhan.Model.LanguageModel;

/**
 * Created by John on 5/4/2018.
 */
public class CursorModelEvaluater {
    public static MenuDBModel getMenuModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(MenuTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(MenuTable.Cols.NAME));
        String imageurl = cursor.getString(cursor.getColumnIndex(MenuTable.Cols.IMAGE_URL)) != null ? cursor.getString(cursor.getColumnIndex(MenuTable.Cols.IMAGE_URL)) : "";
        int notificationCount = cursor.getInt(cursor.getColumnIndex(MenuTable.Cols.NOTIFICATION_COUNT));
        String target = cursor.getString(cursor.getColumnIndex(MenuTable.Cols.TARGET));
        int menu_show = cursor.getInt(cursor.getColumnIndex(MenuTable.Cols.MENU_SHOW));
        // String dropbox_url = cursor.getString(cursor.getColumnIndex(MenuTable.Cols.DROPBOX_URL));
        return new MenuDBModel(String.valueOf(id), name, target, imageurl, notificationCount, menu_show);
    }

    public static MySyllabusDBModel getMySyllabusModel(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.ID));
        int product_id = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.PRODUCT_ID));
        String product_name = cursor.getString(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.PRODUCT_NAME));
        String crop_name = cursor.getString(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.CROP_NAME));
        int crop_id = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.CROP_ID));
        int no_of_days = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.NO_OF_DAYS));
        int no_of_dd = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.NO_OF_DD));
        int month = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.MONTH));
        int year = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.YEAR));
        int atp = cursor.getInt(cursor.getColumnIndex(MySyllabusForBDOTable.Cols.ATP));
        MySyllabusDBModel model = new MySyllabusDBModel();
        model.setId(id);
        model.setProduct_id(product_id);
        model.setProduct_name(product_name);
        model.setCrop_id(crop_id);
        model.setCrop_name(crop_name);
        model.setNo_of_days(no_of_days);
        model.setNo_of_dd(no_of_dd);
        model.setMonth(month);
        model.setAtp(atp);
        model.setYear(year);
        return model;
    }

    public static ProductBean getProductModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(ProductTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(ProductTable.Cols.PRODUCT_NAME));
        ProductBean bean = new ProductBean();
        bean.setItem(name);
        bean.setpID(id);
        return bean;
    }

    public static ProductBean getProductModelFromProductTableCursor(Cursor cursor) {
        ProductCropResponse.ProductCropList model = getProductCropDetails(cursor);
        ProductBean bean = new ProductBean();
        bean.setItem(model.getProduct());
        bean.setpID(Integer.parseInt(model.getCropId()));
        return bean;
    }

    public static ImageOfflineModel getImageOfflineModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(ImageOfflineTable.Cols.ID));
        int is_uploaded = cursor.getInt(cursor.getColumnIndex(ImageOfflineTable.Cols.IS_UPLOADED));
        String image_url_local = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.IMAGE_URL_LOCAL));
        String lattitude = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.LATTITUDE));
        String longitude = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.LONGITUDE));
        String ticket_id = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.TICKET_ID));
        String type = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.TYPE));
        String description = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.DESCRIPTION));
        String date = cursor.getString(cursor.getColumnIndex(ImageOfflineTable.Cols.DATE));
        int isOffline = cursor.getInt(cursor.getColumnIndex(ImageOfflineTable.Cols.TICKET_OFFLINE));
        ImageOfflineModel bean = new ImageOfflineModel();
        bean.setId(id);
        bean.setIsUploaded(is_uploaded);
        bean.setImage_url_local(image_url_local);
        bean.setIsTicketOffline(isOffline);
        bean.setLattitude(lattitude);
        bean.setLongitude(longitude);
        bean.setTicket_id(ticket_id);
        bean.setType(type);
        bean.setDescription(description);
        bean.setDate(date);
        return bean;
    }

    public static CropBean getCropModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(CropTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(CropTable.Cols.CROP_NAME));
        CropBean bean = new CropBean();
        bean.setItem(name);
        bean.setcID(id);
        return bean;
    }

    public static CropBean getCropModelFromProductCropCursor(Cursor cursor) {
        ProductCropResponse.ProductCropList model = getProductCropDetails(cursor);
        CropBean bean = new CropBean();
        bean.setItem(model.getCrop());
        bean.setcID(Integer.parseInt(model.getCropId()));
        return bean;
    }

    public static DistrictBean getDistrictFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(DistrictTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(DistrictTable.Cols.DISTRICT_NAME));
        DistrictBean bean = new DistrictBean();
        bean.setItem(name);
        bean.setdID(id);
        return bean;
    }

    public static TehsilBean getTehsilFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(TehsilTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(TehsilTable.Cols.TEHSIL_NAME));
        TehsilBean bean = new TehsilBean();
        bean.setItem(name);
        bean.settID(id);
        return bean;
    }

    public static DataSourceBean getDataSourcesModel(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(DataSourceTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(DataSourceTable.Cols.Name));
        DataSourceBean bean = new DataSourceBean();
        bean.setItem(name);
        bean.setDsID(id);
        return bean;
    }

    public static StateBean getStateModel(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(StateTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(StateTable.Cols.STATE_NAME));
        StateBean bean = new StateBean();
        bean.setItem(name);
        bean.setsID(id);
        return bean;
    }


    public static PlanAtpList getBDOPlanS1AtpList(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(BDOPlanS1Table.Cols.ID));
        int localid = cursor.getInt(cursor.getColumnIndex(BDOPlanS1Table.Cols.PreID));
        String name = cursor.getString(cursor.getColumnIndex(BDOPlanS1Table.Cols.BDO_NAME));
        int activities = cursor.getInt(cursor.getColumnIndex(BDOPlanS1Table.Cols.ACTIVITES));
        int month = cursor.getInt(cursor.getColumnIndex(BDOPlanS1Table.Cols.MONTH));
        PlanAtpList bean = new PlanAtpList();
        bean.setId(id);
        bean.setLocal_id(localid);
        bean.setName(name);
        bean.setActivites(activities);
        bean.setMonth(month);
        return bean;
    }

    public static ProductCropResponse.ProductCropList getProductCropDetails(Cursor cursor) {
        String localid = cursor.getString(cursor.getColumnIndex(ProductCropTable.Cols.CROP_ID));
        int id = cursor.getInt(cursor.getColumnIndex(ProductCropTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(ProductCropTable.Cols.CROP));
        String activities = cursor.getString(cursor.getColumnIndex(ProductCropTable.Cols.PRODUCT_ID));
        String prod = cursor.getString(cursor.getColumnIndex(ProductCropTable.Cols.PRODUCT));
        String month = cursor.getString(cursor.getColumnIndex(ProductCropTable.Cols.CATEGORY));
        ProductCropResponse.ProductCropList bean = new ProductCropResponse.ProductCropList();
        bean.setId(id);
        bean.setCropId(localid);
        bean.setCrop(name);
        bean.setProductId(activities);
        bean.setProduct(prod);
        bean.setProductCropCategoryId(month);
        return bean;
    }

    public static LanguageModel getLanguageListDetails(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(LanguageTable.Cols.ID));
        String name = cursor.getString(cursor.getColumnIndex(LanguageTable.Cols.LANGUAGE_NAME));
        LanguageModel bean = new LanguageModel();
        bean.setId(String.valueOf(id));
        bean.setLanguage(name);
        return bean;
    }

    public static PlanAtpDetail getBDOPlanS1AtpListDetail(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.ID));
        int plan_atp_id = cursor.getInt(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.PLAN_ATP_ID));
        String name = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.NAME));
        String product = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.PRODUCT));
        String product1 = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.PRODUCT1));
        String product2 = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.PRODUCT2));
        String crop = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.CROP));
        String dataSource = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.DATA_SOURCE));
        String state = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.STATE));
        String tehsil = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.TEHSIL));
        String district = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.DISTRICT));
        String village = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.VILLAGE));
        int expected_farmer = cursor.getInt(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.EXPECTED_FARMER));
        int expected_pda = cursor.getInt(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.EXPECTED_PDA));
        String plan_date = cursor.getString(cursor.getColumnIndex(BDOPlanS1DetailTable.Cols.PLAN_DATE));
        PlanAtpDetail bean = new PlanAtpDetail();
        bean.setId(id);
        bean.setName(name);
        bean.setCrop(crop);
        bean.setProduct(product);
        bean.setPlanAtpId(plan_atp_id);
        bean.setDataSource(dataSource);
        bean.setProduct1(product1);
        bean.setProduct2(product2);
        bean.setState(state);
        bean.setDistrict(district);
        bean.setTehsil(tehsil);
        bean.setVillage(village);
        bean.setExpectedFarmer(expected_farmer);
        bean.setExpectedPda(expected_pda);
        bean.setPlanDate(plan_date);
        return bean;
    }


    public static UserTeamModel getUserTeamModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(UserTeamTable.Cols.ID));
        String username = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.USERNAME));
        String profile_pic = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.PROFILE_PIC));
        String email = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.EMAIL));
        String phone = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.PHONE));
        String designation_code = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.DESIGNATION_CODE));
        String designation_name = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.DESIGNATION_NAME));
        int is_on_deputation = cursor.getInt(cursor.getColumnIndex(UserTeamTable.Cols.IS_ON_DEPUTATION));
        String parent_id = cursor.getString(cursor.getColumnIndex(UserTeamTable.Cols.PARENT_ID));
        return new UserTeamModel(id, username, profile_pic, email, phone, designation_code, designation_name, is_on_deputation, parent_id);
    }

    public static PlanAtpDBModel getPlanAtpModelFromCursor(Cursor cursor) {
        int id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.ID));
        int product_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.PRODUCT_ID));
        int crop_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.CROP_ID));
        String crop_one = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.CROP_ONE));
        String crop_two = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.CROP_TWO));
        int data_source_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.DATA_SOURCE_ID));
        int state_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.STATE_ID));
        int district_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.DISTRICT_ID));
        int tehsil_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.TEHSIL_ID));
        int expected_farmer = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.EXPECTED_FARMER));
        int isUploaded = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.ISUPLOADED));
        int languageid = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.LANGUAGE_ID));
        int expected_pda = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.EXPECTED_PDA));
        String village = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.VILLAGE));
        String record_id = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.RECORD_ID));
        String date = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.DATE));
        String PRODUCT_NAME = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.PRODUCT_NAME));
        String CROP_NAME = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.CROP_NAME));
        String TEHSIL_NAME = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.TEHSIL_NAME));
        String DISTRICT_NAME = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.DISTRICT_NAME));
        String STATE_NAME = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.STATE_NAME));
        String DATA_SOURCE = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.DATA_SOURCE));
        String territory = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.TERRITORY_NAME));
        int territory_id = cursor.getInt(cursor.getColumnIndex(PlanAtpTable.Cols.TERRITORY_ID));
        String DDID = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.DD_ID));
        String ISFL = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.IS_FL));
        String monitorID = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.MONITOR_ID));
        String presenterID = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.PRESENTER_ID));
        String monitorUserType = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.MONITOR_USER_TYPE));
        String presenterUserType = cursor.getString(cursor.getColumnIndex(PlanAtpTable.Cols.PRESENTER_USER_TYPE));

        return new PlanAtpDBModel(id,
                product_id,
                crop_id,
                data_source_id,
                state_id, district_id,
                tehsil_id, expected_farmer,
                isUploaded,
                expected_pda,
                languageid,
                village,
                record_id,
                date,
                crop_one,
                crop_two,
                "",
                PRODUCT_NAME,
                CROP_NAME,
                TEHSIL_NAME,
                DISTRICT_NAME,
                STATE_NAME,
                DATA_SOURCE,
                DDID,
                ISFL,
                presenterID,
                presenterUserType,
                monitorID,
                monitorUserType,
                territory,
                territory_id);
    }
}
