package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class UserTable {

    public static final String TABLE_NAME = "user_table";
    public static final String PATH = "user_table";
    public static final int PATH_TOKEN = 4;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String USERNAME = "username";
        public static final String PROFILE_PIC = "profilepic";
        public static final String EMAIL = "email";
        public static final String PHONE = "phone";
        public static final String DESIGNATION_CODE = "designation_code";
    }


}
