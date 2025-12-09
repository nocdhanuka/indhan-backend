package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class ImageOfflineTable {

    public static final String TABLE_NAME = "image_gps_table";
    public static final String PATH = "image_gps_table";
    public static final int PATH_TOKEN = 19;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String IMAGE_URL_LOCAL = "image_url_local";
        public static final String LATTITUDE = "lattitude";
        public static final String LONGITUDE = "longitude";
        public static final String TICKET_ID = "ticket_id";
        public static final String IS_UPLOADED = "is_uploaded";
        public static final String TYPE = "type";
        public static final String DESCRIPTION = "description";
        public static final String TICKET_OFFLINE = "ticket_offline";
        public static final String DATE = "date";
    }
}
