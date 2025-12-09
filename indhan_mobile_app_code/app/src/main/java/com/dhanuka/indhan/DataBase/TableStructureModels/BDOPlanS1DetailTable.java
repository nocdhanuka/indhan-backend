package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

/**
 * Created by John on 5/29/2018.
 */
public class BDOPlanS1DetailTable {
    public static final String TABLE_NAME = "bdoplans1detailstable";
    public static final String PATH = "bdoplans1detailstable";
    public static final int PATH_TOKEN = 18;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {
        public static final String PLAN_ATP_ID = "plan_atp_id";
        public static final String ID = "id";
        public static final String NAME = "name";
        public static final String PRODUCT = "product";
        public static final String PRODUCT1 = "product1";
        public static final String PRODUCT2 = "product2";
        public static final String CROP = "crop";
        public static final String DATA_SOURCE = "data_source";
        public static final String STATE = "state";
        public static final String TEHSIL = "tehsil";
        public static final String DISTRICT = "district";
        public static final String VILLAGE = "village";
        public static final String EXPECTED_FARMER = "expected_farmer";
        public static final String EXPECTED_PDA = "expected_pda";
        public static final String PLAN_DATE = "plan_date";
    }
}
