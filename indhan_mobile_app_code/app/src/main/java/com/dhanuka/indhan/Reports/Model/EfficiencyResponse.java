package com.dhanuka.indhan.Reports.Model;

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.ArrayList;
import java.util.Date;

/**
 * Created by John on 6/28/2018.
 */


public class EfficiencyResponse {

    @SerializedName("status")
    @Expose
    private String status;
    @SerializedName("status_code")
    @Expose
    private Integer statusCode;
    @SerializedName("message")
    @Expose
    private String message;
    @SerializedName("data")
    @Expose
    private Data data;

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public Integer getStatusCode() {
        return statusCode;
    }

    public void setStatusCode(Integer statusCode) {
        this.statusCode = statusCode;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public Data getData() {
        return data;
    }

    public void setData(Data data) {
        this.data = data;
    }

    public class Data {

        @SerializedName("efficiency")
        @Expose
        private ArrayList<Efficiency> efficiency = null;

        public ArrayList<Efficiency> getEfficiency() {
            return efficiency;
        }

        public void setEfficiency(ArrayList<Efficiency> efficiency) {
            this.efficiency = efficiency;
        }

    }

    public class Efficiency implements Comparable<Efficiency> {

        Date dateObj;
        @SerializedName("month")
        @Expose
        private String month;
        @SerializedName("year")
        @Expose
        private String year;
        @SerializedName("efficiency")
        @Expose
        private String efficiency;

        public Date getDateObj() {
            return dateObj;
        }

        public void setDateObj(Date dateObj) {
            this.dateObj = dateObj;
        }

        public String getMonth() {
            return month;
        }

        public void setMonth(String month) {
            this.month = month;
        }

        public String getYear() {
            return year;
        }

        public void setYear(String year) {
            this.year = year;
        }

        public String getEfficiency() {
            return efficiency;
        }

        public void setEfficiency(String efficiency) {
            this.efficiency = efficiency;
        }

        @Override
        public int compareTo(@NonNull Efficiency efficiency) {
            try {
                return getDateObj().compareTo(efficiency.dateObj);
            } catch (Exception e) {
                return -1;
            }
        }
    }


}
