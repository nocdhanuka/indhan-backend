package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class ChemicalAddDistrictWiseResponse {

    @Expose
    @SerializedName("data")
    private Data data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public Data getData() {
        return data;
    }

    public void setData(Data data) {
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
        @SerializedName("id")
        private int id;
        @Expose
        @SerializedName("remarks")
        private String remarks;
        @Expose
        @SerializedName("formulation_id")
        private String formulation_id;
        @Expose
        @SerializedName("class_id")
        private String class_id;
        @Expose
        @SerializedName("district_id")
        private String district_id;
        @Expose
        @SerializedName("state_id")
        private String state_id;
        @Expose
        @SerializedName("ecnname")
        private String ecnname;
        @Expose
        @SerializedName("ecnnumber")
        private String ecnnumber;

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }

        public String getRemarks() {
            return remarks;
        }

        public void setRemarks(String remarks) {
            this.remarks = remarks;
        }

        public String getFormulation_id() {
            return formulation_id;
        }

        public void setFormulation_id(String formulation_id) {
            this.formulation_id = formulation_id;
        }

        public String getClass_id() {
            return class_id;
        }

        public void setClass_id(String class_id) {
            this.class_id = class_id;
        }

        public String getDistrict_id() {
            return district_id;
        }

        public void setDistrict_id(String district_id) {
            this.district_id = district_id;
        }

        public String getState_id() {
            return state_id;
        }

        public void setState_id(String state_id) {
            this.state_id = state_id;
        }

        public String getEcnname() {
            return ecnname;
        }

        public void setEcnname(String ecnname) {
            this.ecnname = ecnname;
        }

        public String getEcnnumber() {
            return ecnnumber;
        }

        public void setEcnnumber(String ecnnumber) {
            this.ecnnumber = ecnnumber;
        }
    }
}
