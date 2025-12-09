package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

import java.util.ArrayList;

public class AllDistrictBean {

    public String status;
    public int status_code;
    public String message;
    public ArrayList<DistrictData> data;


    @Entity
    public static class DistrictData {
        @PrimaryKey(autoGenerate = true)
        public int _id;
        public String district_id;
        public String state_id;
        public String district;
        public String status;
    }
}
