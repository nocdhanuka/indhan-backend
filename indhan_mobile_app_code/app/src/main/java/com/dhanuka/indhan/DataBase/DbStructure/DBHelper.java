package com.dhanuka.indhan.DataBase.DbStructure;

/**
 * Created by John on 5/3/2018.
 */


import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.dhanuka.indhan.DataBase.TableStructureModels.ACTIVEATPTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.ATPTable;
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
import com.dhanuka.indhan.DataBase.TableStructureModels.UserPermissionTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTable;
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable;

import java.text.MessageFormat;

public class DBHelper extends SQLiteOpenHelper {
    private static final String KEY_CREATE_TABLE = "CREATE TABLE IF NOT EXISTS {0} ({1})";
    private static final String KEY_DROP_TABLE = "DROP TABLE IF EXISTS {0}";
    private static final int CURRENT_DB_VERSION = 1;
    private static final String DB_NAME = "indhanfebdb";

    public DBHelper(Context context) {
        super(context, (DB_NAME), null, CURRENT_DB_VERSION);
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        createUserTable(db);
        createMenuTable(db);
        createProductTable(db);
        createCropTable(db);
        createUserTeamTable(db);
        createUserPermissionTable(db);
        createSyllabusTable(db);
        createMySyllabusTable(db);
        createPlanAtpTable(db);
        createDistrictTable(db);
        createTehsilTable(db);
        createStateTable(db);
        createDataSourceTable(db);
        createBDOPlanS1Table(db);
        createBDOPlanDetailsS1Table(db);
        createImageOfflineTable(db);
        createProductCropTable(db);
        createLanguageTable(db);

    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        dropTable(db, CropTable.TABLE_NAME);
        dropTable(db, MenuTable.TABLE_NAME);
        dropTable(db, ProductTable.TABLE_NAME);
        dropTable(db, UserTable.TABLE_NAME);
        dropTable(db, UserPermissionTable.TABLE_NAME);
        dropTable(db, UserTeamTable.TABLE_NAME);
        dropTable(db, SyllabusTable.TABLE_NAME);
        dropTable(db, MySyllabusForBDOTable.TABLE_NAME);
        dropTable(db, PlanAtpTable.TABLE_NAME);
        dropTable(db, DistrictTable.TABLE_NAME);
        dropTable(db, StateTable.TABLE_NAME);
        dropTable(db, DataSourceTable.TABLE_NAME);
        dropTable(db, TehsilTable.TABLE_NAME);
        dropTable(db, BDOPlanS1Table.TABLE_NAME);
        dropTable(db, BDOPlanS1DetailTable.TABLE_NAME);
        dropTable(db, BDOPlanS1DetailTable.TABLE_NAME);
        dropTable(db, ImageOfflineTable.TABLE_NAME);
        dropTable(db, LanguageTable.TABLE_NAME);
        dropTable(db, ATPTable.TABLE_NAME);
        dropTable(db, ACTIVEATPTable.TABLE_NAME);
        onCreate(db);
    }

    private void dropTable(SQLiteDatabase db, String name) {
        String query = MessageFormat.format(DBHelper.KEY_DROP_TABLE, name);
        db.execSQL(query);
    }

    private void createTable(SQLiteDatabase db, String name, String fields) {
        String query = MessageFormat.format(DBHelper.KEY_CREATE_TABLE, name, fields);
        db.execSQL(query);
    }

    //@formatter:off
    private void createUserTable(SQLiteDatabase db) {
        String userTableField = UserTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                UserTable.Cols.USERNAME + " TEXT, " +
                UserTable.Cols.PROFILE_PIC + " TEXT, " +
                UserTable.Cols.DESIGNATION_CODE + " TEXT, " +
                UserTable.Cols.EMAIL + " TEXT, " +
                UserTable.Cols.PHONE + " TEXT ";
        createTable(db, UserTable.TABLE_NAME, userTableField);
    }

