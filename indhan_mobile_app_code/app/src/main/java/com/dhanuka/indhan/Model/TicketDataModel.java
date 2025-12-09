package com.dhanuka.indhan.Model;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

/**
 * Created by Shivam on 3/9/2018.
 */

public class TicketDataModel {

    @SerializedName("tID")
    @Expose
    private String tID;
    @SerializedName("ticket")
    @Expose
    private String ticket;
    @SerializedName("datetime")
    @Expose
    private String datetime;
    @SerializedName("datetime_format")
    @Expose
    private String datetime_format;

    public String gettID() {
        return tID;
    }

    public void settID(String tID) {
        this.tID = tID;
    }

    public String getTicket() {
        return ticket;
    }

    public void setTicket(String ticket) {
        this.ticket = ticket;
    }

    public String getDatetime() {
        return datetime;
    }

    public void setDatetime(String datetime) {
        this.datetime = datetime;
    }

    public String getDatetime_format() {
        return datetime_format;
    }

    public void setDatetime_format(String datetime_format) {
        this.datetime_format = datetime_format;
    }
}
