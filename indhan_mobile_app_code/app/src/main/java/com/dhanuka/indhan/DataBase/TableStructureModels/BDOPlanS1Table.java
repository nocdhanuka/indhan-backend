package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/29/2018.
 */
public class BDOPlanS1Table {
    public static final String TABLE_NAME = "bdoplans1table";
    public static final String PATH = "bdoplans1table";
    public static final int PATH_TOKEN = 17;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String PreID = "id";
        public static final String ID = "bdo_id";
        public static final String BDO_NAME = "bdo_name";
        public static final String ACTIVITES = "activites";
        public static final String MONTH = "month";
    }
}
