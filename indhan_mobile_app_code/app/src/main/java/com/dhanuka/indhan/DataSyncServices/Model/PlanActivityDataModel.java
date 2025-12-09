package com.dhanuka.indhan.DataSyncServices.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;


/**
 * Created by John on 5/21/2018.
 */

public class PlanActivityDataModel {

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

    public class Productlist {

        @SerializedName("product_id")
        @Expose
        private Integer productId;
        @SerializedName("product")
        @Expose
        private String product;

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

    }

    public class Bdolist {

        @SerializedName("bdo_id")
        @Expose
        private Integer bdoId;
        @SerializedName("zonal_manager_id")
        @Expose
        private Integer zonalManagerId;
        @SerializedName("regional_manager_id")
        @Expose
        private Integer regionalManagerId;
        @SerializedName("sales_regional_manager_id")
        @Expose
        private Integer salesRegionalManagerId;
        @SerializedName("area_manager_id")
        @Expose
        private Integer areaManagerId;
        @SerializedName("sales_area_manager_id")
        @Expose
        private Integer salesAreaManagerId;
        @SerializedName("state_id")
        @Expose
        private Integer stateId;
        @SerializedName("district_id")
        @Expose
        private Integer districtId;
        @SerializedName("language_id")
        @Expose
        private Integer languageId;
        @SerializedName("bdo_name")
        @Expose
        private String bdoName;
        @SerializedName("bdo_code")
        @Expose
        private String bdoCode;
        @SerializedName("bdo_hq")
        @Expose
        private String bdoHq;
        @SerializedName("bdo_mobile_no")
        @Expose
        private String bdoMobileNo;
        @SerializedName("department")
        @Expose
        private String department;
        @SerializedName("designation")
        @Expose
        private String designation;
        @SerializedName("deputation_status")
        @Expose
        private Integer deputationStatus;

        public Integer getBdoId() {
            return bdoId;
        }

        public void setBdoId(Integer bdoId) {
            this.bdoId = bdoId;
        }

        public Integer getZonalManagerId() {
            return zonalManagerId;
        }

        public void setZonalManagerId(Integer zonalManagerId) {
            this.zonalManagerId = zonalManagerId;
        }

        public Integer getRegionalManagerId() {
            return regionalManagerId;
        }

        public void setRegionalManagerId(Integer regionalManagerId) {
            this.regionalManagerId = regionalManagerId;
        }

        public Integer getSalesRegionalManagerId() {
            return salesRegionalManagerId;
        }

        public void setSalesRegionalManagerId(Integer salesRegionalManagerId) {
            this.salesRegionalManagerId = salesRegionalManagerId;
        }

        public Integer getAreaManagerId() {
            return areaManagerId;
        }

        public void setAreaManagerId(Integer areaManagerId) {
            this.areaManagerId = areaManagerId;
        }

        public Integer getSalesAreaManagerId() {
            return salesAreaManagerId;
        }

        public void setSalesAreaManagerId(Integer salesAreaManagerId) {
            this.salesAreaManagerId = salesAreaManagerId;
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

        public Integer getLanguageId() {
            return languageId;
        }

        public void setLanguageId(Integer languageId) {
            this.languageId = languageId;
        }

        public String getBdoName() {
            return bdoName;
        }

        public void setBdoName(String bdoName) {
            this.bdoName = bdoName;
        }

        public String getBdoCode() {
            return bdoCode;
        }

        public void setBdoCode(String bdoCode) {
            this.bdoCode = bdoCode;
        }

        public String getBdoHq() {
            return bdoHq;
        }

        public void setBdoHq(String bdoHq) {
            this.bdoHq = bdoHq;
        }

        public String getBdoMobileNo() {
            return bdoMobileNo;
        }

        public void setBdoMobileNo(String bdoMobileNo) {
            this.bdoMobileNo = bdoMobileNo;
        }

        public String getDepartment() {
            return department;
        }

        public void setDepartment(String department) {
            this.department = department;
        }

        public String getDesignation() {
            return designation;
        }

        public void setDesignation(String designation) {
            this.designation = designation;
        }

        public Integer getDeputationStatus() {
            return deputationStatus;
        }

        public void setDeputationStatus(Integer deputationStatus) {
            this.deputationStatus = deputationStatus;
        }

    }


    public class Croplist {

        @SerializedName("crop_id")
        @Expose
        private Integer cropId;
        @SerializedName("crop")
        @Expose
        private String crop;

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

    }


    public class Data {

        @SerializedName("bdolist")
        @Expose
        private List<Bdolist> bdolist = null;
        @SerializedName("productlist")
        @Expose
        private List<Productlist> productlist = null;
        @SerializedName("croplist")
        @Expose
        private List<Croplist> croplist = null;

        public List<Bdolist> getBdolist() {
            return bdolist;
        }

        public void setBdolist(List<Bdolist> bdolist) {
            this.bdolist = bdolist;
        }

        public List<Productlist> getProductlist() {
            return productlist;
        }

        public void setProductlist(List<Productlist> productlist) {
            this.productlist = productlist;
        }

        public List<Croplist> getCroplist() {
            return croplist;
        }

        public void setCroplist(List<Croplist> croplist) {
            this.croplist = croplist;
        }

    }

}


