package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;

/**
 * Created by Shivam on 3/8/2018.
 */

public class AddNumberModel implements Serializable {
    @SerializedName("status")
    @Expose
    private int status;
    @SerializedName("number")
    @Expose
    private String number;
    @SerializedName("time")
    @Expose
    private String time;
    @SerializedName("serverTime")
    @Expose
    private String serverTime;

    public String getNumber() {
        return number;
    }

    public void setNumber(String number) {
        this.number = number;
    }

    public String getTime() {
        return time;
    }

    public void setTime(String time) {
        this.time = time;
    }

    public String getServerTime() {
        return serverTime;
    }

    public void setServerTime(String serverTime) {
        this.serverTime = serverTime;
    }

    public int getStatus() {
        return status;
    }

    public void setStatus(int status) {
        this.status = status;
    }
}
