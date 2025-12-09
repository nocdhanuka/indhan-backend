package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class FormulationResponse {

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
        @SerializedName("created_at")
        private String created_at;
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
        @SerializedName("name")
        private String name;
        @Expose
        @SerializedName("class_id")
        private String class_id;
        @Expose
        @SerializedName("f_id")
        private String f_id;

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

        public String getValue() {
            return value;
        }

        public void setValue(String value) {
            this.value = value;
        }

        public String getVolume() {
            if(volume == null){
                volume = "";
            }
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

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getClass_id() {
            return class_id;
        }

        public void setClass_id(String class_id) {
            this.class_id = class_id;
        }

        public String getF_id() {
            return f_id;
        }

        public void setF_id(String f_id) {
            this.f_id = f_id;
        }
    }
}