    private void createImageOfflineTable(SQLiteDatabase db) {
        String userTableField = ImageOfflineTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                ImageOfflineTable.Cols.IMAGE_URL_LOCAL + " TEXT, " +
                ImageOfflineTable.Cols.LATTITUDE + " TEXT, " +
                ImageOfflineTable.Cols.LONGITUDE + " TEXT, " +
                ImageOfflineTable.Cols.TICKET_ID + " TEXT, " +
                ImageOfflineTable.Cols.IS_UPLOADED + " INTEGER, " +
                ImageOfflineTable.Cols.TYPE + " TEXT, " +
                ImageOfflineTable.Cols.DESCRIPTION + " TEXT, " +
                ImageOfflineTable.Cols.TICKET_OFFLINE + " INTEGER, " +
                ImageOfflineTable.Cols.DATE + " TEXT ";
        createTable(db, ImageOfflineTable.TABLE_NAME, userTableField);
    }

    private void createBDOPlanS1Table(SQLiteDatabase db) {
        String userTableField =
                BDOPlanS1Table.Cols.PreID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                        BDOPlanS1Table.Cols.ID + " INTEGER, " +
                        BDOPlanS1Table.Cols.BDO_NAME + " TEXT, " +
                        BDOPlanS1Table.Cols.ACTIVITES + " INTEGER, " +
                        BDOPlanS1Table.Cols.MONTH + " INTEGER ";

        createTable(db, BDOPlanS1Table.TABLE_NAME, userTableField);
    }

    private void createBDOPlanDetailsS1Table(SQLiteDatabase db) {
        String userTableField = BDOPlanS1DetailTable.Cols.ID + " INTEGER , " +
                BDOPlanS1DetailTable.Cols.PLAN_ATP_ID + " INTEGER  PRIMARY KEY, " +
                BDOPlanS1DetailTable.Cols.NAME + " TEXT ," +
                BDOPlanS1DetailTable.Cols.PRODUCT + " TEXT ," +
                BDOPlanS1DetailTable.Cols.PRODUCT1 + " TEXT ," +
                BDOPlanS1DetailTable.Cols.PRODUCT2 + " TEXT ," +
                BDOPlanS1DetailTable.Cols.CROP + " TEXT ," +
                BDOPlanS1DetailTable.Cols.DATA_SOURCE + " TEXT ," +
                BDOPlanS1DetailTable.Cols.STATE + " TEXT , " +
                BDOPlanS1DetailTable.Cols.TEHSIL + " TEXT , " +
                BDOPlanS1DetailTable.Cols.DISTRICT + " TEXT , " +
                BDOPlanS1DetailTable.Cols.VILLAGE + " TEXT , " +
                BDOPlanS1DetailTable.Cols.EXPECTED_FARMER + " INTEGER , " +
                BDOPlanS1DetailTable.Cols.EXPECTED_PDA + " INTEGER,  " +
                BDOPlanS1DetailTable.Cols.PLAN_DATE + " TEXT ";
        createTable(db, BDOPlanS1DetailTable.TABLE_NAME, userTableField);
    }

    private void createUserTeamTable(SQLiteDatabase db) {
        String userTableField = UserTeamTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                UserTeamTable.Cols.USERNAME + " TEXT, " +
                UserTeamTable.Cols.PROFILE_PIC + " TEXT, " +
                UserTeamTable.Cols.DESIGNATION_CODE + " TEXT, " +
                UserTeamTable.Cols.DESIGNATION_NAME + " TEXT," + " " +
                UserTeamTable.Cols.PARENT_ID + " INTEGER," +
                UserTeamTable.Cols.EMAIL + " TEXT, " +
                UserTeamTable.Cols.IS_ON_DEPUTATION + " INTEGER, " +
                UserTeamTable.Cols.PHONE + " TEXT";
        createTable(db, UserTeamTable.TABLE_NAME, userTableField);
    }

