package com.dhanuka.indhan.Activity.HomeMVP.Model

import com.dhanuka.indhan.Model.LanguageModel
import com.dhanuka.indhan.Model.TicketDataModel
import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/27/2018.
 */

class HomeBean {

    @SerializedName("status_code")
    @Expose
    var status_code: Int? = null

    @SerializedName("message")
    @Expose
    var message: String? = null

    @SerializedName("status")
    @Expose
    var status: String? = null

    @SerializedName("data")
    @Expose
    var data: Data? = null

    inner class Data {

        @SerializedName("menulist")
        @Expose
        var menulist: ArrayList<MenuBean>? = null

        @SerializedName("ticketdata")
        @Expose
        var ticketdata: ArrayList<TicketDataModel>? = null

        @SerializedName("languages")
        @Expose
        var languagesData: ArrayList<LanguageModel>? = null

        var app_latest_version: String? = null

        @SerializedName("activity_permission")
        @Expose
        var activityPermission: String? = null
    }
}
