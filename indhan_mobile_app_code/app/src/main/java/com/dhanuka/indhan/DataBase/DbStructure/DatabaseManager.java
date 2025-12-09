package com.dhanuka.indhan.DataBase.DbStructure;

/**
 * Created by John on 5/3/2018.
 */

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.util.Log;

import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean;
import com.dhanuka.indhan.Activity.NewTicketMVP.TehsilMVP.Model.TehsilBean;
import com.dhanuka.indhan.DataBase.TableDataModel.CropModel;
import com.dhanuka.indhan.DataBase.TableDataModel.DistrictDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel;
import com.dhanuka.indhan.DataBase.TableDataModel.MySyllabusDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDetail;
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpList;
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse;
import com.dhanuka.indhan.DataBase.TableDataModel.TehsilDBModel;
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
import com.dhanuka.indhan.DataBase.TableStructureModels.SyllabusTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.TehsilTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable;
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel;
import com.dhanuka.indhan.Model.LanguageModel;
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct;
import com.dhanuka.indhan.util.Utility;

import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;

public class DatabaseManager {

    /**
     * Delete Queries
     */

    public static void deleteAllMenuData(Context context) {
        context.getContentResolver().delete(MenuTable.CONTENT_URI, null, null);
    }

    public static void deleteAllLanguageData(Context context) {
        context.getContentResolver().delete(LanguageTable.CONTENT_URI, null, null);
    }

    public static void deleteProductCropList(Context context) {
        context.getContentResolver().delete(ProductCropTable.CONTENT_URI, null, null);
    }

    public static void deleteAllImageOfflineTable(Context context) {
        context.getContentResolver().delete(ImageOfflineTable.CONTENT_URI, null, null);
    }

    public static void deleteAllImageOfflineTableParticular(Context context, int ticketId) {
        String condition = ImageOfflineTable.Cols.TICKET_ID + "= ?";
        context.getContentResolver().delete(ImageOfflineTable.CONTENT_URI, condition, new String[]{String.valueOf(ticketId)});
    }

    public static void deleteAllbdoListData(Context context) {
        context.getContentResolver().delete(BDOPlanS1Table.CONTENT_URI, null, null);
    }

    public static void deleteAllDistrictData(Context context, int stateId) {
        String condition = DistrictTable.Cols.STATE_ID + "= ?";
        context.getContentResolver().delete(DistrictTable.CONTENT_URI, condition, new String[]{String.valueOf(stateId)});
    }

    public static void deletePlanAtp(Context context, int stateId) {
        String condition = PlanAtpTable.Cols.ID + "= ?";
        context.getContentResolver().delete(PlanAtpTable.CONTENT_URI, condition, new String[]{String.valueOf(stateId)});
    }

    public static void deleteAllbdoDetailDataofBDOID(Context context, int bdoID) {
        String condition = BDOPlanS1DetailTable.Cols.ID + "= ?";
        context.getContentResolver().delete(BDOPlanS1DetailTable.CONTENT_URI, condition, new String[]{String.valueOf(bdoID)});
    }

    public static void deleteAllTehsilData(Context context, int districtId) {
        String condition = TehsilTable.Cols.DISTRICT_ID + "= ?";
        context.getContentResolver().delete(TehsilTable.CONTENT_URI, condition, new String[]{String.valueOf(districtId)});
    }

    public static void deleteAllDataSourceData(Context context) {
        context.getContentResolver().delete(DataSourceTable.CONTENT_URI, null, null);
    }

    public static void deleteAllStateData(Context context) {
        context.getContentResolver().delete(StateTable.CONTENT_URI, null, null);
    }


    public static void deleteAllPlanAtp(Context context) {
        context.getContentResolver().delete(PlanAtpTable.CONTENT_URI, null, null);
    }

    public static void deleteAllMySyllabusData(Context context) {
        context.getContentResolver().delete(MySyllabusForBDOTable.CONTENT_URI, null, null);
    }

    public static void deleteAllCropData(Context context) {
        context.getContentResolver().delete(CropTable.CONTENT_URI, null, null);
    }

    public static void deleteAllProductData(Context context) {
        context.getContentResolver().delete(ProductTable.CONTENT_URI, null, null);
    }

    public static void deleteAllProductCropData(Context context) {
        context.getContentResolver().delete(ProductCropTable.CONTENT_URI, null, null);
    }

    public static void deleteAllBDOData(Context context) {
        context.getContentResolver().delete(UserTeamTable.CONTENT_URI, null, null);
    }

    public static void deleteSyllabusofId(Context context, SyllabusProduct id) {
        context.getContentResolver().delete(SyllabusTable.CONTENT_URI, SyllabusTable.Cols.ID + " = ?", new String[]{String.valueOf(id.getId())});
    }

