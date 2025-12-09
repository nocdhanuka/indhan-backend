package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class ArrivalDepartureResponse {

    //    @Expose
//    @SerializedName("data")
//    private Data data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

//    public Data getData() {
//        return data;
//    }
//
//    public void setData(Data data) {
//        this.data = data;
//    }

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
        @SerializedName("departure_image")
        private String departure_image;
        @Expose
        @SerializedName("arival_image")
        private String arival_image;

        public String getDeparture_image() {
            return departure_image;
        }

        public void setDeparture_image(String departure_image) {
            this.departure_image = departure_image;
        }

        public String getArival_image() {
            return arival_image;
        }

        public void setArival_image(String arival_image) {
            this.arival_image = arival_image;
        }
    }
}
