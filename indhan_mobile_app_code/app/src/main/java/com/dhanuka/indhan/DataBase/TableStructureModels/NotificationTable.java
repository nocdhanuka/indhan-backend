package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
class NotificationTable {

    public static final String TABLE_NAME = "notification_table";
    public static final String PATH = "notification_table";
    public static final int PATH_TOKEN = 10;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String HEADING = "heading";
        public static final String MESSAGE = "message";
        public static final String TARGET_SCREEN = "target_screen";
        public static final String IS_READ = "is_read";
    }


}
