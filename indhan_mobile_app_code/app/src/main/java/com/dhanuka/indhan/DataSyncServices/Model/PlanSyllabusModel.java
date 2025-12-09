package com.dhanuka.indhan.DataSyncServices.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class PlanSyllabusModel {

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
    private List<Datum> data = null;

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

    public List<Datum> getData() {
        return data;
    }

    public void setData(List<Datum> data) {
        this.data = data;
    }


    public class Datum {

        @SerializedName("user_id")
        @Expose
        private Integer userId;
        @SerializedName("month")
        @Expose
        private Integer month;
        @SerializedName("product_id")
        @Expose
        private Integer productId;
        @SerializedName("crop_id")
        @Expose
        private Integer cropId;
        @SerializedName("days")
        @Expose
        private Integer days;
        @SerializedName("dd")
        @Expose
        private Integer dd;
        @SerializedName("record_id")
        @Expose
        private String recordId;
        @SerializedName("status")
        @Expose
        private String status;
        @SerializedName("created_by_user_id")
        @Expose
        private Integer createdByUserId;
        @SerializedName("user_type")
        @Expose
        private String userType;

        public Integer getUserId() {
            return userId;
        }

        public void setUserId(Integer userId) {
            this.userId = userId;
        }

        public Integer getMonth() {
            return month;
        }

        public void setMonth(Integer month) {
            this.month = month;
        }

        public Integer getProductId() {
            return productId;
        }

        public void setProductId(Integer productId) {
            this.productId = productId;
        }

        public Integer getCropId() {
            return cropId;
        }

        public void setCropId(Integer cropId) {
            this.cropId = cropId;
        }

        public Integer getDays() {
            return days;
        }

        public void setDays(Integer days) {
            this.days = days;
        }

        public Integer getDd() {
            return dd;
        }

        public void setDd(Integer dd) {
            this.dd = dd;
        }

        public String getRecordId() {
            return recordId;
        }

        public void setRecordId(String recordId) {
            this.recordId = recordId;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public Integer getCreatedByUserId() {
            return createdByUserId;
        }

        public void setCreatedByUserId(Integer createdByUserId) {
            this.createdByUserId = createdByUserId;
        }

        public String getUserType() {
            return userType;
        }

        public void setUserType(String userType) {
            this.userType = userType;
        }

    }


}