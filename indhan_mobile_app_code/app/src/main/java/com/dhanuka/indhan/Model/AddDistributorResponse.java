package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class AddDistributorResponse {

    @Expose
    @SerializedName("data")
    private DistributorResponse.Datum data;
    @Expose
    @SerializedName("message")
    private String message;
    @Expose
    @SerializedName("status_code")
    private int status_code;
    @Expose
    @SerializedName("status")
    private String status;

    public DistributorResponse.Datum getData() {
        return data;
    }

    public void setData(DistributorResponse.Datum data) {
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

}
