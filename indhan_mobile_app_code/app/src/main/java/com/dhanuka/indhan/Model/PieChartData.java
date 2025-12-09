package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class PieChartData {

    @Expose
    @SerializedName("total_companyData")
    private int total_companyData;
    @Expose
    @SerializedName("total_formulation")
    private int total_formulation;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public int getTotal_companyData() {
        return total_companyData;
    }

    public void setTotal_companyData(int total_companyData) {
        this.total_companyData = total_companyData;
    }

    public int getTotal_formulation() {
        return total_formulation;
    }

    public void setTotal_formulation(int total_formulation) {
        this.total_formulation = total_formulation;
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
}
