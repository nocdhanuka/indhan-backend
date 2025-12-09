package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

import java.util.List;

public class AllVillageBean {
    public String status;
    public int status_code;
    public String message;
    public List<VillageData> data;


    @Entity
    public static class VillageData {
        @PrimaryKey(autoGenerate = true)
        public int _id;
        public String district_id;
        public String tehsil_id;
        public String village_id;
        public String village;
        public String status;
    }
}
