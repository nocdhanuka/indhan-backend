package com.dhanuka.indhan.SyllabusHistory.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class SharedSyllabusHistoryModel {

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

    public static class SharedSyllabusList {

        @SerializedName("product_id")
        @Expose
        String product_id;
        @SerializedName("product")
        @Expose
        String product_name;
        @SerializedName("crop_id")
        @Expose
        String crop_id;
        @SerializedName("crop")
        @Expose
        String crops_name;
        @SerializedName("days")
        @Expose
        String no_of_days;
        @SerializedName("dd")
        @Expose
        String no_of_dd;
        @SerializedName("month")
        @Expose
        String month;
        @SerializedName("year")
        @Expose
        String year;
        @SerializedName("bdo_name")
        @Expose
        String bdo_name;
        @SerializedName("atp")
        @Expose
        String atp;
        @SerializedName("datetime_format")
        @Expose
        String timestamp;
        @SerializedName("bdo_id")
        @Expose
        String bdo_id;

        public String getProduct_id() {
            return product_id;
        }

        public void setProduct_id(String product_id) {
            this.product_id = product_id;
        }

        public String getProduct_name() {
            return product_name;
        }

        public void setProduct_name(String product_name) {
            this.product_name = product_name;
        }

        public String getCrop_id() {
            return crop_id;
        }

        public void setCrop_id(String crop_id) {
            this.crop_id = crop_id;
        }

        public String getCrops_name() {
            return crops_name;
        }

        public void setCrops_name(String crops_name) {
            this.crops_name = crops_name;
        }

        public String getNo_of_days() {
            return no_of_days;
        }

        public void setNo_of_days(String no_of_days) {
            this.no_of_days = no_of_days;
        }

        public String getNo_of_dd() {
            return no_of_dd;
        }

        public void setNo_of_dd(String no_of_dd) {
            this.no_of_dd = no_of_dd;
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

        public String getBdo_name() {
            return bdo_name;
        }

        public void setBdo_name(String bdo_name) {
            this.bdo_name = bdo_name;
        }

        public String getAtp() {
            return atp;
        }

        public void setAtp(String atp) {
            this.atp = atp;
        }

        public String getTimestamp() {
            return timestamp;
        }

        public void setTimestamp(String timestamp) {
            this.timestamp = timestamp;
        }

        public String getBdo_id() {
            return bdo_id;
        }

        public void setBdo_id(String bdo_id) {
            this.bdo_id = bdo_id;
        }
    }

    public class Data {

        @SerializedName("my_syllabus")
        @Expose
        private List<SharedSyllabusList> sharedSyllabusLists = null;

        public List<SharedSyllabusList> getSharedSyllabusLists() {
            return sharedSyllabusLists;
        }

        public void setSharedSyllabusLists(List<SharedSyllabusList> sharedSyllabusLists) {
            this.sharedSyllabusLists = sharedSyllabusLists;
        }
    }
}
