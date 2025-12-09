package com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/7/2018.
 */

class EndTicketBean {

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
        @SerializedName("ticket_number")
        @Expose
        var ticket_number: String? = null
    }
}
