package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class MarketResponse {

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
        @SerializedName("added_by")
        private int added_by;
        @Expose
        @SerializedName("status")
        private String status;
        @Expose
        @SerializedName("market")
        private String market;
        @Expose
        @SerializedName("territory_id")
        private int territory_id;
        @Expose
        @SerializedName("district_id")
        private int district_id;
        @Expose
        @SerializedName("market_id")
        private int market_id;

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

        public int getAdded_by() {
            return added_by;
        }

        public void setAdded_by(int added_by) {
            this.added_by = added_by;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public String getMarket() {
            return market;
        }

        public void setMarket(String market) {
            this.market = market;
        }

        public int getTerritory_id() {
            return territory_id;
        }

        public void setTerritory_id(int territory_id) {
            this.territory_id = territory_id;
        }

        public int getDistrict_id() {
            return district_id;
        }

        public void setDistrict_id(int district_id) {
            this.district_id = district_id;
        }

        public int getMarket_id() {
            return market_id;
        }

        public void setMarket_id(int market_id) {
            this.market_id = market_id;
        }
    }
}
