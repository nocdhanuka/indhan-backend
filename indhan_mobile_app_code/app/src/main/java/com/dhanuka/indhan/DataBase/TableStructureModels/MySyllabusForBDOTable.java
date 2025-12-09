package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class MySyllabusForBDOTable {
    public static final String TABLE_NAME = "my_syllabus_table";
    public static final String PATH = "my_syllabus_table";
    public static final int PATH_TOKEN = 11;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String PRODUCT_ID = "product_id";
        public static final String PRODUCT_NAME = "product_name";
        public static final String CROP_ID = "crop_id";
        public static final String CROP_NAME = "crop_name";
        public static final String NO_OF_DAYS = "no_of_days";
        public static final String NO_OF_DD = "no_of_dd";
        public static final String MONTH = "month";
        public static final String ATP = "atp";
        public static final String YEAR = "year";
    }
}