    private void createMySyllabusTable(SQLiteDatabase db) {
        String userTableField = MySyllabusForBDOTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT , " +
                MySyllabusForBDOTable.Cols.PRODUCT_ID + " INTEGER, " +
                MySyllabusForBDOTable.Cols.PRODUCT_NAME + " TEXT, " +
                MySyllabusForBDOTable.Cols.CROP_ID + " INTEGER, " +
                MySyllabusForBDOTable.Cols.CROP_NAME + " TEXT, " +
                MySyllabusForBDOTable.Cols.NO_OF_DAYS + " INTEGER, " +
                MySyllabusForBDOTable.Cols.MONTH + " INTEGER, " +
                MySyllabusForBDOTable.Cols.YEAR + " INTEGER, " +
                MySyllabusForBDOTable.Cols.ATP + " INTEGER, " +
                MySyllabusForBDOTable.Cols.NO_OF_DD + " INTEGER";
        createTable(db, MySyllabusForBDOTable.TABLE_NAME, userTableField);
        //
    }

    private void createPlanAtpTable(SQLiteDatabase db) {
        String planTableField = PlanAtpTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                PlanAtpTable.Cols.PRODUCT_ID + " INTEGER, " +
                PlanAtpTable.Cols.CROP_ID + " INTEGER, " +
                PlanAtpTable.Cols.CROP_ONE + " TEXT, " +
                PlanAtpTable.Cols.CROP_TWO + " TEXT, " +
                PlanAtpTable.Cols.DATA_SOURCE_ID + " INTEGER, " +
                PlanAtpTable.Cols.LANGUAGE_ID + " INTEGER, " +
                PlanAtpTable.Cols.STATE_ID + " INTEGER, " +
                PlanAtpTable.Cols.DISTRICT_ID + " INTEGER, " +
                PlanAtpTable.Cols.TEHSIL_ID + " INTEGER, " +
                PlanAtpTable.Cols.EXPECTED_FARMER + " INTEGER, " +
                PlanAtpTable.Cols.TERRITORY_ID + " INTEGER, " +
                PlanAtpTable.Cols.TERRITORY_NAME + " TEXT, " +
                PlanAtpTable.Cols.VILLAGE + " TEXT, " +
                PlanAtpTable.Cols.EXPECTED_PDA + " INTEGER, " +
                PlanAtpTable.Cols.ISUPLOADED + " INTEGER, " +
                PlanAtpTable.Cols.RECORD_ID + " TEXT," +
                PlanAtpTable.Cols.DATE + " TEXT, " +
                PlanAtpTable.Cols.PRODUCT_NAME + " TEXT," +
                PlanAtpTable.Cols.CROP_NAME + " TEXT," +
                PlanAtpTable.Cols.TEHSIL_NAME + " TEXT," +
                PlanAtpTable.Cols.STATE_NAME + " TEXT," +
                PlanAtpTable.Cols.DISTRICT_NAME + " TEXT," +
                PlanAtpTable.Cols.DATA_SOURCE + " TEXT," +
                PlanAtpTable.Cols.PRESENTER_ID + " TEXT," +
                PlanAtpTable.Cols.PRESENTER_USER_TYPE + " TEXT," +
                PlanAtpTable.Cols.MONITOR_ID + " TEXT," +
                PlanAtpTable.Cols.MONITOR_USER_TYPE + " TEXT," +
                PlanAtpTable.Cols.DD_ID + " TEXT," +
                PlanAtpTable.Cols.IS_FL + " TEXT";


        /// TEXT
        createTable(db, PlanAtpTable.TABLE_NAME, planTableField);

    }

    private void createMenuTable(SQLiteDatabase db) {
        String menuTableField = MenuTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                MenuTable.Cols.NAME + " TEXT, " +
                MenuTable.Cols.TARGET + " TEXT, " +
                MenuTable.Cols.IMAGE_URL + " TEXT, " +
                MenuTable.Cols.NOTIFICATION_COUNT + " INTEGER, " +
                MenuTable.Cols.MENU_SHOW + " INTEGER ";
        createTable(db, MenuTable.TABLE_NAME, menuTableField);
    }

