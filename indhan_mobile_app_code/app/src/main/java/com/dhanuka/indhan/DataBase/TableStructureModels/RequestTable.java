package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
class RequestTable {

    public static final String TABLE_NAME = "request_table";
    public static final String PATH = "request_table";
    public static final int PATH_TOKEN = 8;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String PLAN_ID = "plan_id";
        public static final String REQUEST = "request";
        public static final String REQUEST_STATUS = "request_status";
        public static final String TIMESTAMP = "timestamp";
        public static final String MODIFIED_DATE = "modified_date";
    }


}
