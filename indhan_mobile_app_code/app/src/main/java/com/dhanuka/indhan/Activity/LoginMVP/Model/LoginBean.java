package com.dhanuka.indhan.Activity.LoginMVP.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Created by John on 6/3/2018.
 */


public class LoginBean {

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

    public class UserState {

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


    public class Data {

        @SerializedName("user_type")
        @Expose
        private String userType;
        @SerializedName("designation")
        @Expose
        private String designation;
        @SerializedName("user_id")
        @Expose
        private String user_id;
        @SerializedName("user_state")
        @Expose
        private List<UserState> userState = null;
        @SerializedName("ECN")
        @Expose
        private String eCN;
        @SerializedName("api_token")
        @Expose
        private String apiToken;
        @SerializedName("Username")
        @Expose
        private String username;
        @SerializedName("activity_permission")
        @Expose
        private String activityPermission;

        public String getActivityPermission() {
            if (activityPermission == null) {
                activityPermission = "0";
            }
            return activityPermission;
        }

        public void setActivityPermission(String activityPermission) {
            this.activityPermission = activityPermission;
        }

        public String getDesignation() {
            return designation;
        }

        public void setDesignation(String designation) {
            this.designation = designation;
        }

        public String getUser_id() {
            return user_id;
        }

        public void setUser_id(String user_id) {
            this.user_id = user_id;
        }

        public String geteCN() {
            return eCN;
        }

        public void seteCN(String eCN) {
            this.eCN = eCN;
        }

        public String getUsername() {
            return username;
        }

        public void setUsername(String username) {
            this.username = username;
        }

        public String getUserType() {
            return userType;
        }

        public void setUserType(String userType) {
            this.userType = userType;
        }

        public List<UserState> getUserState() {
            return userState;
        }

        public void setUserState(List<UserState> userState) {
            this.userState = userState;
        }

        public String getECN() {
            return eCN;
        }

        public void setECN(String eCN) {
            this.eCN = eCN;
        }

        public String getApiToken() {
            return apiToken;
        }

        public void setApiToken(String apiToken) {
            this.apiToken = apiToken;
        }

    }

}


