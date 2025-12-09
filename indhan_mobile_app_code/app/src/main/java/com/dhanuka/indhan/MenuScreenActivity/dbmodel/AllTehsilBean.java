package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

import java.util.ArrayList;

public class AllTehsilBean {
    public String status;
    public int status_code;
    public String message;
    public ArrayList<TehsilData> data;


    @Entity
    public static class TehsilData {
        @PrimaryKey(autoGenerate = true)
        public int _id;
        public String district_id;
        public String tehsil_id;
        public String tehsil;
        public String status;
    }
}
