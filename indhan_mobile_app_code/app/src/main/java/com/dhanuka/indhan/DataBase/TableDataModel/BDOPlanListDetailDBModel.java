package com.dhanuka.indhan.DataBase.TableDataModel;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class BDOPlanListDetailDBModel {

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

    public class Data {

        @SerializedName("plan_atp_detail")
        @Expose
        private List<PlanAtpDetail> planAtpDetail = null;

        public List<PlanAtpDetail> getPlanAtpDetail() {
            return planAtpDetail;
        }

        public void setPlanAtpDetail(List<PlanAtpDetail> planAtpDetail) {
            this.planAtpDetail = planAtpDetail;
        }

    }


}

