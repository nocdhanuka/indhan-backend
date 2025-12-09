package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class SyllabusTable {
    public static final String TABLE_NAME = "syllabus_table";
    public static final String PATH = "syllabus_table";
    public static final int PATH_TOKEN = 7;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String BDO_ID = "bdo_id";
        public static final String MONTH = "month";
        public static final String PRODUCT_ID = "product_id";
        public static final String CROP_ID = "crop_id";
        public static final String NO_OF_DAYS = "no_of_days";
        public static final String NO_OF_DD = "no_of_dd";
        public static final String TIMESTAMP = "timestamp";
        public static final String ISUPLOADED = "isuploaded";
        public static final String RECORD_ID = "record_id";
        ////
        public static final String PRODUCT_NAME = "product_name";
        public static final String CROP_NAME = "crop_name";
        public static final String BDO_NAME = "BDO_NAME ";
       /* public static final String TEHSIL_NAME = "tehsil_name";
        public static final String STATE_NAME = "state_name";
        public static final String DISTRICT_NAME = "district_name";*/
    }
}
