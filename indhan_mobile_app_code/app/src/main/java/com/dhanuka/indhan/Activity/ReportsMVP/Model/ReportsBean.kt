package com.dhanuka.indhan.Activity.ReportsMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 2/28/2018.
 */

class ReportsBean {

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


        @SerializedName("Total_Activities")
        @Expose
        var total_Activities: String? = null

        @SerializedName("Total_Farmers")
        @Expose
        var total_Farmers: String? = null

        @SerializedName("Average_Farmers_Activities")
        @Expose
        var average_Farmers_Activities: String? = null
    }
}