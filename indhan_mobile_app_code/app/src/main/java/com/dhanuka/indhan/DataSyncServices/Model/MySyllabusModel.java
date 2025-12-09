package com.dhanuka.indhan.DataSyncServices.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Created by John on 5/21/2018.
 */


public class MySyllabusModel {

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

        @SerializedName("my_syllabus")
        @Expose
        private List<MySyllabu> mySyllabus = null;

        public List<MySyllabu> getMySyllabus() {
            return mySyllabus;
        }

        public void setMySyllabus(List<MySyllabu> mySyllabus) {
            this.mySyllabus = mySyllabus;
        }

    }


    public class MySyllabu {

        @SerializedName("product_id")
        @Expose
        private Integer productId;
        @SerializedName("product")
        @Expose
        private String product;
        @SerializedName("crop_id")
        @Expose
        private Integer cropId;
        @SerializedName("crop")
        @Expose
        private String crop;
        @SerializedName("days")
        @Expose
        private Integer days;
        @SerializedName("dd")
        @Expose
        private Integer dd;
        @SerializedName("month")
        @Expose
        private String month;
        @SerializedName("year")
        @Expose
        private String year;
        @SerializedName("atp")
        @Expose
        private String atp;

        public String getAtp() {
            return atp;
        }

        public void setAtp(String atp) {
            this.atp = atp;
        }

        public String getYear() {
            return year;
        }

        public void setYear(String year) {
            this.year = year;
        }

        public String getMonth() {
            return month;
        }

        public void setMonth(String month) {
            this.month = month;
        }

        public Integer getProductId() {
            return productId;
        }

        public void setProductId(Integer productId) {
            this.productId = productId;
        }

        public String getProduct() {
            return product;
        }

        public void setProduct(String product) {
            this.product = product;
        }

        public Integer getCropId() {
            return cropId;
        }

        public void setCropId(Integer cropId) {
            this.cropId = cropId;
        }

        public String getCrop() {
            return crop;
        }

        public void setCrop(String crop) {
            this.crop = crop;
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

    }


}
