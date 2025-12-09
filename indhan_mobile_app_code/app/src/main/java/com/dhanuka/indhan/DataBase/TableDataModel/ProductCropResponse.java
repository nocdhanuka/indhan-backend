package com.dhanuka.indhan.DataBase.TableDataModel;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Created by John on 8/9/2018.
 */


public class ProductCropResponse {

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

    public static class ProductCropList {

        @SerializedName("product_crop_category_id")
        @Expose
        private String productCropCategoryId;
        @SerializedName("product_id")
        @Expose
        private String productId;
        @SerializedName("product")
        @Expose
        private String product;
        @SerializedName("crop_id")
        @Expose
        private String cropId;
        @SerializedName("crop")
        @Expose
        private String crop;

        private Integer Id;

        public Integer getId() {
            return Id;
        }

        public void setId(Integer id) {
            Id = id;
        }

        public String getProductCropCategoryId() {
            return productCropCategoryId;
        }

        public void setProductCropCategoryId(String productCropCategoryId) {
            this.productCropCategoryId = productCropCategoryId;
        }

        public String getProductId() {
            return productId;
        }

        public void setProductId(String productId) {
            this.productId = productId;
        }

        public String getProduct() {
            return product;
        }

        public void setProduct(String product) {
            this.product = product;
        }

        public String getCropId() {
            return cropId;
        }

        public void setCropId(String cropId) {
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

        @SerializedName("product_crop_list")
        @Expose
        private List<ProductCropList> productCropList = null;

        public List<ProductCropList> getProductCropList() {
            return productCropList;
        }

        public void setProductCropList(List<ProductCropList> productCropList) {
            this.productCropList = productCropList;
        }

    }


}