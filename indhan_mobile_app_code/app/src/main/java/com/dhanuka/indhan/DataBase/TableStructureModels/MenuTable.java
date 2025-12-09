package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class MenuTable {

    public static final String TABLE_NAME = "menu_table";
    public static final String PATH = "menu_table";
    public static final int PATH_TOKEN = 2;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String NAME = "name";
        public static final String TARGET = "target";
        public static final String IMAGE_URL = "image_url";
        public static final String MENU_SHOW = "menu_show";
        public static final String NOTIFICATION_COUNT = "notification_count";
        // public static final String  DROPBOX_URL = "dropbox_url";
    }


}
