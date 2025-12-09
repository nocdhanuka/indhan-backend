package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class DistrictTable {
    public static final String TABLE_NAME = "district_name_table";
    public static final String PATH = "district_name_table";
    public static final int PATH_TOKEN = 13;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String DISTRICT_NAME = "district_name";
        public static final String STATE_ID = "state_id";
    }
}
