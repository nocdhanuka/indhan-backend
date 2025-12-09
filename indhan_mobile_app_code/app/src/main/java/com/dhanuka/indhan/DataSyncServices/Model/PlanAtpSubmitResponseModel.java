package com.dhanuka.indhan.DataSyncServices.Model;

/**
 * Created by John on 5/30/2018.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;


public class PlanAtpSubmitResponseModel {

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

        @SerializedName("plan_date")
        @Expose
        private String planDate;
        @SerializedName("product_id")
        @Expose
        private Integer productId;
        @SerializedName("crop_id")
        @Expose
        private Integer cropId;
        @SerializedName("data_source_id")
        @Expose
        private Integer dataSourceId;
        @SerializedName("state_id")
        @Expose
        private Integer stateId;
        @SerializedName("district_id")
        @Expose
        private Integer districtId;
        @SerializedName("territory_id")
        @Expose
        private Integer territoryId;
        @SerializedName("tehsil_id")
        @Expose
        private Integer tehsilId;
        @SerializedName("village")
        @Expose
        private String village;
        @SerializedName("expected_farmer")
        @Expose
        private Integer expectedFarmer;
        @SerializedName("expected_pda")
        @Expose
        private Integer expectedPda;
        @SerializedName("created_by_user_id")
        @Expose
        private Integer createdByUserId;
        @SerializedName("user_type")
        @Expose
        private String userType;
        @SerializedName("record_id")
        @Expose
        private String recordId;

        public Integer getTerritoryId() {
            return territoryId;
        }

        public void setTerritoryId(Integer territoryId) {
            this.territoryId = territoryId;
        }

        public String getPlanDate() {
            return planDate;
        }

        public void setPlanDate(String planDate) {
            this.planDate = planDate;
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

        public Integer getDataSourceId() {
            return dataSourceId;
        }

        public void setDataSourceId(Integer dataSourceId) {
            this.dataSourceId = dataSourceId;
        }

        public Integer getStateId() {
            return stateId;
        }

        public void setStateId(Integer stateId) {
            this.stateId = stateId;
        }

        public Integer getDistrictId() {
            return districtId;
        }

        public void setDistrictId(Integer districtId) {
            this.districtId = districtId;
        }

        public Integer getTehsilId() {
            return tehsilId;
        }

        public void setTehsilId(Integer tehsilId) {
            this.tehsilId = tehsilId;
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

        public String getRecordId() {
            return recordId;
        }

        public void setRecordId(String recordId) {
            this.recordId = recordId;
        }

    }
}