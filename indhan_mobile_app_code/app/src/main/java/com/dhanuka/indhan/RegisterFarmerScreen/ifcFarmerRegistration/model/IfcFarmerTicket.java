package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class IfcFarmerTicket {

    @Expose
    @SerializedName("ifc_farmer_data")
    private List<Ifc_farmer_data> ifc_farmer_data;
    @Expose
    @SerializedName("data")
    private List<Data> data;
    @Expose
    @SerializedName("base_url")
    private String image_path;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public List<Ifc_farmer_data> getIfc_farmer_data() {
        return ifc_farmer_data;
    }

    public void setIfc_farmer_data(List<Ifc_farmer_data> ifc_farmer_data) {
        this.ifc_farmer_data = ifc_farmer_data;
    }

    public List<Data> getData() {
        return data;
    }

    public void setData(List<Data> data) {
        this.data = data;
    }

    public String getImage_path() {
        return image_path;
    }

    public void setImage_path(String image_path) {
        this.image_path = image_path;
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

    public static class Ifc_farmer_data {
        @Expose
        @SerializedName("updated_at")
        private String updated_at;
        @Expose
        @SerializedName("time")
        private String created_at;
        @Expose
        @SerializedName("status")
        private int status;
        @Expose
        @SerializedName("acreage")
        private String acreage;
        @Expose
        @SerializedName("longitude")
        private String longitude;
        @Expose
        @SerializedName("latitude")
        private String latitude;
        @Expose
        @SerializedName("image_date_time")
        private String image_date_time;
        @Expose
        @SerializedName("image")
        private String image;
        @Expose
        @SerializedName("number")
        private String mobile;
        @Expose
        @SerializedName("farmer_name")
        private String farmer_name;
        @Expose
        @SerializedName("ticket_no")
        private String ticket_no;
        @Expose
        @SerializedName("user_type")
        private String user_type;
        @Expose
        @SerializedName("user_id")
        private int user_id;
        @Expose
        @SerializedName("activity_distance")
        private String activity_distance;

        public String getActivity_distance() {
            return activity_distance;
        }

        public void setActivity_distance(String activity_distance) {
            this.activity_distance = activity_distance;
        }

        @Expose
        @SerializedName("farmer_id")
        private int id;

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

        public int getStatus() {
            return status;
        }

        public void setStatus(int status) {
            this.status = status;
        }

        public String getAcreage() {
            return acreage;
        }

        public void setAcreage(String acreage) {
            this.acreage = acreage;
        }

        public String getLongitude() {
            return longitude;
        }

        public void setLongitude(String longitude) {
            this.longitude = longitude;
        }

        public String getLatitude() {
            return latitude;
        }

        public void setLatitude(String latitude) {
            this.latitude = latitude;
        }

        public String getImage_date_time() {
            return image_date_time;
        }

        public void setImage_date_time(String image_date_time) {
            this.image_date_time = image_date_time;
        }

        public String getImage() {
            return image;
        }

        public void setImage(String image) {
            this.image = image;
        }

        public String getMobile() {
            return mobile;
        }

        public void setMobile(String mobile) {
            this.mobile = mobile;
        }

        public String getFarmer_name() {
            return farmer_name;
        }

        public void setFarmer_name(String farmer_name) {
            this.farmer_name = farmer_name;
        }

        public String getTicket_no() {
            return ticket_no;
        }

        public void setTicket_no(String ticket_no) {
            this.ticket_no = ticket_no;
        }

        public String getUser_type() {
            return user_type;
        }

        public void setUser_type(String user_type) {
            this.user_type = user_type;
        }

        public int getUser_id() {
            return user_id;
        }

        public void setUser_id(int user_id) {
            this.user_id = user_id;
        }

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }
    }

    public static class Data {
        @Expose
        @SerializedName("datetime_format")
        private String datetime_format;
        @Expose
        @SerializedName("datetime")
        private String datetime;
        @Expose
        @SerializedName("data_source")
        private String data_source;
        @Expose
        @SerializedName("ticket_no")
        private String ticket_no;
        @Expose
        @SerializedName("tID")
        private int tID;

        public String getDatetime_format() {
            return datetime_format;
        }

        public void setDatetime_format(String datetime_format) {
            this.datetime_format = datetime_format;
        }

        public String getDatetime() {
            return datetime;
        }

        public void setDatetime(String datetime) {
            this.datetime = datetime;
        }

        public String getData_source() {
            return data_source;
        }

        public void setData_source(String data_source) {
            this.data_source = data_source;
        }

        public String getTicket_no() {
            return ticket_no;
        }

        public void setTicket_no(String ticket_no) {
            this.ticket_no = ticket_no;
        }

        public int getTID() {
            return tID;
        }

        public void setTID(int tID) {
            this.tID = tID;
        }
    }
}
