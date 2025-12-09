package com.dhanuka.indhan.CustomerCare.Model;

/**
 * Created by John on 7/23/2018.
 */


import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;
import java.util.List;


public class CCQueriesForS1Model {

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

    public class ResponseList implements Serializable {

        @SerializedName("customer_query_id")
        @Expose
        private String customerQueryId;
        @SerializedName("ECN")
        @Expose
        private String eCN;
        @SerializedName("name")
        @Expose
        private String name;
        @SerializedName("token_number")
        @Expose
        private String tokenNumber;
        @SerializedName("status")
        @Expose
        private String status;
        @SerializedName("product")
        @Expose
        private String product;
        @SerializedName("product_quantity")
        @Expose
        private String productQuantity;
        @SerializedName("remark")
        @Expose
        private String remark;
        @SerializedName("created_at")
        @Expose
        private String createdAt;

        public String getCustomerQueryId() {
            return customerQueryId;
        }

        public void setCustomerQueryId(String customerQueryId) {
            this.customerQueryId = customerQueryId;
        }

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getTokenNumber() {
            return tokenNumber;
        }

        public void setTokenNumber(String tokenNumber) {
            this.tokenNumber = tokenNumber;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getProduct() {
            return product;
        }

        public void setProduct(String product) {
            this.product = product;
        }

        public String getProductQuantity() {
            return productQuantity;
        }

        public void setProductQuantity(String productQuantity) {
            this.productQuantity = productQuantity;
        }

        public String getRemark() {
            return remark;
        }

        public void setRemark(String remark) {
            this.remark = remark;
        }

        public String getCreatedAt() {
            return createdAt;
        }

        public void setCreatedAt(String createdAt) {
            this.createdAt = createdAt;
        }

    }

    public class CustomerDatum implements Serializable, Comparable<CCQueriesModel.CustomerDatum> {

        Date date;
        String date_str;
        String time;
        @SerializedName("ECN")
        @Expose
        private String eCN;
        @SerializedName("name")
        @Expose
        private String name;
        @SerializedName("query_id")
        @Expose
        private String queryId;
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

        public String geteCN() {
            return eCN;
        }

        public void seteCN(String eCN) {
            this.eCN = eCN;
        }

        public Date getDate() {
            return date;
        }

        public void setDate(Date date) {
            this.date = date;
        }

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

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getQueryId() {
            return queryId;
        }

        public void setQueryId(String queryId) {
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
        public int compareTo(@NonNull CCQueriesModel.CustomerDatum customerDatum) {
            return getDate().compareTo(customerDatum.date);
        }
    }

    public class Data {

        @SerializedName("customer_data")
        @Expose
        private List<CustomerDatum> customerData = null;
        @SerializedName("response_list")
        @Expose
        private List<ResponseList> responseList = null;

        public List<CustomerDatum> getCustomerData() {
            return customerData;
        }

        public void setCustomerData(List<CustomerDatum> customerData) {
            this.customerData = customerData;
        }

        public List<ResponseList> getResponseList() {
            return responseList;
        }

        public void setResponseList(List<ResponseList> responseList) {
            this.responseList = responseList;
        }
    }
}








