package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class PlanAtpTable {
    public static final String TABLE_NAME = "plan_atp_table";
    public static final String PATH = "plan_atp_table";
    public static final int PATH_TOKEN = 12;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String PRODUCT_ID = "product_id";
        public static final String CROP_ID = "crop_id";
        public static final String CROP_ONE = "crop_one";
        public static final String CROP_TWO = "crop_two";
        public static final String DATA_SOURCE_ID = "data_source_id";
        public static final String LANGUAGE_ID = "language_id";
        public static final String STATE_ID = "state_id";
        public static final String DISTRICT_ID = "district_id";
        public static final String TEHSIL_ID = "tehsil_id";
        public static final String EXPECTED_FARMER = "expected_farmer";
        public static final String ISUPLOADED = "isUploaded";
        public static final String VILLAGE = "village";
        public static final String EXPECTED_PDA = "expected_pda";
        public static final String RECORD_ID = "record_id";
        public static final String DATE = "date";
        public static final String PRODUCT_NAME = "product_name";
        public static final String CROP_NAME = "crop_name";
        public static final String TEHSIL_NAME = "tehsil_name";
        public static final String STATE_NAME = "state_name";
        public static final String DISTRICT_NAME = "district_name";
        public static final String TERRITORY_NAME = "territory";
        public static final String TERRITORY_ID = "territory_id";
        public static final String DATA_SOURCE = "data_source";
        public static final String DD_ID = "dd_id";
        public static final String IS_FL = "isFL";

        public static final String PRESENTER_ID = "presenter_id";
        public static final String MONITOR_ID = "monitor_ID";
        public static final String PRESENTER_USER_TYPE = "presenter_type";
        public static final String MONITOR_USER_TYPE = "monitor_type";

        //NAME
    }
}
