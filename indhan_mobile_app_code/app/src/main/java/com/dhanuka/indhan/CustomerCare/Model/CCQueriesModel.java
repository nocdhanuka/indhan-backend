package com.dhanuka.indhan.CustomerCare.Model;

/**
 * Created by John on 7/23/2018.
 */


import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;
import java.util.List;

public class CCQueriesModel {

    @SerializedName("status")
    @Expose
    private String status;
    @SerializedName("status_code")
    @Expose
    private Integer statusCode;
    @SerializedName("message")
    @Expose
    private String message;
    @Nullable
    @SerializedName("data")
    @Expose
    private Data data = null;

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

    public class CustomerDatum implements Serializable, Comparable<CustomerDatum> {

        Date date;
        String date_str;
        String time;
        @SerializedName("query_id")
        @Expose
        private Integer queryId;
        @SerializedName("token_number")
        @Expose
        private String tokenNumber;
        @SerializedName("mobile")
        @Expose
        private String mobile;
        @SerializedName("farmername")
        @Expose
        private String farmername;
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
        @SerializedName("pincode")
        @Expose
        private String pincode;
        @SerializedName("product")
        @Expose
        private String product;
        @SerializedName("crop")
        @Expose
        private String crop;
        @SerializedName("disease")
        @Expose
        private String disease;
        @SerializedName("type_of_call")
        @Expose
        private String typeOfCall;
        @SerializedName("is_follow_up")
        @Expose
        private String isFollowUp;
        @SerializedName("remark")
        @Expose
        private String remark;
        @SerializedName("datetime_format")
        @Expose
        private String datetimeFormat;
        @SerializedName("status")
        @Expose
        private String status;

        public String getDate_str() {
            return date_str;
        }

        public void setDate_str(String date_str) {
            this.date_str = date_str;
        }

        public String getTime() {
            return time;
        }

        public void setTime(String time) {
            this.time = time;
        }

        public Integer getQueryId() {
            return queryId;
        }

        public void setQueryId(Integer queryId) {
            this.queryId = queryId;
        }

        public String getTokenNumber() {
            return tokenNumber;
        }

        public void setTokenNumber(String tokenNumber) {
            this.tokenNumber = tokenNumber;
        }

        public String getMobile() {
            return mobile;
        }

        public void setMobile(String mobile) {
            this.mobile = mobile;
        }

        public String getFarmername() {
            return farmername;
        }

        public void setFarmername(String farmername) {
            this.farmername = farmername;
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

        public String getPincode() {
            return pincode;
        }

        public void setPincode(String pincode) {
            this.pincode = pincode;
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

        public String getDisease() {
            return disease;
        }

        public void setDisease(String disease) {
            this.disease = disease;
        }

        public String getTypeOfCall() {
            return typeOfCall;
        }

        public void setTypeOfCall(String typeOfCall) {
            this.typeOfCall = typeOfCall;
        }

        public String getIsFollowUp() {
            return isFollowUp;
        }

        public void setIsFollowUp(String isFollowUp) {
            this.isFollowUp = isFollowUp;
        }

        public String getRemark() {
            return remark;
        }

        public void setRemark(String remark) {
            this.remark = remark;
        }

        public String getDatetimeFormat() {
            return datetimeFormat;
        }

        public void setDatetimeFormat(String datetimeFormat) {
            this.datetimeFormat = datetimeFormat;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        @Override
        public int compareTo(@NonNull CustomerDatum customerDatum) {
            if (getDate() != null) {
                return getDate().compareTo(customerDatum.date);
            } else {
                return -1;
            }
        }

        public Date getDate() {
            return date;
        }

        public void setDate(Date date) {
            this.date = date;
        }
    }


    public class Data {

        @SerializedName("customer_data")
        @Expose
        private List<CustomerDatum> customerData = null;

        public List<CustomerDatum> getCustomerData() {
            return customerData;
        }

        public void setCustomerData(List<CustomerDatum> customerData) {
            this.customerData = customerData;
        }

    }
}

