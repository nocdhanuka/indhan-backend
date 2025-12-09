package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/29/2018.
 */
public class ProductCropTable {
    public static final String TABLE_NAME = "productcroptable";
    public static final String PATH = "productcroptable";
    public static final int PATH_TOKEN = 20;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String ID = "id";
        public static final String PRODUCT_ID = "product_id";
        public static final String CROP_ID = "crop_id";
        public static final String PRODUCT = "product";
        public static final String CROP = "crop";
        public static final String CATEGORY = "category";


    }
}
