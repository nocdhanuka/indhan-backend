package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/5/2018.
 */
public class ProductTable {

    public static final String TABLE_NAME = "product_table";
    public static final String PATH = "product_table";
    public static final int PATH_TOKEN = 3;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "_id";
        public static final String PRODUCT_NAME = "product_name";
    }


}
