package com.dhanuka.indhan.ActivateTicket.Model;

/**
 * Created by John on 5/31/2018.
 */

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;
import java.util.List;

public class ActiveTicketListModelOld {

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

    public class Datum implements Serializable, Comparable<Datum> {

        @SerializedName("product")
        @Expose
        private String product;
        @SerializedName("crop")
        @Expose
        private String crop;
        @SerializedName("data_source")
        @Expose
        private String dataSource;
        @SerializedName("state")
        @Expose
        private String state;
        @SerializedName("district")
        @Expose
        private String district;
        @SerializedName("tehsil")
        @Expose
        private String tehsil;
        @SerializedName("village")
        @Expose
        private String village;
        @SerializedName("expected_farmer")
        @Expose
        private Integer expectedFarmer;
        @SerializedName("expected_pda")
        @Expose
        private Integer expectedPda;
        @SerializedName("record_id")
        @Expose
        private String recordId;
        @SerializedName("plan_date")
        @Expose
        private String planDate;
        @SerializedName("plan_atp_id")
        @Expose
        private Integer planAtpId;
        @SerializedName("status")
        @Expose
        private Integer status;

        private Date date;

        public Date getDate() {
            return date;
        }

        public void setDate(Date date) {
            this.date = date;
        }

        public String getProduct() {
            return product;
        }

        public void setProduct(String product) {
            this.product = product;
        }

        public String getCrop() {
            return crop;
        }

        public void setCrop(String crop) {
            this.crop = crop;
        }

        public String getDataSource() {
            return dataSource;
        }

        public void setDataSource(String dataSource) {
            this.dataSource = dataSource;
        }

        public String getState() {
            return state;
        }

        public void setState(String state) {
            this.state = state;
        }

        public String getDistrict() {
            return district;
        }

        public void setDistrict(String district) {
            this.district = district;
        }

        public String getTehsil() {
            return tehsil;
        }

        public void setTehsil(String tehsil) {
            this.tehsil = tehsil;
        }

        public String getVillage() {
            return village;
        }

        public void setVillage(String village) {
            this.village = village;
        }

        public Integer getExpectedFarmer() {
            return expectedFarmer;
        }

        public void setExpectedFarmer(Integer expectedFarmer) {
            this.expectedFarmer = expectedFarmer;
        }

        public Integer getExpectedPda() {
            return expectedPda;
        }

        public void setExpectedPda(Integer expectedPda) {
            this.expectedPda = expectedPda;
        }

        public String getRecordId() {
            return recordId;
        }

        public void setRecordId(String recordId) {
            this.recordId = recordId;
        }

        public String getPlanDate() {
            return planDate;
        }

        public void setPlanDate(String planDate) {
            this.planDate = planDate;
        }

        public Integer getPlanAtpId() {
            return planAtpId;
        }

        public void setPlanAtpId(Integer planAtpId) {
            this.planAtpId = planAtpId;
        }

        public Integer getStatus() {
            return status;
        }

        public void setStatus(Integer status) {
            this.status = status;
        }


        @Override
        public int compareTo(@NonNull Datum datum) {
            return getDate().compareTo(datum.date);
        }
    }

}

