package com.dhanuka.indhan.DataSyncServices.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Created by John on 5/23/2018.
 */


public class PlanRequirementDataModel {

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

    public class CropList {

        @SerializedName("pID")
        @Expose
        private Integer pID;
        @SerializedName("Item")
        @Expose
        private String item;

        public Integer getPID() {
            return pID;
        }

        public void setPID(Integer pID) {
            this.pID = pID;
        }

        public String getItem() {
            return item;
        }

        public void setItem(String item) {
            this.item = item;
        }

    }

    public class Data {

        @SerializedName("data_source_list")
        @Expose
        private List<DataSourceList> dataSourceList = null;
        @SerializedName("state_list")
        @Expose
        private List<StateList> stateList = null;
        @SerializedName("product_list")
        @Expose
        private List<ProductList> productList = null;
        @SerializedName("crop_list")
        @Expose
        private List<CropList> cropList = null;

        public List<DataSourceList> getDataSourceList() {
            return dataSourceList;
        }

        public void setDataSourceList(List<DataSourceList> dataSourceList) {
            this.dataSourceList = dataSourceList;
        }

        public List<StateList> getStateList() {
            return stateList;
        }

        public void setStateList(List<StateList> stateList) {
            this.stateList = stateList;
        }

        public List<ProductList> getProductList() {
            return productList;
        }

        public void setProductList(List<ProductList> productList) {
            this.productList = productList;
        }

        public List<CropList> getCropList() {
            return cropList;
        }

        public void setCropList(List<CropList> cropList) {
            this.cropList = cropList;
        }

    }

    public class DataSourceList {

        @SerializedName("dsID")
        @Expose
        private Integer dsID;
        @SerializedName("Item")
        @Expose
        private String item;

        public Integer getDsID() {
            return dsID;
        }

        public void setDsID(Integer dsID) {
            this.dsID = dsID;
        }

        public String getItem() {
            return item;
        }

        public void setItem(String item) {
            this.item = item;
        }

    }

    public class ProductList {

        @SerializedName("pID")
        @Expose
        private Integer pID;
        @SerializedName("Item")
        @Expose
        private String item;

        public Integer getPID() {
            return pID;
        }

        public void setPID(Integer pID) {
            this.pID = pID;
        }

        public String getItem() {
            return item;
        }

        public void setItem(String item) {
            this.item = item;
        }

    }

    public class StateList {

        @SerializedName("sID")
        @Expose
        private Integer sID;
        @SerializedName("Item")
        @Expose
        private String item;

        public Integer getSID() {
            return sID;
        }

        public void setSID(Integer sID) {
            this.sID = sID;
        }

        public String getItem() {
            return item;
        }

        public void setItem(String item) {
            this.item = item;
        }

    }
}



