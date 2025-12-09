package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class UpdateDistributor {

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
        @SerializedName("ratings")
        private String ratings;
        @Expose
        @SerializedName("connected_retailers")
        private String connected_retailers;
        @Expose
        @SerializedName("brands")
        private String brands;
        @Expose
        @SerializedName("crope")
        private String crope;
        @Expose
        @SerializedName("updated_at")
        private String updated_at;
        @Expose
        @SerializedName("created_at")
        private String created_at;
        @Expose
        @SerializedName("status")
        private String status;
        @Expose
        @SerializedName("distributerforname")
        private String distributerforname;
        @Expose
        @SerializedName("ecnname")
        private String ecnname;
        @Expose
        @SerializedName("addedbyecn")
        private String addedbyecn;
        @Expose
        @SerializedName("turnover")
        private String turnover;
        @Expose
        @SerializedName("pincode")
        private String pincode;
        @Expose
        @SerializedName("Adress")
        private String Adress;
        @Expose
        @SerializedName("mobile2")
        private String mobile2;
        @Expose
        @SerializedName("mobilenumber")
        private String mobilenumber;
        @Expose
        @SerializedName("distributer_name")
        private String distributer_name;
        @Expose
        @SerializedName("market")
        private String market;
        @Expose
        @SerializedName("territory_id")
        private String territory_id;
        @Expose
        @SerializedName("district_id")
        private String district_id;
        @Expose
        @SerializedName("state_id")
        private String state_id;
        @Expose
        @SerializedName("id")
        private int id;

        public String getRatings() {
            return ratings;
        }

        public void setRatings(String ratings) {
            this.ratings = ratings;
        }

        public String getConnected_retailers() {
            return connected_retailers;
        }

        public void setConnected_retailers(String connected_retailers) {
            this.connected_retailers = connected_retailers;
        }

        public String getBrands() {
            return brands;
        }

        public void setBrands(String brands) {
            this.brands = brands;
        }

        public String getCrope() {
            return crope;
        }

        public void setCrope(String crope) {
            this.crope = crope;
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

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getDistributerforname() {
            return distributerforname;
        }

        public void setDistributerforname(String distributerforname) {
            this.distributerforname = distributerforname;
        }

        public String getEcnname() {
            return ecnname;
        }

        public void setEcnname(String ecnname) {
            this.ecnname = ecnname;
        }

        public String getAddedbyecn() {
            return addedbyecn;
        }

        public void setAddedbyecn(String addedbyecn) {
            this.addedbyecn = addedbyecn;
        }

        public String getTurnover() {
            return turnover;
        }

        public void setTurnover(String turnover) {
            this.turnover = turnover;
        }

        public String getPincode() {
            return pincode;
        }

        public void setPincode(String pincode) {
            this.pincode = pincode;
        }

        public String getAdress() {
            return Adress;
        }

        public void setAdress(String Adress) {
            this.Adress = Adress;
        }

        public String getMobile2() {
            return mobile2;
        }

        public void setMobile2(String mobile2) {
            this.mobile2 = mobile2;
        }

        public String getMobilenumber() {
            return mobilenumber;
        }

        public void setMobilenumber(String mobilenumber) {
            this.mobilenumber = mobilenumber;
        }

        public String getDistributer_name() {
            return distributer_name;
        }

        public void setDistributer_name(String distributer_name) {
            this.distributer_name = distributer_name;
        }

        public String getMarket() {
            return market;
        }

        public void setMarket(String market) {
            this.market = market;
        }

        public String getTerritory_id() {
            return territory_id;
        }

        public void setTerritory_id(String territory_id) {
            this.territory_id = territory_id;
        }

        public String getDistrict_id() {
            return district_id;
        }

        public void setDistrict_id(String district_id) {
            this.district_id = district_id;
        }

        public String getState_id() {
            return state_id;
        }

        public void setState_id(String state_id) {
            this.state_id = state_id;
        }

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }
    }
}
