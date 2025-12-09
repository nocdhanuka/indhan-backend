package com.dhanuka.indhan.DataBase.TableStructureModels;

import android.net.Uri;

import com.dhanuka.indhan.DataBase.DbStructure.ContentDescriptor;

public class ACTIVEATPTable {

    public static final String TABLE_NAME = "atp_offline_active";
    public static final String PATH = "atp_offline_active";
    public static final int PATH_TOKEN = 38;
    public static final Uri CONTENT_URI = ContentDescriptor.BASE_URI.buildUpon().appendPath(PATH).build();

    public static class Cols {

        public static final String tID = "id";

        public static final String TICKET = "ticket";

//        private final String datetime="datetime";
//
//        private final String datetimeFormat="datetimeFormat";
//
//        private final String description="description";
//
//        private final String totalFarmers="description";
//
//        private final String totalValidFarmers="totalValidFarmers";
//
//        private final String dataSource="dataSource";
//
//        private final String state="state";
//
//        private final String district="district";
//
//        private final String tehsil="tehsil";
//
//        private String village="village";
//
//        private String product1="product1";
//
//        private String product2="product2";
//
//        private String product3="product3";
//
//        private String crop1="crop1";
//
//        private String crop2="crop2";
//
//        private String crop3="crop3";
//
//        private String expectedFarmers="expectedFarmers";
//
//        private String expectedPda="expectedPda";
//
//        private String spName1="spName1";
//
//        private String spNumber1="spNumber1";
//
//        private String spName2="spName2";
//
//        private String spNumber2="spNumber2";
//
//        private String spName3="spName3";
//
//        private String spNumber3="spNumber3";
//
//
//        private String spName4="spName4";
//
//        private String spNumber4="spNumber4";
//
//        private String spName5="spName5";
//
//        private String spNumber5="spNumber5";
//
//        private String spName6="spName6";
//
//        private String spNumber6="spNumber6";
//
//        private String baseUrl="baseUrl";
//
//        private String uploadedFile="uploadedFile";
//
//        private String ticketStatus="ticketStatus";
//
//        private String language_id="language_id";
//
//        private String language="language";
//
//        private String ddName="ddName";
//
//        private String ddId="ddId";
//
//        private String userType="userType";
//
//
//
//
//        private final String feedbackImage="feedbackImage";
//
//        private String uploadedByType="uploadedByType";
//
//
//        private String heightLightCount="heightLightCount";
    }
}
