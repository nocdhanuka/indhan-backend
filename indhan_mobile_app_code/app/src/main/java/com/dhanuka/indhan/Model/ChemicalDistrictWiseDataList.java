package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ChemicalDistrictWiseDataList {

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
        @SerializedName("updated_at")
        private String updated_at;
        @Expose
        @SerializedName("status")
        private String status;

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        @Expose
        @SerializedName("created_at")
        private String created_at;
        @Expose
        @SerializedName("remarks")
        private String remarks;
        @Expose
        @SerializedName("value")
        private String value;
        @Expose
        @SerializedName("volume")
        private String volume;
        @Expose
        @SerializedName("nrv")
        private String nrv;
        @Expose
        @SerializedName("brand")
        private String brand;
        @Expose
        @SerializedName("formulationname")
        private String formulationname;
        @Expose
        @SerializedName("classname")
        private String classname;
        @Expose
        @SerializedName("district")
        private String district;
        @Expose
        @SerializedName("state")
        private String state;
        @Expose
        @SerializedName("ecnname")
        private String ecnname;
        @Expose
        @SerializedName("ecnnumber")
        private String ecnnumber;
        @Expose
        @SerializedName("id")
        public String id;

        public String getId() {
            return id;
        }

        public void setId(String id) {
            this.id = id;
        }

        public String getUpdated_at() {
            return updated_at;
        }

        public void setUpdated_at(String updated_at) {
            this.updated_at = updated_at;
        }

        public String getCreated_at() {
            return created_at;
        }

        public void setCreated_at(String created_at) {
            this.created_at = created_at;
        }

        public String getRemarks() {
            return remarks;
        }

        public void setRemarks(String remarks) {
            this.remarks = remarks;
        }

        public String getValue() {
            return value;
        }

        public void setValue(String value) {
            this.value = value;
        }

        public String getVolume() {
            return volume;
        }

        public void setVolume(String volume) {
            this.volume = volume;
        }

        public String getNrv() {
            return nrv;
        }

        public void setNrv(String nrv) {
            this.nrv = nrv;
        }

        public String getBrand() {
            return brand;
        }

        public void setBrand(String brand) {
            this.brand = brand;
        }

        public String getFormulationname() {
            return formulationname;
        }

        public void setFormulationname(String formulationname) {
            this.formulationname = formulationname;
        }

        public String getClassname() {
            return classname;
        }

        public void setClassname(String classname) {
            this.classname = classname;
        }

        public String getDistrict() {
            return district;
        }

        public void setDistrict(String district) {
            this.district = district;
        }

        public String getState() {
            return state;
        }

        public void setState(String state) {
            this.state = state;
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