    private void createSyllabusTable(SQLiteDatabase db) {
        String syllabusTableField = SyllabusTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                SyllabusTable.Cols.BDO_ID + " INTEGER, " +
                SyllabusTable.Cols.PRODUCT_ID + " INTEGER, " +
                SyllabusTable.Cols.CROP_ID + " INTEGER, " +
                SyllabusTable.Cols.NO_OF_DAYS + " INTEGER, " +
                SyllabusTable.Cols.NO_OF_DD + " INTEGER, " +
                SyllabusTable.Cols.MONTH + " TEXT, " +
                SyllabusTable.Cols.TIMESTAMP + " TEXT, " +
                SyllabusTable.Cols.ISUPLOADED + " INTEGER, " +
                SyllabusTable.Cols.RECORD_ID + " TEXT, " +
                SyllabusTable.Cols.BDO_NAME + " TEXT, " +
                SyllabusTable.Cols.PRODUCT_NAME + " TEXT, " +
                SyllabusTable.Cols.CROP_NAME + " TEXT ";

        createTable(db, SyllabusTable.TABLE_NAME, syllabusTableField);
        //
    }

    private void createProductTable(SQLiteDatabase db) {
        String productTableField = ProductTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                ProductTable.Cols.PRODUCT_NAME + " TEXT ";
        createTable(db, ProductTable.TABLE_NAME, productTableField);
    }

    private void createLanguageTable(SQLiteDatabase db) {
        String productTableField = LanguageTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                LanguageTable.Cols.LANGUAGE_NAME + " TEXT ";
        createTable(db, LanguageTable.TABLE_NAME, productTableField);
    }

    private void createProductCropTable(SQLiteDatabase db) {
        String productTableField = ProductCropTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                ProductCropTable.Cols.PRODUCT_ID + " TEXT, " +
                ProductCropTable.Cols.CROP_ID + " TEXT, " +
                ProductCropTable.Cols.PRODUCT + " TEXT, " +
                ProductCropTable.Cols.CROP + " TEXT, " +
                ProductCropTable.Cols.CATEGORY + " TEXT ";
        createTable(db, ProductCropTable.TABLE_NAME, productTableField);
    }

    private void createCropTable(SQLiteDatabase db) {
        String cropTableField = CropTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                CropTable.Cols.CROP_NAME + " TEXT, " +
                CropTable.Cols.PRODUCT_ID + " INTEGER ";
        createTable(db, CropTable.TABLE_NAME, cropTableField);
    }

    private void createUserPermissionTable(SQLiteDatabase db) {
        String userPermissionField = UserPermissionTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                UserPermissionTable.Cols.PERMISSION_NAME + " TEXT, " +
                UserPermissionTable.Cols.PERMISSION_VALUE + " TEXT ";
        createTable(db, UserPermissionTable.TABLE_NAME, userPermissionField);
    }

    private void createDistrictTable(SQLiteDatabase db) {
        String userPermissionField = DistrictTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                DistrictTable.Cols.DISTRICT_NAME + " TEXT, " +
                DistrictTable.Cols.STATE_ID + " INTEGER ";
        createTable(db, DistrictTable.TABLE_NAME, userPermissionField);
    }

    private void createTehsilTable(SQLiteDatabase db) {
        String userPermissionField = TehsilTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                TehsilTable.Cols.TEHSIL_NAME + " TEXT, " +
                TehsilTable.Cols.DISTRICT_ID + " INTEGER ";
        createTable(db, TehsilTable.TABLE_NAME, userPermissionField);
    }

    private void createStateTable(SQLiteDatabase db) {
        String userPermissionField = StateTable.Cols.ID + " INTEGER PRIMARY KEY , " +
                StateTable.Cols.STATE_NAME + " TEXT ";
        createTable(db, StateTable.TABLE_NAME, userPermissionField);
    }

    private void createDataSourceTable(SQLiteDatabase db) {
        String userPermissionField = DataSourceTable.Cols.ID + " INTEGER PRIMARY KEY AUTOINCREMENT, " +
                DataSourceTable.Cols.Name + " TEXT ";
        createTable(db, DataSourceTable.TABLE_NAME, userPermissionField);
    }


}
