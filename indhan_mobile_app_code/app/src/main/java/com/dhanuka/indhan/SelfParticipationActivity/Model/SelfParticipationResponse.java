package com.dhanuka.indhan.SelfParticipationActivity.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;


public class SelfParticipationResponse {

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

    public class BdoList {

        @SerializedName("bdo_id")
        @Expose
        private Integer bdoId;
        @SerializedName("bdo_name")
        @Expose
        private String bdoName;

        public Integer getBdoId() {
            return bdoId;
        }

        public void setBdoId(Integer bdoId) {
            this.bdoId = bdoId;
        }

        public String getBdoName() {
            return bdoName;
        }

        public void setBdoName(String bdoName) {
            this.bdoName = bdoName;
        }

    }


    public class Data {

        @SerializedName("bdo_list")
        @Expose
        private List<BdoList> bdoList = null;
        @SerializedName("ticket_list")
        @Expose
        private List<TicketList> ticketList = null;
        @SerializedName("atp_list")
        @Expose
        private List<AtpList> atpList = null;
        @SerializedName("is_pending_plan")
        @Expose
        private List<IsPendingPlan> isPendingPlan = null;

        public List<BdoList> getBdoList() {
            return bdoList;
        }

        public void setBdoList(List<BdoList> bdoList) {
            this.bdoList = bdoList;
        }

        public List<TicketList> getTicketList() {
            return ticketList;
        }

        public void setTicketList(List<TicketList> ticketList) {
            this.ticketList = ticketList;
        }

        public List<AtpList> getAtpList() {
            return atpList;
        }

        public void setAtpList(List<AtpList> atpList) {
            this.atpList = atpList;
        }

        public List<IsPendingPlan> getIsPendingPlan() {
            return isPendingPlan;
        }

        public void setIsPendingPlan(List<IsPendingPlan> isPendingPlan) {
            this.isPendingPlan = isPendingPlan;
        }

    }


    public class IsPendingPlan {

        @SerializedName("active_plans")
        @Expose
        private Integer activePlans;

        public Integer getActivePlans() {
            return activePlans;
        }

        public void setActivePlans(Integer activePlans) {
            this.activePlans = activePlans;
        }

    }
}


