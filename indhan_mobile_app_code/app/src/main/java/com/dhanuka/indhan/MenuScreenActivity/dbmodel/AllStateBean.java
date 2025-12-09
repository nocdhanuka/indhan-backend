package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

import java.util.ArrayList;

public class AllStateBean {

    public String status;
    public int status_code;
    public String message;
    public ArrayList<StateData> data;


    @Entity
    public static class StateData {
        @PrimaryKey(autoGenerate = true)
        public int _id;
        public String state_id;
        public String state;
        public String status;
    }
}
