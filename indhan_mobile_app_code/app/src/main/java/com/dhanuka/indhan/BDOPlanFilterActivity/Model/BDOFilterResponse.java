package com.dhanuka.indhan.BDOPlanFilterActivity.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.List;

/**
 * Created by John on 6/8/2018.
 */


public class BDOFilterResponse {

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


    public class BDOList implements Serializable {

        @SerializedName("area_manager_ID")
        @Expose
        private Integer areaManagerID;
        @SerializedName("bdo_ID")
        @Expose
        private Integer bdoID;
        @SerializedName("bdo_NAME")
        @Expose
        private String bdoNAME;
        @SerializedName("ECN")
        @Expose
        private String eCN;
        private boolean isSelected = false;

        public String geteCN() {
            return eCN;
        }

        public void seteCN(String eCN) {
            this.eCN = eCN;
        }

        public boolean isSelected() {
            return isSelected;
        }

        public void setSelected(boolean selected) {
            isSelected = selected;
        }

        public Integer getAreaManagerID() {
            return areaManagerID;
        }

        public void setAreaManagerID(Integer areaManagerID) {
            this.areaManagerID = areaManagerID;
        }

        public Integer getBdoID() {
            return bdoID;
        }

        public void setBdoID(Integer bdoID) {
            this.bdoID = bdoID;
        }

        public String getBdoNAME() {
            return bdoNAME;
        }

        public void setBdoNAME(String bdoNAME) {
            this.bdoNAME = bdoNAME;
        }

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

    }


    public class Data {

        @SerializedName("Regional_Manager_List")
        @Expose
        private List<RegionalManagerList> regionalManagerList = null;
        @SerializedName("Area_Manager_List")
        @Expose
        private List<AreaManagerList> areaManagerList = null;
        @SerializedName("BDO_List")
        @Expose
        private List<BDOList> bDOList = null;

        public List<RegionalManagerList> getRegionalManagerList() {
            return regionalManagerList;
        }

        public void setRegionalManagerList(List<RegionalManagerList> regionalManagerList) {
            this.regionalManagerList = regionalManagerList;
        }

        public List<AreaManagerList> getAreaManagerList() {
            return areaManagerList;
        }

        public void setAreaManagerList(List<AreaManagerList> areaManagerList) {
            this.areaManagerList = areaManagerList;
        }

        public List<BDOList> getBDOList() {
            return bDOList;
        }

        public void setBDOList(List<BDOList> bDOList) {
            this.bDOList = bDOList;
        }

    }

    public class AreaManagerList {

        @SerializedName("regional_manager_ID")
        @Expose
        private Integer regionalManagerID;
        @SerializedName("area_manager_ID")
        @Expose
        private Integer areaManagerID;
        @SerializedName("area_NAME")
        @Expose
        private String areaNAME;
        @SerializedName("ECN")
        @Expose
        private String eCN;

        private boolean isSelected = false;

        public boolean isSelected() {
            return isSelected;
        }

        public void setSelected(boolean selected) {
            isSelected = selected;
        }

        public Integer getRegionalManagerID() {
            return regionalManagerID;
        }

        public void setRegionalManagerID(Integer regionalManagerID) {
            this.regionalManagerID = regionalManagerID;
        }

        public Integer getAreaManagerID() {
            return areaManagerID;
        }

        public void setAreaManagerID(Integer areaManagerID) {
            this.areaManagerID = areaManagerID;
        }

        public String getAreaNAME() {
            return areaNAME;
        }

        public void setAreaNAME(String areaNAME) {
            this.areaNAME = areaNAME;
        }

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

    }


    public class RegionalManagerList {

        @SerializedName("regional_manager_ID")
        @Expose
        private Integer regionalManagerID;
        @SerializedName("regional_NAME")
        @Expose
        private String regionalNAME;
        @SerializedName("ECN")
        @Expose
        private String eCN;

        private boolean isSelected = false;

        public String geteCN() {
            return eCN;
        }

        public void seteCN(String eCN) {
            this.eCN = eCN;
        }

        public boolean isSelected() {
            return isSelected;
        }

        public void setSelected(boolean selected) {
            isSelected = selected;
        }

        public Integer getRegionalManagerID() {
            return regionalManagerID;
        }

        public void setRegionalManagerID(Integer regionalManagerID) {
            this.regionalManagerID = regionalManagerID;
        }

        public String getRegionalNAME() {
            return regionalNAME;
        }

        public void setRegionalNAME(String regionalNAME) {
            this.regionalNAME = regionalNAME;
        }

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

    }

}

