package com.dhanuka.indhan.DataBase.TableDataModel;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

/**
 * Created by John on 5/29/2018.
 */


public class BDOPlanListDBModel {

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

    public class CurrentMonthPlanAtpList {

        @SerializedName("month")
        @Expose
        private Integer month;
        @SerializedName("id")
        @Expose
        private Integer id;
        @SerializedName("name")
        @Expose
        private String name;
        @SerializedName("activites")
        @Expose
        private Integer activites;

        public Integer getMonth() {
            return month;
        }

        public void setMonth(Integer month) {
            this.month = month;
        }

        public Integer getId() {
            return id;
        }

        public void setId(Integer id) {
            this.id = id;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public Integer getActivites() {
            return activites;
        }

        public void setActivites(Integer activites) {
            this.activites = activites;
        }

    }


    public class Data {

        @SerializedName("current_month_plan_atp_list")
        @Expose
        private List<CurrentMonthPlanAtpList> currentMonthPlanAtpList = null;
        @SerializedName("next_month_plan_atp_list")
        @Expose
        private List<NextMonthPlanAtpList> nextMonthPlanAtpList = null;

        public List<CurrentMonthPlanAtpList> getCurrentMonthPlanAtpList() {
            return currentMonthPlanAtpList;
        }

        public void setCurrentMonthPlanAtpList(List<CurrentMonthPlanAtpList> currentMonthPlanAtpList) {
            this.currentMonthPlanAtpList = currentMonthPlanAtpList;
        }

        public List<NextMonthPlanAtpList> getNextMonthPlanAtpList() {
            return nextMonthPlanAtpList;
        }

        public void setNextMonthPlanAtpList(List<NextMonthPlanAtpList> nextMonthPlanAtpList) {
            this.nextMonthPlanAtpList = nextMonthPlanAtpList;
        }

    }


    public class NextMonthPlanAtpList {

        @SerializedName("month")
        @Expose
        private Integer month;
        @SerializedName("id")
        @Expose
        private Integer id;
        @SerializedName("name")
        @Expose
        private String name;
        @SerializedName("activites")
        @Expose
        private Integer activites;

        public Integer getMonth() {
            return month;
        }

        public void setMonth(Integer month) {
            this.month = month;
        }

        public Integer getId() {
            return id;
        }

        public void setId(Integer id) {
            this.id = id;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public Integer getActivites() {
            return activites;
        }

        public void setActivites(Integer activites) {
            this.activites = activites;
        }

    }
}

