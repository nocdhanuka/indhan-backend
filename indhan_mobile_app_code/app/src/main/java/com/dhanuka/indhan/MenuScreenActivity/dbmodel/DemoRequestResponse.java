package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import java.util.ArrayList;

public class DemoRequestResponse {

    public ArrayList<DemoRequestList> data;
    private String message;
    private String status;
    private int status_code;

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public int getStatus_code() {
        return status_code;
    }

    public void setStatus_code(int status_code) {
        this.status_code = status_code;
    }
}
