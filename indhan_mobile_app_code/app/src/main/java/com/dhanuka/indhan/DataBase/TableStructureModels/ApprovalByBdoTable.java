package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
class ApprovalByBdoTable {

    public static final String TABLE_NAME = "appropublic static final String _by_bdo_table";
    public static final String PATH = "appropublic static final String _by_bdo_table";
    public static final int PATH_TOKEN = 9;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String BDO_ID = "bdo_id";
        public static final String BDO_NAME = "bdo_name";
        public static final String PRODUCT_ID = "product_id";
        public static final String CROP_ID = "crop_id";
        public static final String NO_OF_DAYS = "no_of_days";
        public static final String NO_OF_DD = "no_of_dd";
        public static final String MEETING = "meeting";
        public static final String EXPECTED_FARMER = "expected_farmer";
        public static final String DATE = "date";
    }


}