    /**
     * Save and Update Queries
     */
    public static void saveMenuData(Context context, MenuDBModel menuDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(MenuTable.Cols.ID, Integer.parseInt(menuDBModel.getId()));
        cvMenu.put(MenuTable.Cols.NAME, menuDBModel.getName());
        cvMenu.put(MenuTable.Cols.NOTIFICATION_COUNT, menuDBModel.getNotification_count());
        cvMenu.put(MenuTable.Cols.TARGET, menuDBModel.getTarget());
        cvMenu.put(MenuTable.Cols.IMAGE_URL, menuDBModel.getImage_url());
        cvMenu.put(MenuTable.Cols.MENU_SHOW, menuDBModel.getMenu_show());
        //context.getContentResolver().insert(MenuTable.CONTENT_URI, cvMenu);
        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(MenuTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveImageOfflineTable(Context context, ImageOfflineModel menuDBModel, int value) {
        Log.d("saveImageOfflineTable", "Saved");
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(ImageOfflineTable.Cols.IMAGE_URL_LOCAL, menuDBModel.getImage_url_local());
        cvMenu.put(ImageOfflineTable.Cols.LATTITUDE, menuDBModel.getLattitude());
        cvMenu.put(ImageOfflineTable.Cols.LONGITUDE, menuDBModel.getLongitude());
        cvMenu.put(ImageOfflineTable.Cols.TICKET_ID, menuDBModel.getTicket_id());
        cvMenu.put(ImageOfflineTable.Cols.IS_UPLOADED, menuDBModel.getIsUploaded());
        cvMenu.put(ImageOfflineTable.Cols.TYPE, menuDBModel.getType());
        cvMenu.put(ImageOfflineTable.Cols.TICKET_OFFLINE, value);
        cvMenu.put(ImageOfflineTable.Cols.DESCRIPTION, menuDBModel.getDescription());
        cvMenu.put(ImageOfflineTable.Cols.DATE, menuDBModel.getDate());
        String condition = ImageOfflineTable.Cols.TICKET_ID + " = ? AND " + ImageOfflineTable.Cols.IS_UPLOADED + " = ?";

        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(ImageOfflineTable.CONTENT_URI, null, condition, new String[]{String.valueOf(menuDBModel.getTicket_id()), String.valueOf(menuDBModel.getIsUploaded())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(ImageOfflineTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(menuDBModel.getTicket_id()), String.valueOf(menuDBModel.getIsUploaded())});
            } else {
                context.getContentResolver().insert(ImageOfflineTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
            Log.d("Exception", e.toString());
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void updateImageOfflineTable(Context context, ImageOfflineModel menuDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(ImageOfflineTable.Cols.IS_UPLOADED, menuDBModel.getIsUploaded());
        String condition = ImageOfflineTable.Cols.ID + " = ? ";

        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(ImageOfflineTable.CONTENT_URI, null, condition, new String[]{String.valueOf(menuDBModel.getId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(ImageOfflineTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(menuDBModel.getId())});
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static ArrayList<ImageOfflineModel> getNonUploadedImageOfflineTable(Context context) {
        ArrayList<ImageOfflineModel> list = new ArrayList<>();
        Cursor cursor = null;
        String condition = ImageOfflineTable.Cols.IS_UPLOADED + " = ?";
        try {
            cursor = context.getContentResolver().query(ImageOfflineTable.CONTENT_URI, null, condition, new String[]{String.valueOf(0)}, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getImageOfflineModelFromCursor(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ImageOfflineModel getNonUploadedImageOfflineTable(Context context, String ticketID) {
        ArrayList<ImageOfflineModel> list = new ArrayList<>();
        ImageOfflineModel model = null;
        Cursor cursor = null;
        String condition = ImageOfflineTable.Cols.TICKET_ID + " = ? AND " + ImageOfflineTable.Cols.IS_UPLOADED + " = ?";
        try {
            cursor = context.getContentResolver().query(ImageOfflineTable.CONTENT_URI, null, condition, new String[]{ticketID, String.valueOf(0)}, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    model = CursorModelEvaluater.getImageOfflineModelFromCursor(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return model;
    }

    public static void saveCropData(Context context, CropModel cropDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(CropTable.Cols.ID, cropDBModel.getId());
        cvMenu.put(CropTable.Cols.CROP_NAME, cropDBModel.getCropName());
        cvMenu.put(CropTable.Cols.PRODUCT_ID, cropDBModel.getPid());
        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(CropTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveProductData(Context context, ProductBean productDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(ProductTable.Cols.ID, productDBModel.getpID());
        cvMenu.put(ProductTable.Cols.PRODUCT_NAME, productDBModel.getItem());

        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(ProductTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveDataSource(Context context, DataSourceBean datasourcetable) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(DataSourceTable.Cols.ID, datasourcetable.getDsID());
        cvMenu.put(DataSourceTable.Cols.Name, datasourcetable.getItem());
        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(DataSourceTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveTehsilData(Context context, TehsilDBModel datasourcetable) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(TehsilTable.Cols.ID, datasourcetable.getId());
        cvMenu.put(TehsilTable.Cols.DISTRICT_ID, datasourcetable.getDid());
        cvMenu.put(TehsilTable.Cols.TEHSIL_NAME, datasourcetable.getTehsilName());

        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(TehsilTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveDistrictData(Context context, DistrictDBModel datasourcetable) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(DistrictTable.Cols.ID, datasourcetable.getId());
        cvMenu.put(DistrictTable.Cols.STATE_ID, datasourcetable.getStateId());
        cvMenu.put(DistrictTable.Cols.DISTRICT_NAME, datasourcetable.getDistrictName());

        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(DistrictTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveState(Context context, StateBean stateModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(StateTable.Cols.ID, stateModel.getsID());
        cvMenu.put(StateTable.Cols.STATE_NAME, stateModel.getItem());

        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(StateTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void savePlanAtp(Context context, PlanAtpDBModel model) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(PlanAtpTable.Cols.PRODUCT_ID, model.getProduct_id());
        cvMenu.put(PlanAtpTable.Cols.CROP_ID, model.getCrop_id());
        cvMenu.put(PlanAtpTable.Cols.LANGUAGE_ID, model.getLanguageId());
        cvMenu.put(PlanAtpTable.Cols.CROP_ONE, model.getCrop_one());
        cvMenu.put(PlanAtpTable.Cols.CROP_TWO, model.getCrop_two());
        cvMenu.put(PlanAtpTable.Cols.DATA_SOURCE_ID, model.getData_source_id());
        cvMenu.put(PlanAtpTable.Cols.STATE_ID, model.getState_id());
        cvMenu.put(PlanAtpTable.Cols.DISTRICT_ID, model.getDistrict_id());
        cvMenu.put(PlanAtpTable.Cols.TEHSIL_ID, model.getTehsil_id());
        cvMenu.put(PlanAtpTable.Cols.EXPECTED_FARMER, model.getExpected_farmer());
        cvMenu.put(PlanAtpTable.Cols.ISUPLOADED, model.getIsUploaded());
        cvMenu.put(PlanAtpTable.Cols.VILLAGE, model.getVillage());
        cvMenu.put(PlanAtpTable.Cols.EXPECTED_PDA, model.getExpected_pda());
        cvMenu.put(PlanAtpTable.Cols.RECORD_ID, model.getRecord_id());
        cvMenu.put(PlanAtpTable.Cols.PRODUCT_NAME, model.getProduct());
        cvMenu.put(PlanAtpTable.Cols.STATE_NAME, model.getState());
        cvMenu.put(PlanAtpTable.Cols.CROP_NAME, model.getCrop());
        cvMenu.put(PlanAtpTable.Cols.DISTRICT_NAME, model.getDistrict());
        cvMenu.put(PlanAtpTable.Cols.TERRITORY_NAME, model.getTerritory());
        cvMenu.put(PlanAtpTable.Cols.TERRITORY_ID, model.getTerritory_id());
        cvMenu.put(PlanAtpTable.Cols.TEHSIL_NAME, model.getTehsil());
        cvMenu.put(PlanAtpTable.Cols.DATE, model.getDate());
        cvMenu.put(PlanAtpTable.Cols.DATA_SOURCE, model.getDATA_SOURCE());
        cvMenu.put(PlanAtpTable.Cols.DD_ID, model.getDD_ID());
        cvMenu.put(PlanAtpTable.Cols.IS_FL, model.getIS_FL());
        cvMenu.put(PlanAtpTable.Cols.PRESENTER_ID, model.getPresenter_id());
        cvMenu.put(PlanAtpTable.Cols.PRESENTER_USER_TYPE, model.getPresenter_type());
        cvMenu.put(PlanAtpTable.Cols.MONITOR_ID, model.getMonitor_id());
        cvMenu.put(PlanAtpTable.Cols.MONITOR_USER_TYPE, model.getMonitor_type());

        Cursor cursor = null;
        try {
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(PlanAtpTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveSyllabus(Context context, SyllabusProduct syllabusDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(SyllabusTable.Cols.BDO_ID, syllabusDBModel.getBdo_id());
        cvMenu.put(SyllabusTable.Cols.MONTH, syllabusDBModel.getMonth());
        cvMenu.put(SyllabusTable.Cols.PRODUCT_ID, syllabusDBModel.getProduct_id());
        cvMenu.put(SyllabusTable.Cols.CROP_ID, syllabusDBModel.getCrop_id());
        cvMenu.put(SyllabusTable.Cols.NO_OF_DAYS, syllabusDBModel.getNo_of_days());
        cvMenu.put(SyllabusTable.Cols.NO_OF_DD, syllabusDBModel.getNo_of_dd());
        cvMenu.put(SyllabusTable.Cols.TIMESTAMP, syllabusDBModel.getTimestamp()/*String.valueOf(System.currentTimeMillis())*/);
        cvMenu.put(SyllabusTable.Cols.RECORD_ID, syllabusDBModel.getRecord_id());
        cvMenu.put(SyllabusTable.Cols.ISUPLOADED, syllabusDBModel.getIsUploaded());
        cvMenu.put(SyllabusTable.Cols.PRODUCT_NAME, syllabusDBModel.getProduct_name());
        cvMenu.put(SyllabusTable.Cols.CROP_NAME, syllabusDBModel.getCrops_name());
        cvMenu.put(SyllabusTable.Cols.BDO_NAME, syllabusDBModel.getBdo_name());
        Cursor cursor = null;

        String condition = SyllabusTable.Cols.BDO_ID + "= ? AND " + SyllabusTable.Cols.PRODUCT_ID + " = ? AND " + SyllabusTable.Cols.CROP_ID + " = ? AND " + SyllabusTable.Cols.MONTH + " = ? AND " + SyllabusTable.Cols.ISUPLOADED + " = ?" + " = ? AND " + SyllabusTable.Cols.TIMESTAMP + " = ?";
        String condition2 = SyllabusTable.Cols.BDO_ID + "= ? AND " + SyllabusTable.Cols.PRODUCT_ID + " = ? AND " + SyllabusTable.Cols.CROP_ID + " = ? AND " + SyllabusTable.Cols.MONTH + " = ? ";

        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(syllabusDBModel.getBdo_id()), String.valueOf(syllabusDBModel.getProduct_id()), String.valueOf(syllabusDBModel.getCrop_id()), syllabusDBModel.getMonth(), String.valueOf(syllabusDBModel.getIsUploaded()), String.valueOf(syllabusDBModel.getTimestamp())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(SyllabusTable.CONTENT_URI, cvMenu, condition2, new String[]{String.valueOf(syllabusDBModel.getBdo_id()), String.valueOf(syllabusDBModel.getProduct_id()), String.valueOf(syllabusDBModel.getCrop_id()), syllabusDBModel.getMonth()});
            } else {
                context.getContentResolver().insert(SyllabusTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveUserTeamTableData(Context context, UserTeamModel model) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(UserTeamTable.Cols.ID, model.getID());
        cvMenu.put(UserTeamTable.Cols.USERNAME, model.getUSERNAME());
        cvMenu.put(UserTeamTable.Cols.EMAIL, model.getEMAIL());
        cvMenu.put(UserTeamTable.Cols.IS_ON_DEPUTATION, model.getIS_ON_DEPUTATION());
        cvMenu.put(UserTeamTable.Cols.PARENT_ID, model.getPARENT_ID());
        cvMenu.put(UserTeamTable.Cols.PHONE, model.getPHONE());
        cvMenu.put(UserTeamTable.Cols.PROFILE_PIC, model.getPROFILE_PIC());
        cvMenu.put(UserTeamTable.Cols.DESIGNATION_CODE, model.getDESIGNATION_CODE());
        cvMenu.put(UserTeamTable.Cols.DESIGNATION_NAME, model.getDESIGNATION_NAME());
        String condition = UserTeamTable.Cols.ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(UserTeamTable.CONTENT_URI, null, condition, new String[]{String.valueOf(model.getID())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(UserTeamTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(model.getID())});
            } else {
                context.getContentResolver().insert(UserTeamTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static boolean checkAndUpdateSyllabusDatabase(Context context, SyllabusProduct syllabusDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(SyllabusTable.Cols.BDO_ID, syllabusDBModel.getBdo_id());
        cvMenu.put(SyllabusTable.Cols.MONTH, syllabusDBModel.getMonth());
        cvMenu.put(SyllabusTable.Cols.PRODUCT_ID, syllabusDBModel.getProduct_id());
        cvMenu.put(SyllabusTable.Cols.CROP_ID, syllabusDBModel.getCrop_id());
        cvMenu.put(SyllabusTable.Cols.NO_OF_DAYS, syllabusDBModel.getNo_of_days());
        cvMenu.put(SyllabusTable.Cols.NO_OF_DD, syllabusDBModel.getNo_of_dd());
        cvMenu.put(SyllabusTable.Cols.TIMESTAMP, syllabusDBModel.getTimestamp());
        cvMenu.put(SyllabusTable.Cols.ISUPLOADED, syllabusDBModel.getIsUploaded());
        cvMenu.put(SyllabusTable.Cols.RECORD_ID, syllabusDBModel.getRecord_id());
        cvMenu.put(SyllabusTable.Cols.PRODUCT_NAME, syllabusDBModel.getProduct_name());
        cvMenu.put(SyllabusTable.Cols.CROP_NAME, syllabusDBModel.getCrops_name());
        cvMenu.put(SyllabusTable.Cols.BDO_NAME, syllabusDBModel.getBdo_name());
        String condition = SyllabusTable.Cols.ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(syllabusDBModel.getId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(SyllabusTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(syllabusDBModel.getId())});
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return true;
    }

    public static boolean updateUploadStatusinDB(Context context, SyllabusProduct syllabusDBModel) {
        ContentValues cvMenu = new ContentValues();

        cvMenu.put(SyllabusTable.Cols.ISUPLOADED, syllabusDBModel.getIsUploaded());

        String condition = SyllabusTable.Cols.RECORD_ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(syllabusDBModel.getRecord_id())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(SyllabusTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(syllabusDBModel.getRecord_id())});
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return true;
    }

    public static boolean updateUploadinATPTable(Context context, PlanAtpDBModel syllabusDBModel) {
        ContentValues cvMenu = new ContentValues();

        cvMenu.put(PlanAtpTable.Cols.ISUPLOADED, syllabusDBModel.getIsUploaded());

        String condition = PlanAtpTable.Cols.RECORD_ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(PlanAtpTable.CONTENT_URI, null, condition, new String[]{String.valueOf(syllabusDBModel.getRecord_id())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(PlanAtpTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(syllabusDBModel.getRecord_id())});
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
        return true;
    }

    public static ArrayList<MenuDBModel> getAllMenuList(Context context) {
        ArrayList<MenuDBModel> list = new ArrayList<>();
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(MenuTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getMenuModelFromCursor(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }

        return list;
    }

    public static ArrayList<UserTeamModel> getAllUserTeamDataList(Context context) {
        ArrayList<UserTeamModel> list = new ArrayList<>();
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(UserTeamTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getUserTeamModelFromCursor(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }

        return list;
    }

    public static ArrayList<PlanAtpDBModel> getAllPlanATPList(Context context, int isUploaded) {
        ArrayList<PlanAtpDBModel> list = new ArrayList<>();
        Cursor cursor = null;
        String condition = PlanAtpTable.Cols.ISUPLOADED + "= ?";
        PlanAtpDBModel bean;
        try {
            cursor = context.getContentResolver().query(PlanAtpTable.CONTENT_URI, null, condition, new String[]{String.valueOf(isUploaded)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getPlanAtpModelFromCursor(cursor);
                    boolean value = true;
                    if (value)
                        list.add(bean);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<Date> getAllTimePlanList(Context context) {
        ArrayList<PlanAtpDBModel> list = new ArrayList<>();
        ArrayList<Date> time_list = new ArrayList<>();
        Cursor cursor = null;
        PlanAtpDBModel bean;
        try {
            cursor = context.getContentResolver().query(PlanAtpTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getPlanAtpModelFromCursor(cursor);
                    if (!time_list.contains(bean.getDate_obj()))
                        time_list.add(bean.getDate_obj());
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return time_list;
    }

    public static ArrayList<ProductBean> getProductDataFromDB(Context context) {
        ArrayList<ProductBean> list = new ArrayList<>();
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(ProductTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getProductModelFromProductTableCursor(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }

        return list;
    }

    public static ArrayList<CropBean> getCropDataFromDB(Context context) {
        ArrayList<CropBean> list = new ArrayList<>();
        //   String condition = CropTable.Cols.PRODUCT_ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(CropTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getCropModelFromCursor(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<MenuDBModel> getMenuData() {
        ArrayList<MenuDBModel> menu = new ArrayList<>();
        menu.add(new MenuDBModel("1", "Plan Syllabus", "plansyllabus", null, 2, 1));
        menu.add(new MenuDBModel("2", "Syllabus History", "plansyllabus", null, 2, 1));
        menu.add(new MenuDBModel("3", "Self Participation", "plansyllabus", null, 2, 1));
        menu.add(new MenuDBModel("4", "Reports", "reports", null, 2, 1));
        menu.add(new MenuDBModel("5", "BDO Plan", "bdoplan", null, 4, 1));
        menu.add(new MenuDBModel("6", "Farmer Registration", "farmerregistration", null, 2, 1));
        menu.add(new MenuDBModel("7", "New Ticket", "newticket", null, 8, 1));
        menu.add(new MenuDBModel("8", "Customer Care", "customercare", null, 2, 1));
        menu.add(new MenuDBModel("9", "Testimonials", "testimonials", null, 3, 1));
        menu.add(new MenuDBModel("10", "DROPBOXURL", "dropbox_url", null, 3, 1));

        return menu;
    }

    public static ProductBean getProductOfId(Context context, int productId) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = ProductCropTable.Cols.PRODUCT_ID + "= ?";
        Cursor cursor = null;
        ProductBean bean = null;
        try {
            cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, null, condition, new String[]{String.valueOf(productId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getProductModelFromProductTableCursor(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<DistrictBean> getDistrictData(Context context, int stateId) {
        ArrayList<DistrictBean> list = new ArrayList<>();
        String condition = DistrictTable.Cols.STATE_ID + "= ?";
        Cursor cursor = null;
        DistrictBean bean = null;
        try {
            cursor = context.getContentResolver().query(DistrictTable.CONTENT_URI, null, condition, new String[]{String.valueOf(stateId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getDistrictFromCursor(cursor);
                    list.add(bean);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static DistrictBean getDistrictDataofId(Context context, int districtId) {
        ArrayList<DistrictBean> list = new ArrayList<>();
        String condition = DistrictTable.Cols.ID + "= ?";
        Cursor cursor = null;
        DistrictBean bean = null;
        try {
            cursor = context.getContentResolver().query(DistrictTable.CONTENT_URI, null, condition, new String[]{String.valueOf(districtId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getDistrictFromCursor(cursor);

                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<TehsilBean> getTehsilData(Context context, int districtId) {
        ArrayList<TehsilBean> list = new ArrayList<>();
        String condition = TehsilTable.Cols.DISTRICT_ID + "= ?";
        Cursor cursor = null;
        TehsilBean bean = null;
        try {
            cursor = context.getContentResolver().query(TehsilTable.CONTENT_URI, null, condition, new String[]{String.valueOf(districtId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getTehsilFromCursor(cursor);
                    list.add(bean);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static TehsilBean getTehsilDataOfID(Context context, int tehsilId) {
        ArrayList<TehsilBean> list = new ArrayList<>();
        String condition = TehsilTable.Cols.ID + "= ?";
        Cursor cursor = null;
        TehsilBean bean = null;
        try {
            cursor = context.getContentResolver().query(TehsilTable.CONTENT_URI, null, condition, new String[]{String.valueOf(tehsilId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getTehsilFromCursor(cursor);

                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static CropBean getCropOfId(Context context, int cropID) {
        String condition = ProductCropTable.Cols.CROP_ID + "= ?";
        Cursor cursor = null;
        CropBean bean = null;
        try {
            cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, null, condition, new String[]{String.valueOf(cropID)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getCropModelFromProductCropCursor(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static UserTeamModel getBdoNameOfId(Context context, int bdoID) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = UserTeamTable.Cols.ID + "= ?";
        Cursor cursor = null;
        UserTeamModel bean = null;
        try {
            cursor = context.getContentResolver().query(UserTeamTable.CONTENT_URI, null, condition, new String[]{String.valueOf(bdoID)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getUserTeamModelFromCursor(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<SyllabusProduct> getAllSyllabusListWhereBdoID(Context context, int bdoID, String _month, int isUploaded) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = SyllabusTable.Cols.BDO_ID + "= ? AND " + SyllabusTable.Cols.MONTH + "= ? AND " + SyllabusTable.Cols.ISUPLOADED + " = ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(bdoID), _month, String.valueOf(isUploaded)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    int id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ID));
                    int bdo_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.BDO_ID));
                    int product_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_ID));
                    int crop_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.CROP_ID));
                    int no_of_days = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DAYS));
                    int no_of_dd = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DD));
                    int isuploaded = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ISUPLOADED));
                    String record_id = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.RECORD_ID));
                    String timeStamp = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.TIMESTAMP));
                    String month = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.MONTH));
                    UserTeamModel bdoName = getBdoNameOfId(context, bdo_id);
                    String productName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_NAME));
                    String cropName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.CROP_NAME));
                    SyllabusProduct model = new SyllabusProduct();
                    model.setId(id);
                    model.setBdo_id(bdo_id);
                    model.setProduct_id(product_id);
                    model.setCrop_id(crop_id);
                    model.setNo_of_days(no_of_days);
                    model.setNo_of_dd(no_of_dd);
                    model.setMonth(month);
                    model.setTimestamp(timeStamp);
                    model.setBdo_name(bdoName.getUSERNAME());
                    model.setProduct_name(productName);
                    model.setCrops_name(cropName);
                    model.setIsUploaded(isuploaded);
                    model.setRecord_id(record_id);
                    list.add(model);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<SyllabusProduct> getAllSyllabusListWhereProductID(Context context, int prod_id, String _month, int isUploaded) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = SyllabusTable.Cols.PRODUCT_ID + "= ? AND " + SyllabusTable.Cols.MONTH + "= ? AND " + SyllabusTable.Cols.ISUPLOADED + " = ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(prod_id), _month, String.valueOf(isUploaded)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    int id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ID));
                    int bdo_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.BDO_ID));
                    int product_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_ID));
                    int crop_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.CROP_ID));
                    int no_of_days = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DAYS));
                    int no_of_dd = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DD));
                    String timeStamp = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.TIMESTAMP));
                    String month = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.MONTH));
                    int isuploaded = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ISUPLOADED));
                    String record_id = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.RECORD_ID));
                    UserTeamModel bdoName = getBdoNameOfId(context, bdo_id);
                    String productName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_NAME));
                    String cropName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.CROP_NAME));
                    SyllabusProduct model = new SyllabusProduct();
                    model.setId(id);
                    model.setBdo_id(bdo_id);
                    model.setProduct_id(product_id);
                    model.setCrop_id(crop_id);
                    model.setNo_of_days(no_of_days);
                    model.setNo_of_dd(no_of_dd);
                    model.setMonth(month);
                    model.setTimestamp(timeStamp);
                    model.setBdo_name(bdoName.getUSERNAME());
                    model.setProduct_name(productName);
                    model.setCrops_name(cropName);
                    model.setIsUploaded(isuploaded);
                    model.setRecord_id(record_id);
                    list.add(model);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<SyllabusProduct> getAllNonSyncedSyllabus(Context context) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = SyllabusTable.Cols.ISUPLOADED + "= ? ";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(0)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    int id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ID));
                    int bdo_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.BDO_ID));
                    int product_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_ID));
                    int crop_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.CROP_ID));
                    int no_of_days = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DAYS));
                    int no_of_dd = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DD));
                    String timeStamp = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.TIMESTAMP));
                    String month = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.MONTH));
                    int isuploaded = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ISUPLOADED));
                    String record_id = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.RECORD_ID));
                    UserTeamModel bdoName = getBdoNameOfId(context, bdo_id);
                    String productName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_NAME));
                    String cropName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.CROP_NAME));
                    SyllabusProduct model = new SyllabusProduct();
                    model.setId(id);
                    model.setBdo_id(bdo_id);
                    model.setProduct_id(product_id);
                    model.setCrop_id(crop_id);
                    model.setNo_of_days(no_of_days);
                    model.setNo_of_dd(no_of_dd);
                    model.setMonth(month);
                    model.setTimestamp(timeStamp);
                    model.setBdo_name(bdoName.getUSERNAME());
                    model.setProduct_name(productName);
                    model.setCrops_name(cropName);
                    model.setIsUploaded(isuploaded);
                    model.setRecord_id(record_id);
                    list.add(model);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<SyllabusProduct> getSyllabusFromCropAndProduct(Context context, int crop_Id, int prod_id, String _month, int isUploaded) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = SyllabusTable.Cols.PRODUCT_ID + "= ? AND " + SyllabusTable.Cols.MONTH + "= ? AND " + SyllabusTable.Cols.CROP_ID + "= ? AND " + SyllabusTable.Cols.ISUPLOADED + " =?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(prod_id), _month, String.valueOf(crop_Id), String.valueOf(isUploaded)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    int id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ID));
                    int bdo_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.BDO_ID));
                    int product_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_ID));
                    int crop_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.CROP_ID));
                    int no_of_days = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DAYS));
                    int no_of_dd = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DD));
                    String timeStamp = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.TIMESTAMP));
                    String month = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.MONTH));
                    int isuploaded = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ISUPLOADED));

                    String productName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_NAME));
                    String cropName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.CROP_NAME));
                    //String bdoName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.BDO_NAME));
                    UserTeamModel bdoName = getBdoNameOfId(context, bdo_id);
                    String record_id = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.RECORD_ID));
                   /* ProductBean productBean = getProductOfId(context, product_id);
                    CropBean cropBean = getCropOfId(context, crop_id);
             */
                    SyllabusProduct model = new SyllabusProduct();
                    model.setId(id);
                    model.setBdo_id(bdo_id);
                    model.setProduct_id(product_id);
                    model.setCrop_id(crop_id);
                    model.setNo_of_days(no_of_days);
                    model.setNo_of_dd(no_of_dd);
                    model.setMonth(month);
                    model.setTimestamp(timeStamp);
                    model.setBdo_name(bdoName.getUSERNAME());
                    model.setProduct_name(productName);
                    model.setCrops_name(cropName);
              /*      if (productBean == null) {
                        model.setProduct_name("not available");
                    } else {
                        model.setProduct_name(productBean.getItem());
                    }*/
