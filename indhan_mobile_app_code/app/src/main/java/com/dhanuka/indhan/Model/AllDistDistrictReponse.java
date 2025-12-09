package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class AllDistDistrictReponse {

    @Expose
    @SerializedName("data")
    private List<Data> data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public List<Data> getData() {
        return data;
    }

    public void setData(List<Data> data) {
        this.data = data;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getStatus_code() {
        return status_code;
    }

    public void setStatus_code(int status_code) {
        this.status_code = status_code;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public static class Data {
        @Expose
        @SerializedName("distributor_by_ecn")
        private int distributor_by_ecn;
        @Expose
        @SerializedName("nonrating_distributor")
        private int nonrating_distributor;
        @Expose
        @SerializedName("rating_distributor")
        private int rating_distributor;
        @Expose
        @SerializedName("admin_distributor")
        private int admin_distributor;
        @Expose
        @SerializedName("district")
        private String district_name;
        @Expose
        @SerializedName("district_id")
        private String district_id;
        @Expose
        @SerializedName("state_id")
        private String state_id;

        public String getState_id() {
            return state_id;
        }

        public void setState_id(String state_id) {
            this.state_id = state_id;
        }

        public int getDistributor_by_ecn() {
            return distributor_by_ecn;
        }

        public void setDistributor_by_ecn(int distributor_by_ecn) {
            this.distributor_by_ecn = distributor_by_ecn;
        }

        public int getNonrating_distributor() {
            return nonrating_distributor;
        }

        public void setNonrating_distributor(int nonrating_distributor) {
            this.nonrating_distributor = nonrating_distributor;
        }

        public int getRating_distributor() {
            return rating_distributor;
        }

        public void setRating_distributor(int rating_distributor) {
            this.rating_distributor = rating_distributor;
        }

        public int getAdmin_distributor() {
            return admin_distributor;
        }

        public void setAdmin_distributor(int admin_distributor) {
            this.admin_distributor = admin_distributor;
        }

        public String getDistrict_name() {
            return district_name;
        }

        public void setDistrict_name(String district_name) {
            this.district_name = district_name;
        }

        public String getDistrict_id() {
            return district_id;
        }

        public void setDistrict_id(String district_id) {
            this.district_id = district_id;
        }
    }
}