/*
                    if (cropBean == null) {
                        model.setProduct_name("not available");
                    } else {
                        model.setCrops_name(cropBean.getItem());
                    }*/
                    model.setIsUploaded(isuploaded);
                    model.setRecord_id(record_id);
                    list.add(model);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }


    public static int getNoOfDaysOfBDO(Context context, int bdoID, String month, int isUploaded) {
        ArrayList<SyllabusProduct> list = getAllSyllabusListWhereBdoID(context, bdoID, month, isUploaded);
        int noOfDays = 0;
        for (SyllabusProduct iterate : list) {
            noOfDays += iterate.getNo_of_days();
        }
        return noOfDays;
    }

    public static HashMap<String, Integer> getNoOfBDOandNoOfBDOFromSyllabus(Context context, int syllabus_Prod_id, String month, int isUploaded) {
        ArrayList<SyllabusProduct> list = getAllSyllabusListWhereProductID(context, syllabus_Prod_id, month, isUploaded);
        HashMap<String, Integer> returnMap = new HashMap<>();
        int noOfDays = 0;
        returnMap.put("bdo_no", list.size());
        for (SyllabusProduct iterate : list) {
            noOfDays += iterate.getNo_of_days();
        }
        returnMap.put("no_of_days", noOfDays);
        return returnMap;
    }

    public static int getDaysofProdnCropFromATP(Context ctx, int cropId, int prodId) {
        MySyllabusDBModel model = getSyllabusOfCropandProd(ctx, prodId, cropId);
        // PlanAtpDBModel model1 = getPlannedATPListOnBasisOfCropAndProd(ctx, 0, prodId, cropId);
        //  int remaindays=model.getNo_of_days()=model1.gete;


        return 0;

    }
    /// yaha kam karna

    public static ArrayList<SyllabusProduct> getAllSyllabusList(Context context, int isUploaded) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = SyllabusTable.Cols.ISUPLOADED + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(SyllabusTable.CONTENT_URI, null, condition, new String[]{String.valueOf(isUploaded)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    int id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ID));
                    int bdo_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.BDO_ID));
                    int product_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_ID));
                    int crop_id = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.CROP_ID));
                    int no_of_days = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DAYS));
                    int no_of_dd = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.NO_OF_DD));
                    String timeStamp = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.TIMESTAMP));
                    String month = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.MONTH));
                    int isuploaded = cursor.getInt(cursor.getColumnIndex(SyllabusTable.Cols.ISUPLOADED));
                    String record_id = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.RECORD_ID));
                    String productName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.PRODUCT_NAME));
                    String cropName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.CROP_NAME));
                    //   String bdoName = cursor.getString(cursor.getColumnIndex(SyllabusTable.Cols.BDO_NAME));
                    UserTeamModel bdoName = getBdoNameOfId(context, bdo_id);
                 /*   ProductBean productBean = getProductOfId(context, product_id);
                    CropBean cropBean = getCropOfId(context, crop_id);
               */
                    SyllabusProduct model = new SyllabusProduct();
                    model.setId(id);
                    model.setBdo_id(bdo_id);
                    model.setProduct_id(product_id);
                    model.setCrop_id(crop_id);
                    model.setNo_of_days(no_of_days);
                    model.setNo_of_dd(no_of_dd);
                    model.setMonth(month);
                    model.setTimestamp(timeStamp);
                    model.setBdo_name(bdoName.getUSERNAME());
                    model.setProduct_name(productName);
                    model.setCrops_name(cropName);
            /*        if (bdoName != null)
                        model.setBdo_name(bdoName.getUSERNAME());
                    if (productBean != null)
                        model.setProduct_name(productBean.getItem());
                    else model.setProduct_name("");
                    if (cropBean != null)
                        model.setCrops_name(cropBean.getItem());
                    else model.setCrops_name("");*/

                    model.setIsUploaded(isuploaded);
                    model.setRecord_id(record_id);
                    list.add(model);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }


    public static void saveMySyllabusData(Context context, MySyllabusDBModel mySyllabusDBModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(MySyllabusForBDOTable.Cols.PRODUCT_NAME, mySyllabusDBModel.getProduct_name());
        cvMenu.put(MySyllabusForBDOTable.Cols.PRODUCT_ID, mySyllabusDBModel.getProduct_id());
        cvMenu.put(MySyllabusForBDOTable.Cols.CROP_ID, mySyllabusDBModel.getCrop_id());
        cvMenu.put(MySyllabusForBDOTable.Cols.CROP_NAME, mySyllabusDBModel.getCrop_name());
        cvMenu.put(MySyllabusForBDOTable.Cols.NO_OF_DAYS, mySyllabusDBModel.getNo_of_days());
        cvMenu.put(MySyllabusForBDOTable.Cols.NO_OF_DD, mySyllabusDBModel.getNo_of_dd());
        cvMenu.put(MySyllabusForBDOTable.Cols.ATP, mySyllabusDBModel.getAtp());
        cvMenu.put(MySyllabusForBDOTable.Cols.MONTH, mySyllabusDBModel.getMonth());
        cvMenu.put(MySyllabusForBDOTable.Cols.YEAR, mySyllabusDBModel.getYear());
        Cursor cursor = null;
        try {
            if (cursor != null && cursor.moveToNext()) {
            } else {
                context.getContentResolver().insert(MySyllabusForBDOTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }


    public static ArrayList<MySyllabusDBModel> getAllMySyllabusList(Context context) {
        ArrayList<MySyllabusDBModel> list = new ArrayList<>();
        Cursor cursor = null;
        MySyllabusDBModel bean = null;
        try {
            cursor = context.getContentResolver().query(MySyllabusForBDOTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getMySyllabusModel(cursor);
                    list.add(CursorModelEvaluater.getMySyllabusModel(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static MySyllabusDBModel getAllMySyllabusListonThebasisofCropnProduct(Context context, int prodid, int cropid, int month) {
        ArrayList<MySyllabusDBModel> list = new ArrayList<>();
        String condition = MySyllabusForBDOTable.Cols.PRODUCT_ID + "= ? AND " + MySyllabusForBDOTable.Cols.CROP_ID + "= ? AND " + MySyllabusForBDOTable.Cols.MONTH + "= ?";
        Cursor cursor = null;
        MySyllabusDBModel bean = null;
        try {
            cursor = context.getContentResolver().query(MySyllabusForBDOTable.CONTENT_URI, null, condition, new String[]{String.valueOf(prodid), String.valueOf(cropid), String.valueOf(month)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getMySyllabusModel(cursor);

                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<PlanAtpDBModel> getPlannedATPListOnBasisOfCropAndProd(Context context, int month, int cropID, int prodId) {
        ArrayList<PlanAtpDBModel> list = new ArrayList<>();
        ArrayList<PlanAtpDBModel> list2 = new ArrayList<>();
        Cursor cursor = null;
        String condition = PlanAtpTable.Cols.CROP_ID + " = + ? AND " + PlanAtpTable.Cols.PRODUCT_ID + " = ?";
        PlanAtpDBModel bean = null;
        try {
            cursor = context.getContentResolver().query(PlanAtpTable.CONTENT_URI, null, condition, new String[]{String.valueOf(cropID), String.valueOf(prodId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getPlanAtpModelFromCursor(cursor);

                  /*  ProductBean product = DatabaseManager.getProductOfId(context, bean.getProduct_id());
                    CropBean crop = DatabaseManager.getCropOfId(context, bean.getCrop_id());
                    StateBean state = DatabaseManager.getStateModelOfId(context, bean.getState_id());
                    DistrictBean district = DatabaseManager.getDistrictDataofId(context, bean.getDistrict_id());
                    TehsilBean tehsil = DatabaseManager.getTehsilDataOfID(context, bean.getTehsil_id());
                    DataSourceBean dataSource = DatabaseManager.getDataSourceFromDBofID(context, bean.getData_source_id());
                    bean.setProduct(product.getItem());
                    bean.setCrop(crop.getItem());
                    bean.setState(state.getItem());
                    bean.setDistrict(district.getItem());
                    bean.setTehsil(tehsil.getItem());
                    bean.setData_source(dataSource.getItem());*/
                    list.add(bean);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        for (PlanAtpDBModel model : list) {
            if (month == Utility.getMontfromDate(model.getDate())) {
                list2.add(model);
            }
        }
        return list2;
    }


    public static MySyllabusDBModel getSyllabusOfCropandProd(Context context, int prod, int crop) {
        ArrayList<MySyllabusDBModel> list = new ArrayList<>();
        String condition = MySyllabusForBDOTable.Cols.PRODUCT_ID + "= ? AND " + MySyllabusForBDOTable.Cols.CROP_ID + " = ?";
        MySyllabusDBModel bean = null;
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(MySyllabusForBDOTable.CONTENT_URI, null, condition, new String[]{String.valueOf(prod), String.valueOf(crop)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getMySyllabusModel(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<DataSourceBean> getDataSourceFromDB(Context context) {
        ArrayList<DataSourceBean> list = new ArrayList<>();

        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(DataSourceTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getDataSourcesModel(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static DataSourceBean getDataSourceFromDBofID(Context context, int id) {
        String condition = DataSourceTable.Cols.ID + "= ?";
        DataSourceBean bean = null;
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(DataSourceTable.CONTENT_URI, null, condition, new String[]{String.valueOf(id)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getDataSourcesModel(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<StateBean> getStateModel(Context context) {
        ArrayList<StateBean> list = new ArrayList<>();

        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(StateTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getStateModel(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static StateBean getStateModelOfId(Context context, int stateId) {
        ArrayList<SyllabusProduct> list = new ArrayList<>();
        String condition = StateTable.Cols.ID + "= ?";
        Cursor cursor = null;
        StateBean bean = null;
        try {
            cursor = context.getContentResolver().query(StateTable.CONTENT_URI, null, condition, new String[]{String.valueOf(stateId)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    bean = CursorModelEvaluater.getStateModel(cursor);
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return bean;
    }

    public static ArrayList<PlanAtpList> getBDOPlanList(Context context) {
        ArrayList<PlanAtpList> list = new ArrayList<>();

        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(BDOPlanS1Table.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getBDOPlanS1AtpList(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<PlanAtpDetail> getBDOPlanListDetail(Context context, int bdo_id) {
        ArrayList<PlanAtpDetail> list = new ArrayList<>();
        String condition = BDOPlanS1DetailTable.Cols.ID + "= ?";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(BDOPlanS1DetailTable.CONTENT_URI, null, condition, new String[]{String.valueOf(bdo_id)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getBDOPlanS1AtpListDetail(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static void saveBDOPlanS1DetailData(Context context, PlanAtpDetail model) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(BDOPlanS1DetailTable.Cols.ID, model.getId());
        cvMenu.put(BDOPlanS1DetailTable.Cols.PLAN_ATP_ID, model.getPlanAtpId());
        cvMenu.put(BDOPlanS1DetailTable.Cols.NAME, model.getName());
        cvMenu.put(BDOPlanS1DetailTable.Cols.PRODUCT, model.getProduct());
        cvMenu.put(BDOPlanS1DetailTable.Cols.PRODUCT1, model.getProduct1());
        cvMenu.put(BDOPlanS1DetailTable.Cols.PRODUCT2, model.getProduct2());
        cvMenu.put(BDOPlanS1DetailTable.Cols.DATA_SOURCE, model.getDataSource());
        cvMenu.put(BDOPlanS1DetailTable.Cols.CROP, model.getCrop());
        cvMenu.put(BDOPlanS1DetailTable.Cols.STATE, model.getState());
        cvMenu.put(BDOPlanS1DetailTable.Cols.DISTRICT, model.getDistrict());
        cvMenu.put(BDOPlanS1DetailTable.Cols.TEHSIL, model.getTehsil());
        cvMenu.put(BDOPlanS1DetailTable.Cols.VILLAGE, model.getVillage());
        cvMenu.put(BDOPlanS1DetailTable.Cols.EXPECTED_FARMER, model.getExpectedFarmer());
        cvMenu.put(BDOPlanS1DetailTable.Cols.EXPECTED_PDA, model.getExpectedPda());
        cvMenu.put(BDOPlanS1DetailTable.Cols.PLAN_DATE, model.getPlanDate());
        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(BDOPlanS1DetailTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static void saveBDOPlanS1Data(Context context, PlanAtpList model) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(BDOPlanS1Table.Cols.ID, model.getId());
        cvMenu.put(BDOPlanS1Table.Cols.BDO_NAME, model.getName());
        cvMenu.put(BDOPlanS1Table.Cols.ACTIVITES, model.getActivites());
        cvMenu.put(BDOPlanS1Table.Cols.MONTH, model.getMonth());

        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(BDOPlanS1Table.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public static ArrayList<ProductCropResponse.ProductCropList> getProductCropDetails(Context context) {
        ArrayList<ProductCropResponse.ProductCropList> list = new ArrayList<>();
        Cursor cursor = null;
        //   String[] projection= {"DISTINCT crop_id, id, product_id, product, category, crop"};
        try {
            // context.getContentResolver().query(ProductCropTable.CONTENT_URI, null, condition, new String[]{String.valueOf(model.getCropId()), String.valueOf(model.getProductId())}, null);
            cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, null, null, null, null);
            //  cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, new String[] {"Distinct "+ "crop_id","id","product_id", "product", "category","crop"}, null, null,      "crop_id");
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getProductCropDetails(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<ProductCropResponse.ProductCropList> getProductCropDetailProductNew(Context context) {
        ArrayList<ProductCropResponse.ProductCropList> list = new ArrayList<>();
        Cursor cursor = null;
        //   String[] projection= {"DISTINCT crop_id, id, product_id, product, category, crop"};
        try {
            cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, new String[]{"Distinct " + "crop_id", "id", "product_id", "product", "category", "crop"}, null, null, "crop_id");
            //cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI,null, null, null,      null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getProductCropDetails(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static ArrayList<LanguageModel> getLanguageList(Context context) {
        ArrayList<LanguageModel> list = new ArrayList<>();
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(LanguageTable.CONTENT_URI, null, null, null, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list.add(CursorModelEvaluater.getLanguageListDetails(cursor));
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static LanguageModel getLanguageId(Context context, String languageName) {
        LanguageModel list = new LanguageModel();
        Cursor cursor = null;
        String condition = LanguageTable.Cols.LANGUAGE_NAME + "= ?";
        try {
            cursor = context.getContentResolver().query(LanguageTable.CONTENT_URI, null, condition, new String[]{String.valueOf(languageName)}, null);
            if (cursor != null && cursor.getCount() > 0) {
                while (cursor.moveToNext()) {
                    list = (CursorModelEvaluater.getLanguageListDetails(cursor));
                    break;
                }
            }
        } finally {
            if (cursor != null && !cursor.isClosed()) {
                cursor.close();
            }
        }
        return list;
    }

    public static void saveLanguageData(Context context, LanguageModel languageModel) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(LanguageTable.Cols.ID, languageModel.getId());
        cvMenu.put(LanguageTable.Cols.LANGUAGE_NAME, languageModel.getLanguage());
        Cursor cursor = null;
        try {
            //  cursor = contentResolver.query(UserTable.CONTENT_URI, null, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                //    contentResolver.update(UserTable.CONTENT_URI, cvUser, condition, new String[]{String.valueOf(userLoginModel.getResultBean().getUserId())});
            } else {
                context.getContentResolver().insert(LanguageTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    /**
     * distinct values of crop and product, irrespective of category
     *
     * @param context
     * @param model
     */
    public static void saveProdCrop(Context context, ProductCropResponse.ProductCropList model) {
        ContentValues cvMenu = new ContentValues();
        cvMenu.put(ProductCropTable.Cols.CROP_ID, model.getCropId());
        cvMenu.put(ProductCropTable.Cols.CROP, model.getCrop());
        cvMenu.put(ProductCropTable.Cols.PRODUCT_ID, model.getProductId());
        cvMenu.put(ProductCropTable.Cols.PRODUCT, model.getProduct());
        cvMenu.put(ProductCropTable.Cols.CATEGORY, model.getProductCropCategoryId());
        String condition = ProductCropTable.Cols.CROP_ID + "= ? AND " + ProductCropTable.Cols.PRODUCT_ID + "= ? ";
        Cursor cursor = null;
        try {
            cursor = context.getContentResolver().query(ProductCropTable.CONTENT_URI, null, condition, new String[]{String.valueOf(model.getCropId()), String.valueOf(model.getProductId())}, null);
            if (cursor != null && cursor.moveToNext()) {
                context.getContentResolver().update(ProductCropTable.CONTENT_URI, cvMenu, condition, new String[]{String.valueOf(model.getCropId()), String.valueOf(model.getProductId())});
            } else {
                context.getContentResolver().insert(ProductCropTable.CONTENT_URI, cvMenu);
            }
        } catch (Exception e) {
            e.printStackTrace();
        } finally {
            if (cursor != null) {
                cursor.close();
            }
        }
    }

    public String convertStringToHex(String str) {
        char[] chars = str.toCharArray();
        StringBuilder hex = new StringBuilder();
        for (char aChar : chars) {
            hex.append(Integer.toHexString((int) aChar));
        }
        return hex.toString();
    }
}